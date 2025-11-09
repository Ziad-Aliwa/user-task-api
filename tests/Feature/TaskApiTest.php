<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use PHPUnit\Framework\Test;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_creates_a_task_successfully()
    {
        $user = User::factory()->create();

        $data = [
            'title' => 'Test Task',
            'description' => 'This is a test task',
            'status' => 'pending',
            'user_id' => $user->id,
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertCreated()
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

    #[Test]
    public function test_fails_validation_when_creating_task()
    {
        $data = [
            'title' => '',
            'status' => 'invalid-status',
            'user_id' => 999,
        ];

        $response = $this->postJson('/api/v1/tasks', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'status', 'user_id']);
    }

    #[Test]
    public function test_returns_paginated_tasks_list_with_user_filter()
    {
        $user = User::factory()->create();
        Task::factory()->count(5)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/tasks?userId=' . $user->id . '&per_page=3');

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
    public function test_retrieves_a_single_task_successfully()
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/v1/tasks/{$task->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'message' => 'Task retrieved successfully',
                'data' => [
                    'id' => $task->id,
                    'title' => $task->title,
                ]
            ]);
    }

    #[Test]
    public function test_returns_not_found_when_task_does_not_exist()
    {
        $response = $this->getJson('/api/v1/tasks/99999');

        $response->assertNotFound()
            ->assertJson([
                'success' => false,
                'message' => 'Task not found'
            ]);
    }

    #[Test]
    public function test_updates_a_task_successfully()
    {
        $task = Task::factory()->create(['status' => 'pending']);

        $data = [
            'title' => 'Updated Task Title',
            'status' => 'in-progress',
        ];

        $response = $this->putJson("/api/v1/tasks/{$task->id}", $data);

        $response->assertOk()
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

    #[Test]
    public function test_deletes_a_task_successfully()
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
