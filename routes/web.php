<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\CustomerPickupController;
use App\Http\Controllers\Admin\LockerController;
use App\Http\Controllers\Admin\AssignmentController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('lockers', LockerController::class);
    Route::resource('assignments', AssignmentController::class);
    Route::patch('assignments/{assignment}/mark-picked-up', [AssignmentController::class, 'markPickedUp'])->name('assignments.markPickedUp');
Route::post('/lockers/{locker}/unlock', [LockerController::class, 'unlock'])->name('admin.lockers.unlock');
    });

    Route::post('/lockers/{locker}/unlock', [LockerController::class, 'unlock'])->name('admin.lockers.unlock');

// Main Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Feedback Page Route
Route::get('/feedback', [DashboardController::class, 'feedback'])->name('feedback');

// Customer Pickup Routes
Route::get('/pickup', [CustomerPickupController::class, 'showForm'])->name('pickup.form');
Route::post('/pickup', [CustomerPickupController::class, 'process'])->name('pickup.process');
Route::get('/pickup/success', [CustomerPickupController::class, 'success'])->name('pickup.success');

// ChatBot Routes
Route::get('/chatbot', [ChatBotController::class, 'index'])->name('chatbot');
Route::post('/chatbot/message', [ChatBotController::class, 'message'])->name('chatbot.message');

