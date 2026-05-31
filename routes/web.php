<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

    // Tasks
    Route::get('/tasks/board', [TaskController::class, 'board'])->name('tasks.board');
    Route::get('/tasks/trashed', [TaskController::class, 'trashed'])->name('tasks.trashed');
    Route::patch('/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::post('/tasks/{id}/restore', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::delete('/tasks/{id}/force', [TaskController::class, 'forceDelete'])->name('tasks.forceDelete');
    Route::resource('tasks', TaskController::class)->only(['index', 'store', 'update', 'destroy']);

    // Categories
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});
