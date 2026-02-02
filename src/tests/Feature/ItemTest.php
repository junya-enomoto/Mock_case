<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_see_all_items_on_index_page()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);
        foreach ($items as $item) {
            $response->assertSee($item->name); 
        }
    }

    public function test_sold_items_have_sold_label()
    {
        $soldItem = Item::factory()->create(['is_sold' => true]);

        $activeItem = Item::factory()->create(['is_sold' => false]);

        $response = $this->get('/');

        $response->assertStatus(200);
        
        $response->assertSee('SOLD'); 
    }

    public function test_own_items_are_not_shown_on_index_page()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $ownItem = Item::factory()->create(['user_id' => $user->id, 'name' => 'My Item']);
        
        $otherItem = Item::factory()->create(['name' => 'Other Item']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee($ownItem->name); 
        $response->assertSee($otherItem->name);  
    }

    public function test_can_search_items_by_name_partial_match()
    {
        Item::factory()->create(['name' => 'iPhone 13']);
        Item::factory()->create(['name' => 'iPhone 14 Pro']);
        Item::factory()->create(['name' => 'Galaxy S22']);

        $response = $this->get('/?keyword=iPhone');

        $response->assertStatus(200);
        $response->assertSee('iPhone 13');
        $response->assertSee('iPhone 14 Pro');
        $response->assertDontSee('Galaxy S22');
    }

    public function test_search_state_is_maintained_in_mylist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::factory()->create(['name' => 'Liked iPhone']);
        $user->likes()->create(['item_id' => $item->id]);

        $response = $this->get('/?keyword=iPhone&filter=mylist');

        $response->assertStatus(200);
        $response->assertSee('Liked iPhone');

        $response->assertSee('value="iPhone"', false); 
    }

    public function test_can_see_all_item_details()
    {
        $category = Category::factory()->create(['name' => 'Electronics']);
        $item = Item::factory()->create([
            'name' => 'Test Item',
            'brand_name' => 'Test Brand',
            'price' => 1000,
            'description' => 'This is a test description.',
            'condition' => '良好',
        ]);
        $item->categories()->attach($category); 

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee($item->name);
        $response->assertSee($item->brand_name);
        $response->assertSee(number_format($item->price));
        $response->assertSee($item->description);
        $response->assertSee($category->name); 
        $response->assertSee($item->condition);
        
        $response->assertSee($item->likes_count);
        $response->assertSee($item->comments->count());
    }

    public function test_multiple_categories_are_displayed()
    {
        $category1 = Category::factory()->create(['name' => 'Cat1']);
        $category2 = Category::factory()->create(['name' => 'Cat2']);
        
        $item = Item::factory()->create();
        $item->categories()->attach([$category1->id, $category2->id]);

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));

        $response->assertStatus(200);
        $response->assertSee('Cat1');
        $response->assertSee('Cat2');
    }
}
