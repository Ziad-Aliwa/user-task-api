<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:pending,in-progress,completed',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create($validated);
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

    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->notFoundResponse('Task not found');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'sometimes|required|in:pending,in-progress,completed',
        ]);

        $task->update($validated);
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
