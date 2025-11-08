<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $users = User::paginate($perPage);

            Log::info('Users fetched successfully', [
                'per_page' => $perPage,
                'count' => $users->count(),
            ]);

            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (Throwable $e) {
            Log::error('Error fetching users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('Failed to retrieve users', 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = User::create($request->validated());

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $this->successResponse($user, 'User created successfully', 201);
        } catch (Throwable $e) {
            Log::error('Error creating user', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);
            return $this->errorResponse('Failed to create user', 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                Log::warning('User not found', ['user_id' => $id]);
                return $this->notFoundResponse('User not found');
            }

            Log::info('User retrieved successfully', ['user_id' => $id]);
            return $this->successResponse($user, 'User retrieved successfully');
        } catch (Throwable $e) {
            Log::error('Error retrieving user', [
                'error' => $e->getMessage(),
                'user_id' => $id,
            ]);
            return $this->errorResponse('Failed to retrieve user', 500);
        }
    }
}
