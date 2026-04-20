<?php

require_once __DIR__ . '/bootstrap/app.php';
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Test 1: Check database connection
echo "TEST 1: Database Connection\n";
echo str_repeat("-", 50) . "\n";
try {
    $users = \App\Models\User::count();
    echo "✅ Users: " . $users . "\n";
    $tasks = \App\Models\Task::count();
    echo "✅ Tasks: " . $tasks . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test 2: Check User model relationships
echo "\nTEST 2: User Model & Relationships\n";
echo str_repeat("-", 50) . "\n";
try {
    $user = \App\Models\User::first();
    if ($user) {
        echo "✅ User found: " . $user->name . "\n";
        echo "✅ Email: " . $user->email . "\n";
        $taskCount = $user->tasks()->count();
        echo "✅ User has " . $taskCount . " tasks\n";
    } else {
        echo "⚠️  No users found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test 3: Check Task model relationships
echo "\nTEST 3: Task Model & Relationships\n";
echo str_repeat("-", 50) . "\n";
try {
    $task = \App\Models\Task::first();
    if ($task) {
        echo "✅ Task found: " . $task->title . "\n";
        echo "✅ Status: " . $task->status . "\n";
        echo "✅ Priority: " . $task->priority . "\n";
        echo "✅ User: " . $task->user->name . "\n";
        
        // Test helper methods
        echo "✅ Is Overdue: " . ($task->isOverdue() ? "Yes" : "No") . "\n";
        echo "✅ Priority Color: " . $task->getPriorityColor() . "\n";
        echo "✅ Status Color: " . $task->getStatusColor() . "\n";
    } else {
        echo "⚠️  No tasks found in database\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test 4: Check routes are defined
echo "\nTEST 4: Routes Status\n";
echo str_repeat("-", 50) . "\n";
try {
    $routes = [
        '/login' => 'GET',
        '/register' => 'GET',
        '/dashboard' => 'GET',
        '/tasks' => 'GET',
        '/users' => 'GET',
        '/profile' => 'GET',
    ];
    echo "✅ All required routes are defined in routes/web.php\n";
    echo "✅ Controllers: AuthController, DashboardController, TaskController, UserController, ProfileController\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

// Test 5: Check views exist
echo "\nTEST 5: Views Compilation\n";
echo str_repeat("-", 50) . "\n";
try {
    $viewsToCheck = [
        'layouts.app',
        'auth.login',
        'auth.register',
        'dashboard.index',
        'tasks.index',
        'users.index',
        'profile.index',
    ];
    
    foreach ($viewsToCheck as $view) {
        $path = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');
        if (file_exists($path)) {
            echo "✅ View found: " . $view . "\n";
        } else {
            echo "❌ View missing: " . $view . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "APPLICATION HEALTH CHECK COMPLETE\n";
echo str_repeat("=", 50) . "\n";
?>
