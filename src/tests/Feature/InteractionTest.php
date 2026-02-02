<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class InteractionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_like_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['likes_count' => 0]);
        $this->actingAs($user);

        $response = $this->post(route('like.store', ['item_id' => $item->id]));

        $response->assertRedirect(route('item.detail', ['item_id' => $item->id])); 
        
        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertEquals(1, $item->fresh()->likes_count);

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<button type="submit" class="like-btn liked">', false);
    }

    public function test_user_can_unlike_item()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['likes_count' => 1]);
        $this->actingAs($user);

        Like::create(['user_id' => $user->id, 'item_id' => $item->id]);

        $response = $this->delete(route('like.destroy', ['item_id' => $item->id]));

        $response->assertRedirect(route('item.detail', ['item_id' => $item->id]));
        
        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'item_id' => $item->id,
        ]);
        $this->assertEquals(0, $item->fresh()->likes_count);

        $response = $this->get(route('item.detail', ['item_id' => $item->id]));
        $response->assertStatus(200);
        $response->assertSee('<button type="submit" class="like-btn">', false);
    }

    public function test_logged_in_user_can_send_comment()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $this->actingAs($user);

        $commentText = 'This is a test comment.';

        $response = $this->post(route('comment.store', ['item_id' => $item->id]), [
            'content' => $commentText,
        ]);

        $response->assertRedirect(route('item.detail', ['item_id' => $item->id])); 
        
        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'comment' => $commentText,
        ]);
        $this->assertEquals(1, $item->comments()->count());
    }

    public function test_guest_user_cannot_send_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post(route('comment.store', ['item_id' => $item->id]), [
            'content' => 'Comment from guest',
        ]);

        $response->assertRedirect('/login'); 
        
        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_validation_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('comment.store', ['item_id' => $item->id]), [
            'content' => '',
        ]);

         $response->assertRedirect('/'); 
        $response->assertSessionHasErrors(['content' => 'コメントは入力必須です。']); 

    }

    public function test_comment_validation_max_255_chars()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        $this->actingAs($user);

        $longComment = str_repeat('a', 256);

        $response = $this->post(route('comment.store', ['item_id' => $item->id]), [
            'content' => $longComment,
        ]);

        $response->assertRedirect('/'); 
        $response->assertSessionHasErrors(['content' => 'コメントは255文字以内で入力してください。']); 

    }
}
