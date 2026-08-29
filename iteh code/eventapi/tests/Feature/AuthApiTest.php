<?php

namespace Tests\Feature;

use App\Mail\VerifyMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/register', [
            'firstName' => 'Luka',
            'lastName' => 'Test',
            'email' => 'luka@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated()
            ->assertJsonPath('user.email', 'luka@example.com')
            ->assertJsonPath('user.role', 'STUDENT');

        $this->assertDatabaseHas('users', [
            'email' => 'luka@example.com',
            'role' => 'STUDENT',
        ]);

        Mail::assertSent(VerifyMail::class);
    }

    public function test_register_requires_valid_payload(): void
    {
        Mail::fake();

        $this->postJson('/api/register', [
            'firstName' => 'Luka',
            'email' => 'nije-email',
            'password' => 'short',
        ])->assertStatus(422);
    }

    public function test_user_can_login_and_access_me(): void
    {
        $user = User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ]);

        $login->assertOk()
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonStructure(['token', 'user']);

        $token = $login->json('token');

        $this->withToken($token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'student@example.com');
    }

    public function test_login_rejects_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
        ]);

        $this->postJson('/api/login', [
            'email' => 'student@example.com',
            'password' => 'pogresna',
        ])->assertUnauthorized();
    }

    public function test_me_requires_authentication(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }

    public function test_user_can_logout(): void
    {
        User::factory()->create([
            'email' => 'student@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
        ]);

        $token = $this->postJson('/api/login', [
            'email' => 'student@example.com',
            'password' => 'password123',
        ])->json('token');

        $this->withToken($token)
            ->postJson('/api/logout')
            ->assertOk();
    }

    public function test_login_is_rate_limited_after_too_many_failures(): void
    {
        User::factory()->create([
            'email' => 'brute@example.com',
            'password' => bcrypt('password123'),
            'role' => 'STUDENT',
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'brute@example.com',
                'password' => 'pogresna',
            ])->assertUnauthorized();
        }

        $this->postJson('/api/login', [
            'email' => 'brute@example.com',
            'password' => 'pogresna',
        ])->assertStatus(429);
    }
}
