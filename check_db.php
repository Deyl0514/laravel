<?php
// Diagnostic script to check database connectivity and data
define('LARAVEL_START', microtime(true));

try {
    require __DIR__ . '/vendor/autoload.php';
    $app = require_once __DIR__ . '/bootstrap/app.php';
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    // Check database connection
    $db = DB::connection();
    echo "✓ Database Connection: SUCCESS\n";
    echo "  - Host: " . config('database.connections.mysql.host') . "\n";
    echo "  - Database: " . config('database.connections.mysql.database') . "\n";
    
    // Count users
    $userCount = \App\Models\User::count();
    echo "\n✓ Users Table Access: SUCCESS\n";
    echo "  - Total Users: " . $userCount . "\n";
    
    if ($userCount > 0) {
        $users = \App\Models\User::all();
        echo "  - Users:\n";
        foreach ($users as $user) {
            echo "    • " . $user->name . " (" . $user->email . ")\n";
        }
    }
    
    // Count tasks
    $taskCount = \App\Models\Task::count();
    echo "\n✓ Tasks Table Access: SUCCESS\n";
    echo "  - Total Tasks: " . $taskCount . "\n";
    
    if ($taskCount > 0) {
        $tasks = \App\Models\Task::limit(3)->get();
        echo "  - Sample Tasks:\n";
        foreach ($tasks as $task) {
            echo "    • " . $task->title . " (User: " . $task->user->name . ")\n";
        }
    }
    
    // Check session table
    $sessionCount = DB::table('sessions')->count();
    echo "\n✓ Sessions Table Access: SUCCESS\n";
    echo "  - Total Sessions: " . $sessionCount . "\n";
    
    // Check tables exist
    $tables = DB::select("SHOW TABLES FROM " . config('database.connections.mysql.database'));
    echo "\n✓ Database Tables:\n";
    foreach ($tables as $table) {
        $tableName = (array)$table;
        $name = reset($tableName);
        echo "  - " . $name . "\n";
    }
    
    echo "\n✓ ALL CHECKS PASSED - Database is working correctly!\n";
    
} catch (\Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n";
    echo "  File: " . $e->getFile() . "\n";
    echo "  Line: " . $e->getLine() . "\n";
}
