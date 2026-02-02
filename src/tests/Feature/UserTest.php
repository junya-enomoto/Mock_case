<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_see_profile_page_with_correct_info()
    {
        $user = User::factory()->create([
            'user_name' => 'Test User',
            'user_image' => 'storage/profile_images/test.jpg',
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $soldItem = Item::factory()->create(['user_id' => $user->id, 'name' => 'Sold Item']);
        
        $boughtItem = Item::factory()->create(['name' => 'Bought Item']);
        Order::create([
            'user_id' => $user->id,
            'item_id' => $boughtItem->id,
            'payment_method' => 'card',
            'postal_code' => '123-4567',
            'street_address' => 'Test Address',
            'price' => $boughtItem->price,
        ]);

        $response = $this->get(route('mypage'));

        $response->assertStatus(200);
        $response->assertSee($user->user_name);

        $response->assertSee('profile_images/test.jpg'); 
        
        $response->assertSee($soldItem->name);

        $response = $this->get(route('mypage', ['type' => 'buy']));
        $response->assertSee($boughtItem->name);
    }

    public function test_profile_edit_page_shows_initial_values()
    {
        $user = User::factory()->create([
            'user_name' => 'Old Name',
            'postal_code' => '123-4567',
            'street_address' => 'Old Address',
            'email_verified_at' => now(),
        ]);
        $this->actingAs($user);

        $response = $this->get(route('mypage.edit'));

        $response->assertStatus(200);

        $response->assertSee('value="Old Name"', false);
        $response->assertSee('value="123-4567"', false);
        $response->assertSee('value="Old Address"', false);
    }

    public function test_user_can_update_profile()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);

        Storage::fake('public');
        $file = UploadedFile::fake()->create('new_profile.jpg', 100, 'image/jpeg');

        $response = $this->post(route('mypage.update'), [
            'user_name' => 'New Name',
            'postal_code' => '987-6543',
            'street_address' => 'New Address',
            'building_name' => 'New Building',
            'user_image' => $file,
        ]);

        $response->assertRedirect(route('mypage'));
        $response->assertSessionHas('message', 'プロフィールを更新しました');

        $user->refresh();
        $this->assertEquals('New Name', $user->user_name);
        $this->assertEquals('987-6543', $user->postal_code);
        $this->assertNotNull($user->user_image);
    }

    public function test_user_can_create_new_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $this->actingAs($user);
        
        $category = \App\Models\Category::factory()->create();

        Storage::fake('public');
        $file = UploadedFile::fake()->create('item.jpg', 100, 'image/jpeg');

        $response = $this->post(route('item.store'), [
            'name' => 'New Item',
            'description' => 'Item Description',
            'category_ids' => [$category->id],
            'condition' => '良好',
            'price' => 5000,
            'item_image' => $file,
            'brand_name' => 'New Brand',
        ]);

        $response->assertRedirect(route('item.index'));
        $response->assertSessionHas('success', '商品が出品されました！');

        $this->assertDatabaseHas('items', [
            'name' => 'New Item',
            'price' => 5000,
            'condition' => '良好',
            'user_id' => $user->id,
        ]);
        
        $item = Item::where('name', 'New Item')->first();
        $this->assertDatabaseHas('item_categories', [
            'item_id' => $item->id,
            'category_id' => $category->id,
        ]);
    }
}

