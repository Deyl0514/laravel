<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(): View
    {
        $user = auth()->user();
        
        $totalUsers = User::count();
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();
        $pendingTasks = Task::where('status', 'pending')->count();

        // Tasks per status for the logged-in user
        $tasksByStatus = [
            'pending' => Task::where('user_id', $user->id)->where('status', 'pending')->count(),
            'in_progress' => Task::where('user_id', $user->id)->where('status', 'in_progress')->count(),
            'completed' => Task::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        // Tasks by priority for the logged-in user
        $tasksByPriority = [
            'low' => Task::where('user_id', $user->id)->where('priority', 'low')->count(),
            'medium' => Task::where('user_id', $user->id)->where('priority', 'medium')->count(),
            'high' => Task::where('user_id', $user->id)->where('priority', 'high')->count(),
        ];

        // Recent tasks
        $recentTasks = Task::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'tasksByStatus',
            'tasksByPriority',
            'recentTasks'
        ));
    }
}
