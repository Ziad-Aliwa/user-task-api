<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    // inject the service
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $users = $this->userService->getAllUsers($perPage);

            return $this->successResponse($users, 'Users retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to retrieve users', 500);
        }
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->createUser($request->validated());

            return $this->successResponse($user, 'User created successfully', 201);
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to create user', 500);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userService->getUserById($id);

            if (!$user) {
                return $this->notFoundResponse('User not found');
            }

            return $this->successResponse($user, 'User retrieved successfully');
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to retrieve user', 500);
        }
    }
}
