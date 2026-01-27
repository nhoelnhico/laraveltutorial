<?php

use App\Http\Controllers\ProfileController; // Add this at the top
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Protect all these routes with 'auth' middleware
Route::middleware('auth')->group(function () {
    // The Dashboard & Tasks
    Route::get('/dashboard', [TaskController::class, 'index'])->name('dashboard');
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    // THE MISSING PROFILE ROUTES (Add these!)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::patch('/tasks/{task}', [TaskController::class, 'update']);

require __DIR__.'/auth.php';
