<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class TaskController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $query = Task::query();

            if ($request->has('userId')) {
                $query->where('user_id', $request->userId);
            }

            $perPage = $request->query('per_page', 10);
            $tasks = $query->paginate($perPage);

            Log::channel('api')->info('Tasks fetched successfully', [
                'userId' => $request->query('userId'),
                'count' => $tasks->count(),
            ]);

            return $this->successResponse($tasks, 'Tasks retrieved successfully');
        } catch (Throwable $e) {
            Log::channel('api')->error('Error fetching tasks', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to retrieve tasks', 500);
        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $task = Task::create($request->validated());

            Log::channel('api')->info('Task created successfully', [
                'task_id' => $task->id,
                'user_id' => $task->user_id,
            ]);

            return $this->successResponse($task, 'Task created successfully', 201);
        } catch (Throwable $e) {
            Log::channel('api')->error('Error creating task', [
                'error' => $e->getMessage(),
                'data' => $request->all(),
            ]);
            return $this->errorResponse('Failed to create task', 500);
        }
    }

    public function show($id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                Log::channel('api')->warning('Task not found', ['task_id' => $id]);
                return $this->notFoundResponse('Task not found');
            }

            Log::channel('api')->info('Task retrieved successfully', ['task_id' => $id]);
            return $this->successResponse($task, 'Task retrieved successfully');
        } catch (Throwable $e) {
            Log::channel('api')->error('Error retrieving task', ['error' => $e->getMessage(), 'task_id' => $id]);
            return $this->errorResponse('Failed to retrieve task', 500);
        }
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                Log::channel('api')->warning('Task not found for update', ['task_id' => $id]);
                return $this->notFoundResponse('Task not found');
            }

            $task->update($request->validated());

            Log::channel('api')->info('Task updated successfully', ['task_id' => $id]);
            return $this->successResponse($task, 'Task updated successfully');
        } catch (Throwable $e) {
            Log::channel('api')->error('Error updating task', [
                'error' => $e->getMessage(),
                'task_id' => $id,
                'data' => $request->all(),
            ]);
            return $this->errorResponse('Failed to update task', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::find($id);
            if (!$task) {
                Log::channel('api')->warning('Task not found for deletion', ['task_id' => $id]);
                return $this->notFoundResponse('Task not found');
            }

            $task->delete();

            Log::channel('api')->info('Task deleted successfully', ['task_id' => $id]);
            return $this->successResponse(null, 'Task deleted successfully', 204);
        } catch (Throwable $e) {
            Log::channel('api')->error('Error deleting task', ['error' => $e->getMessage(), 'task_id' => $id]);
            return $this->errorResponse('Failed to delete task', 500);
        }
    }
}
