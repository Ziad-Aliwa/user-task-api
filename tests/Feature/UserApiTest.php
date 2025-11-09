<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Test;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_creates_a_user_successfully()
    {
        $data = [
            'username' => 'Test User',
            'email' => 'testuser@example.com'
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertCreated()
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully',
                'data' => [
                    'username' => 'Test User',
                    'email' => 'testuser@example.com'
                ]
            ]);

        $this->assertDatabaseHas('users', ['email' => 'testuser@example.com']);
    }

    #[Test]
    public function test_fails_validation_when_creating_user()
    {
        $data = [
            'username' => '',
            'email' => 'invalid-email'
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'email']);
    }

    #[Test]
    public function test_returns_paginated_users_list()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/users?per_page=3');

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'first_page_url',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);

        $this->assertCount(3, $response->json('data.data'));
    }

    #[Test]
    public function test_retrieves_a_single_user_successfully()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'User retrieved successfully',
                'data' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                ]
            ]);
    }

    #[Test]
    public function test_returns_not_found_when_user_does_not_exist()
    {
        $response = $this->getJson('/api/v1/users/99999');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'User not found'
            ]);
    }
}
