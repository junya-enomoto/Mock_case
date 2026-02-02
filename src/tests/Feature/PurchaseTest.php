<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Mockery;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    protected function tearDown(): void
    {
        Mockery::close(); 
        parent::tearDown();
    }

    public function test_user_can_access_purchase_screen()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->get(route('purchase.index', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee(number_format($item->price));
    }

    public function test_seller_cannot_purchase_own_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->get(route('purchase.index', ['item_id' => $item->id]));

        $response->assertRedirect(route('item.detail', ['item_id' => $item->id]));
        $response->assertSessionHas('error', 'ご自身の出品物は購入できません。');
    }

    public function test_cannot_purchase_sold_out_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['is_sold' => true, 'user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->get(route('purchase.index', ['item_id' => $item->id]));

        $response->assertRedirect(route('item.detail', ['item_id' => $item->id]));
        $response->assertSessionHas('error', 'この商品は売り切れです。');
    }

    public function test_purchase_button_redirects_to_stripe_checkout()
    {
        $stripeSession = Mockery::mock('alias:\Stripe\Checkout\Session');
        $stripeSession->shouldReceive('create')->andReturn((object)['url' => 'https://checkout.stripe.com/test_session_id']);

        $user = User::factory()->create([
            'email_verified_at' => now(),
            'postal_code' => '123-4567',
            'street_address' => 'Test Address'
        ]);

        $item = Item::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->post(route('purchase.process', ['item_id' => $item->id]), [
            'payment_method' => 'card',
        ]);

        $response->assertRedirect('https://checkout.stripe.com/test_session_id');
    }

    public function test_item_is_marked_sold_and_order_created_after_successful_stripe_payment()
    {
        $stripeSessionMock = Mockery::mock('alias:\Stripe\Checkout\Session');
        $stripeSessionMock->shouldReceive('retrieve')->andReturn((object)[
            'metadata' => (object)['payment_method' => 'card']
        ]);

        $user = User::factory()->create(['email_verified_at' => now(), 'postal_code' => '123-4567', 'street_address' => 'Test Address']);
        $item = Item::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->get(route('purchase.success', ['item_id' => $item->id, 'session_id' => 'cs_test_123']));
        
        $response->assertRedirect(route('mypage'));
        $response->assertSessionHas('success', '商品を購入しました！');

        $this->assertTrue($item->fresh()->is_sold == 1); 
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'card',
            'status' => 'completed',
            'price' => $item->price,
        ]);
    }

    public function test_purchase_validation_payment_method_required()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'postal_code' => '123-4567', 'street_address' => 'Test Address']);
        $item = Item::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->post(route('purchase.process', ['item_id' => $item->id]), [
            'payment_method' => '', 
        ]);

        $response->assertSessionHasErrors(['payment_method' => '支払い方法を選択してください。']);
        $response->assertRedirect('/');
    }

    public function test_purchase_validation_address_registered_required()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'postal_code' => null, 'street_address' => null]);
        $item = Item::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->post(route('purchase.process', ['item_id' => $item->id]), [
            'payment_method' => 'card',
        ]);
        
        $response->assertSessionHasErrors(['address_registered' => '配送先情報が不足しています。プロフィールから住所を登録してください。']);
        $response->assertRedirect('/');
    }
    
    public function test_address_can_be_updated_and_reflected_in_purchase_screen()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'postal_code' => '000-0000', 'street_address' => 'Old Address']);
        $item = Item::factory()->create(['user_id' => User::factory()->create()->id]);
        $this->actingAs($user);

        $response = $this->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '123-4567',
            'address' => '新しい住所',
            'building_name' => '新ビルディング',
        ]);
        
        $response->assertRedirect(route('purchase.index', ['item_id' => $item->id]));
        $response->assertSessionHas('success', '配送先住所を更新しました。');

        $response = $this->get(route('purchase.index', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('〒 123-4567');
        $response->assertSee('新しい住所');
        $response->assertSee('新ビルディング');
    }
}
