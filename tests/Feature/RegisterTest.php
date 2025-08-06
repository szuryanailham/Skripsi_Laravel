<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_register_successfully(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Ilham Suryana',
            'email' => 'ilham@example.com',
            'password' => 'secret123',
            'role' => 'user',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'role',
                'user' => ['id', 'name', 'email', 'role', 'created_at', 'updated_at']
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ilham@example.com',
        ]);
    }

    #[Test]
    public function registration_fails_if_validation_fails(): void
    {
        $response = $this->postJson('/api/register', [
            'email' => 'invalid-email',
            'password' => '123',
            'role' => 'invalid_role'
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }

    #[Test]
    public function registration_fails_if_email_already_exists(): void
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Another User',
            'email' => 'duplicate@example.com',
            'password' => 'secret123',
            'role' => 'user',
        ]);

        $response->assertStatus(422)
                 ->assertJsonStructure(['message', 'errors']);
    }
}
