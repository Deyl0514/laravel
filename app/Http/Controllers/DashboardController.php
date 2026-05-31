<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $totalUsers = User::count();

        $myTasksQuery = Task::where('user_id', $user->id);

        $totalTasks = (clone $myTasksQuery)->count();
        $completedTasks = (clone $myTasksQuery)->where('status', 'completed')->count();
        $pendingTasks = (clone $myTasksQuery)->where('status', 'pending')->count();
        $overdueTasks = (clone $myTasksQuery)
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();

        $completionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

        $tasksByStatus = [
            'pending' => (clone $myTasksQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $myTasksQuery)->where('status', 'in_progress')->count(),
            'completed' => $completedTasks,
        ];

        $tasksByPriority = [
            'low' => (clone $myTasksQuery)->where('priority', 'low')->count(),
            'medium' => (clone $myTasksQuery)->where('priority', 'medium')->count(),
            'high' => (clone $myTasksQuery)->where('priority', 'high')->count(),
        ];

        $recentTasks = Task::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalUsers',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'overdueTasks',
            'completionRate',
            'tasksByStatus',
            'tasksByPriority',
            'recentTasks'
        ));
    }
}
