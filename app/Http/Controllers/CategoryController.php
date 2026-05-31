<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = auth()->user()->categories()
            ->withCount('tasks')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $exists = auth()->user()->categories()->where('name', $validated['name'])->exists();
        if ($exists) {
            return response()->json([
                'success' => false,
                'errors' => ['name' => ['You already have a category with that name.']],
            ], 422);
        }

        $category = auth()->user()->categories()->create([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? '#3b82f6',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created!',
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name' => 'required|string|max:60',
            'color' => 'nullable|string|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $duplicate = auth()->user()->categories()
            ->where('name', $validated['name'])
            ->where('id', '!=', $category->id)
            ->exists();
        if ($duplicate) {
            return response()->json([
                'success' => false,
                'errors' => ['name' => ['You already have a category with that name.']],
            ], 422);
        }

        $category->update([
            'name' => $validated['name'],
            'color' => $validated['color'] ?? $category->color,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated!',
            'category' => $category,
        ]);
    }

    public function destroy(Category $category)
    {
        $this->authorizeCategory($category);
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted!',
        ]);
    }

    private function authorizeCategory(Category $category): void
    {
        if ($category->user_id !== auth()->id()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403));
        }
    }
}
