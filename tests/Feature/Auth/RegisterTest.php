<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'test12345',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['token', 'user' => ['id', 'name', 'email']],
            ])
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', ['email' => 'test@gmail.com']);
    }

    public function test_register_requires_name_email_password(): void
    {
        $this->postJson('/api/auth/register', [])
            ->assertUnprocessable()
            ->assertJsonPath('success', false);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        $data = [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'test12345',
        ];

        $this->postJson('/api/auth/register', $data)->assertCreated();
        $this->postJson('/api/auth/register', $data)->assertUnprocessable();
    }
}
