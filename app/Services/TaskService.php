<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaskService
{
    /**
     * Get paginated list of tasks (with optional userId filter)
     */
    public function getAllTasks($userId = null, $perPage = 10)
    {
        try {
            $query = Task::query();

            if ($userId) {
                $query->where('user_id', $userId);
            }

            $tasks = $query->paginate($perPage);

            Log::channel('api')->info('Tasks fetched successfully', [
                'userId' => $userId,
                'count' => $tasks->count(),
            ]);

            return $tasks;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error fetching tasks', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Create a new task
     */
    public function createTask(array $data)
    {
        try {
            $task = Task::create($data);

            Log::channel('api')->info('Task created successfully', [
                'task_id' => $task->id,
                'user_id' => $task->user_id,
            ]);

            return $task;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error creating task', [
                'error' => $e->getMessage(),
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Get task by ID
     */
    public function getTaskById($id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                Log::channel('api')->warning('Task not found', ['task_id' => $id]);
                return null;
            }

            Log::channel('api')->info('Task retrieved successfully', ['task_id' => $id]);
            return $task;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error retrieving task', [
                'error' => $e->getMessage(),
                'task_id' => $id,
            ]);
            throw $e;
        }
    }

    /**
     * Update task by ID
     */
    public function updateTask($id, array $data)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                Log::channel('api')->warning('Task not found for update', ['task_id' => $id]);
                return null;
            }

            $task->update($data);

            Log::channel('api')->info('Task updated successfully', ['task_id' => $id]);
            return $task;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error updating task', [
                'error' => $e->getMessage(),
                'task_id' => $id,
                'data' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Delete task by ID
     */
    public function deleteTask($id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                Log::channel('api')->warning('Task not found for deletion', ['task_id' => $id]);
                return null;
            }

            $task->delete();

            Log::channel('api')->info('Task deleted successfully', ['task_id' => $id]);
            return true;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error deleting task', [
                'error' => $e->getMessage(),
                'task_id' => $id,
            ]);
            throw $e;
        }
    }
}
