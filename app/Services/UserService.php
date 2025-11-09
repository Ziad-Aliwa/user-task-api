<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Throwable;

class UserService
{
    /**
     * Get paginated users.
     */
    public function getAllUsers($perPage = 10)
    {
        try {
            $users = User::paginate($perPage);

            Log::channel('api')->info('Users fetched successfully', [
                'per_page' => $perPage,
                'count' => $users->count(),
            ]);

            return $users;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error fetching users', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Store a new user.
     */
    public function createUser(array $data)
    {
        try {
            $user = User::create($data);

            Log::channel('api')->info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return $user;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error creating user', [
                'error' => $e->getMessage(),
                'input' => $data,
            ]);
            throw $e;
        }
    }

    /**
     * Get a single user by ID.
     */
    public function getUserById($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                Log::channel('api')->warning('User not found', ['user_id' => $id]);
                return null;
            }

            Log::channel('api')->info('User retrieved successfully', ['user_id' => $id]);
            return $user;
        } catch (Throwable $e) {
            Log::channel('api')->error('Error retrieving user', [
                'error' => $e->getMessage(),
                'user_id' => $id,
            ]);
            throw $e;
        }
    }
}
