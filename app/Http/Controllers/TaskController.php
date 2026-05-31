<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * Display all tasks for the logged-in user.
     */
    public function index(Request $request): View
    {
        $query = auth()->user()->tasks()->with('category');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $categories = auth()->user()->categories()->orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Display tasks in a Kanban board view.
     */
    public function board(): View
    {
        $tasks = auth()->user()->tasks()->with('category')->orderBy('updated_at', 'desc')->get();
        $categories = auth()->user()->categories()->orderBy('name')->get();

        $columns = [
            'pending' => $tasks->where('status', 'pending')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'completed' => $tasks->where('status', 'completed')->values(),
        ];

        return view('tasks.board', compact('columns', 'categories'));
    }

    /**
     * Store a new task.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date|after_or_equal:today',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $this->assertOwnedCategory($validated['category_id'] ?? null);

        if ($validated['status'] === 'completed') {
            $validated['completed_at'] = now();
        }

        $task = auth()->user()->tasks()->create($validated);
        $task->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully!',
            'task' => $task,
        ]);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $this->assertOwnedCategory($validated['category_id'] ?? null);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);
        $task->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully!',
            'task' => $task,
        ]);
    }

    /**
     * Quickly update only the status (used by Kanban + inline toggle).
     */
    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeTask($task);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $task->completed_at = now();
        } elseif ($validated['status'] !== 'completed') {
            $task->completed_at = null;
        }

        $task->status = $validated['status'];
        $task->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated!',
            'task' => $task,
        ]);
    }

    /**
     * Soft-delete the task (sent to trash).
     */
    public function destroy(Task $task)
    {
        $this->authorizeTask($task);

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task moved to trash!',
        ]);
    }

    /**
     * List soft-deleted tasks.
     */
    public function trashed(): View
    {
        $tasks = auth()->user()->tasks()->with('category')->onlyTrashed()
            ->orderBy('deleted_at', 'desc')->paginate(10);

        return view('tasks.trashed', compact('tasks'));
    }

    /**
     * Restore a soft-deleted task.
     */
    public function restore(int $id)
    {
        $task = auth()->user()->tasks()->onlyTrashed()->findOrFail($id);
        $task->restore();

        return response()->json([
            'success' => true,
            'message' => 'Task restored!',
        ]);
    }

    /**
     * Permanently delete a task.
     */
    public function forceDelete(int $id)
    {
        $task = auth()->user()->tasks()->onlyTrashed()->findOrFail($id);
        $task->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Task permanently deleted!',
        ]);
    }

    private function authorizeTask(Task $task): void
    {
        if ($task->user_id !== auth()->id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403));
        }
    }

    private function assertOwnedCategory(?int $categoryId): void
    {
        if ($categoryId === null) {
            return;
        }
        $owned = auth()->user()->categories()->whereKey($categoryId)->exists();
        if (! $owned) {
            abort(response()->json([
                'success' => false,
                'message' => 'Invalid category.',
            ], 422));
        }
    }
}
