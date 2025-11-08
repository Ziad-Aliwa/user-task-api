<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a user successfully
     */
    public function test_create_user_successfully()
    {
        $data = [
            'username' => 'Test User',
            'email' => 'testuser@example.com'
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(201)
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

    /**
     * Test validation errors when creating a user
     */
    public function test_create_user_validation_error()
    {
        $data = [
            'username' => '', // missing username
            'email' => 'not-an-email' // invalid email
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(422) // Laravel returns 422 for validation errors
            ->assertJsonValidationErrors(['username', 'email']);
    }

    /**
     * Test retrieving a list of users with pagination
     */
    public function test_get_users_list()
    {
        User::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/users?per_page=3');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data', // list of users
                    'first_page_url',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);

        $this->assertCount(3, $response->json('data.data')); // per_page=3
    }

    /**
     * Test retrieving a single user
     */
    public function test_get_single_user_success()
    {
        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
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

    /**
     * Test retrieving a non-existing user
     */
    public function test_get_single_user_not_found()
    {
        $response = $this->getJson("/api/v1/users/99999");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'User not found'
            ]);
    }
}
