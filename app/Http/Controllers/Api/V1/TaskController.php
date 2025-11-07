<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Models\Task;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->has('userId')) {
            $query->where('user_id', $request->userId);
        }

        $tasks = $query->get();
        return $this->successResponse($tasks, 'Tasks retrieved successfully');
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        return $this->successResponse($task, 'Task created successfully', 201);
    }

    public function show($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        return $this->successResponse($task, 'Task retrieved successfully');
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        $task->update($request->validated());
        return $this->successResponse($task, 'Task updated successfully');
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        $task->delete();
        return $this->successResponse(null, 'Task deleted successfully', 204);
    }
}
