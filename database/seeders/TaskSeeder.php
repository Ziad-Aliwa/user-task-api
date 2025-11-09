<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->warn('No users found, please run UserSeeder first.');
            return;
        }

        foreach ($users as $user) {
            Task::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}

