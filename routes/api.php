<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LockerController;

Route::post('/locker-status', [LockerController::class, 'updateStatus']);
Route::get('/ping', function () { return 'pong'; });