<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create demo users
        $testUser = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
            'gender' => 'male',
            'address' => '123 Main Street, New York, NY 10001',
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('password123'),
            'gender' => 'female',
            'address' => '456 Oak Avenue, Los Angeles, CA 90001',
        ]);

        $user3 = User::create([
            'name' => 'Mike Johnson',
            'email' => 'mike@example.com',
            'password' => Hash::make('password123'),
            'gender' => 'male',
            'address' => '789 Pine Road, Chicago, IL 60601',
        ]);

        // Create sample tasks for test user
        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Complete project proposal',
            'description' => 'Finish writing the Q2 project proposal document with all requirements and timeline.',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => Carbon::now()->addDays(3),
        ]);

        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Review team feedback',
            'description' => 'Go through the feedback received from the team and create action items.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => Carbon::now()->addDays(5),
        ]);

        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Update documentation',
            'description' => 'Update the project documentation with latest API changes.',
            'priority' => 'low',
            'status' => 'completed',
            'due_date' => Carbon::now()->subDays(2),
        ]);

        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Meeting with stakeholders',
            'description' => 'Schedule and prepare for the quarterly meeting with stakeholders.',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => Carbon::now()->addDays(7),
        ]);

        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Setup development environment',
            'description' => 'Configure the new development machine with all required tools and dependencies.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => Carbon::now()->addDays(2),
        ]);

        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Code review for feature branch',
            'description' => 'Review the new feature implementation and provide feedback.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => Carbon::now()->addDay(),
        ]);

        Task::create([
            'user_id' => $testUser->id,
            'title' => 'Overdue task example',
            'description' => 'This is an example of an overdue task that was not completed on time.',
            'priority' => 'high',
            'status' => 'pending',
            'due_date' => Carbon::now()->subDays(5),
        ]);

        // Create sample tasks for other users
        Task::create([
            'user_id' => $user2->id,
            'title' => 'Marketing campaign planning',
            'description' => 'Plan the Q2 marketing campaign strategy and deliverables.',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => Carbon::now()->addDays(10),
        ]);

        Task::create([
            'user_id' => $user3->id,
            'title' => 'Backend optimization',
            'description' => 'Optimize database queries and improve API response times.',
            'priority' => 'medium',
            'status' => 'pending',
            'due_date' => Carbon::now()->addDays(8),
        ]);
    }
}
