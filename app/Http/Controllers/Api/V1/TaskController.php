<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Services\TaskService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class TaskController extends Controller
{
    use ApiResponseTrait;

    protected $taskService;

    // inject the TaskService
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index(Request $request)
    {
        try {
            $tasks = $this->taskService->getAllTasks(
                $request->query('userId'),
                $request->query('per_page', 10)
            );

            return $this->successResponse($tasks, 'Tasks retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to retrieve tasks', 500);
        }
    }

    public function store(StoreTaskRequest $request)
    {
        try {
            $task = $this->taskService->createTask($request->validated());
            return $this->successResponse($task, 'Task created successfully', 201);
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to create task', 500);
        }
    }

    public function show($id)
    {
        try {
            $task = $this->taskService->getTaskById($id);
            if (!$task) {
                return $this->notFoundResponse('Task not found');
            }

            return $this->successResponse($task, 'Task retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to retrieve task', 500);
        }
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        try {
            $task = $this->taskService->updateTask($id, $request->validated());
            if (!$task) {
                return $this->notFoundResponse('Task not found');
            }

            return $this->successResponse($task, 'Task updated successfully');
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to update task', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = $this->taskService->deleteTask($id);
            if (!$deleted) {
                return $this->notFoundResponse('Task not found');
            }

            return $this->successResponse(null, 'Task deleted successfully', 204);
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to delete task', 500);
        }
    }
}
