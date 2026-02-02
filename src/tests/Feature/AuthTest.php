<?php

namespace Tests\Feature;

use App\Models\User; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker; 
use Tests\TestCase;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Mail; 
use Illuminate\Auth\Notifications\VerifyEmail; 

class AuthTest extends TestCase
{
    use RefreshDatabase; 

    public function test_register_validation_name_required()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['name' =>'お名前を入力してください']); 
        $response->assertRedirect('/');
    }

    public function test_register_validation_name_max_20_chars()
    {
        $response = $this->post('/register', [
            'name' => str_repeat('a', 21), 
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['name' =>'お名前は20文字以内で入力してください']);
        $response->assertRedirect('/');
    }

    public function test_register_validation_email_required()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' =>'メールアドレスを入力してください']); // 
        $response->assertRedirect('/');
    }

    public function test_register_validation_email_invalid_format()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' =>'有効なメールアドレス形式で入力してください']);
        $response->assertRedirect('/');
    }

    public function test_register_validation_email_unique()
    {
        User::factory()->create([
            'user_name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(), 
        ]);

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' =>'このメールアドレスは既に登録されています']);
        $response->assertRedirect('/');
    }

    public function test_register_validation_password_required()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['password' =>'パスワードを入力してください']); 
        $response->assertRedirect('/');
    }

    public function test_register_validation_password_min_8_chars()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'shortp7',
            'password_confirmation' => 'shortp7',
        ]);
        $response->assertSessionHasErrors(['password' =>'パスワードは8文字以上で入力してください']);
        $response->assertRedirect('/');
    }

    public function test_register_validation_password_confirmation_mismatch()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongpass',
        ]);
        $response->assertSessionHasErrors(['password' =>'パスワードと一致しません']);
        $response->assertRedirect('/');
    }

    /** @test */
    public function test_user_can_register_successfully_and_redirects_to_profile_settings()
    {
        Mail::fake();

        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'register@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        $this->assertDatabaseHas('users', ['email' =>'register@example.com']);
        $this->assertAuthenticated();

        $response->assertRedirect('/mypage/profile'); 
    }

    public function test_login_validation_email_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' =>'メールアドレスを入力してください']); // 
        $response->assertRedirect('/');
    }

    public function test_login_validation_password_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['password' =>'パスワードを入力してください']); // ★
        $response->assertRedirect('/');
    }

    public function test_login_validation_invalid_credentials()
    {
        $user = User::factory()->create([
            'user_name' => 'Correct User',
            'email' => 'correct@example.com',
            'password' => Hash::make('correctpass'),
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/login', [
            'email' => 'correct@example.com',
            'password' => 'wrongpass',
        ]);
        
        $response->assertSessionHasErrors(['email' => __('auth.failed')]); 
        $response->assertRedirect('/');
    }

    public function test_user_can_login_successfully()
    {
        $user = User::factory()->create([
            'user_name' => 'Login User',
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(), 
        ]);

        $response = $this->post('/login', [
            'email' => 'login@example.com',
            'password' => 'password123',
        ]);
        
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/'); 
    }

    public function test_user_can_logout_successfully()
    {
        $user = User::factory()->create(['user_name' => 'Logout User', 'email_verified_at' => now()]); 
        $this->actingAs($user); 
        $response = $this->post('/logout');
        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
