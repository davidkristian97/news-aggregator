<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])  
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token', 'user' => ['id', 'name', 'email']],
            ])
            ->assertJsonPath('success', true);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ])  
            ->assertUnauthorized()
            ->assertJsonPath('success', false);
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/api/auth/login', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false);
    }
}
