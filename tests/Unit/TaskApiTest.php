<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a task successfully
     */
    public function test_create_task_successfully()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'user_id' => $user->id,
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Task created successfully',
                'data' => [
                    'title' => 'Test Task',
                    'status' => 'pending',
                    'user_id' => $user->id,
                ]
            ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    /**
     * Test validation errors when creating a task
     */
    public function test_create_task_validation_error()
    {
        $data = [
            'title' => '', // missing title
            'status' => 'invalid-status', // invalid enum
            'user_id' => 999, // non-existent user
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'user_id']);
    }

    /**
     * Test retrieving all tasks with optional filtering by user
     */
    public function test_get_tasks_list()
    {
        $user = User::factory()->create();
        Task::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/tasks?userId=' . $user->id . '&per_page=3');

        $response->assertStatus(200)
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

        $this->assertCount(3, $response->json('data.data')); // per_page=3
    }

    /**
     * Test retrieving a single task
     */
    public function test_get_single_task_success()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task retrieved successfully',
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ]
            ]);
    }

    /**
     * Test retrieving a non-existing task
     */
    public function test_get_single_task_not_found()
    {
        $response = $this->getJson("/api/v1/tasks/99999");

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'Task not found'
            ]);
    }

    /**
     * Test updating a task
     */
    public function test_update_task_success()
    {
        $task = Task::factory()->create([
            'status' => 'pending'
        ]);

        $data = [
            'title' => 'Updated Task Title',
            'status' => 'in-progress',
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Task updated successfully',
                'data' => [
                    'title' => 'Updated Task Title',
                    'status' => 'in-progress',
                ]
            ]);

        $this->assertDatabaseHas('tasks', ['title' => 'Updated Task Title', 'status' => 'in-progress']);
    }

    /**
     * Test deleting a task
     */
    public function test_delete_task_success()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
