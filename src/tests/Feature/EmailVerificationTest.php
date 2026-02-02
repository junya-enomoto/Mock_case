<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL; 

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_email_is_sent_on_registration()
    {
        Mail::fake(); 
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Verify User',
            'email' => 'verify@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();

        $user = \App\Models\User::where('email', 'verify@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
        
        $response->assertRedirect('/mypage/profile');
        $this->get('/mypage/profile')->assertRedirect('/email/verify');
    }

    public function test_clicking_verify_here_button_redirects_to_mailhog()
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user);

        $response = $this->get('/email/verify');

        $response->assertStatus(200);

        $response->assertSee('href="http://localhost:8025"', false); 
        $response->assertSee('認証はこちらから'); 
    }

    public function test_email_verification_completes_and_redirects_to_profile_settings()
    {
        Notification::fake();

        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($verificationUrl);

        $this->assertNotNull($user->fresh()->email_verified_at);

        $response->assertRedirect('/mypage/profile?verified=1'); 
    }
}
