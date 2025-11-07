<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiResponseTrait;

    public function index()
    {
        $users = User::all();
        return $this->successResponse($users, 'Users retrieved successfully');
    }

    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return $this->successResponse($user, 'User created successfully', 201);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse($user, 'User retrieved successfully');
    }
}
