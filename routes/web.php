<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatBotController;

// Main Dashboard Routes
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index']);

// Feedback Page Route
Route::get('/feedback', [DashboardController::class, 'feedback'])->name('feedback');

// ChatBot Routes
Route::get('/chatbot', [ChatBotController::class, 'index'])->name('chatbot');
Route::post('/chatbot/message', [ChatBotController::class, 'message'])->name('chatbot.message');

// Flash Routes
Route::get('/flash', [App\Http\Controllers\DashboardController::class, 'flash'])->name('dashboard.flash');