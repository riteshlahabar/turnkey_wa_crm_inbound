<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CallsController;
use App\Http\Controllers\Api\MobileCrmController;

/*
|--------------------------------------------------------------------------
| API ROUTES
|--------------------------------------------------------------------------
*/

// PUBLIC ROUTES (No Authentication)
Route::prefix('v1')->group(function () {

    // Login
    Route::post('/login', [AuthController::class, 'login']);

    // Register
    Route::post('/register', [AuthController::class, 'register']);

    // App settings for splash/login logo
    Route::get('/app-settings', [MobileCrmController::class, 'appSettings']);
});

// PROTECTED ROUTES (Require Authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Get Current User
    Route::get('/user', [AuthController::class, 'user']);

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Existing Calls API
   Route::get('/calls', [MobileCrmController::class, 'serverCalls']);
Route::get('/calls/{call}', [MobileCrmController::class, 'showCall']);
Route::post('/calls', [MobileCrmController::class, 'storeCall']);
Route::patch('/calls/{call}/status', [MobileCrmController::class, 'updateCallCourse']);
Route::delete('/calls/{call}', [MobileCrmController::class, 'destroyCall']);

Route::get('/statuses', [MobileCrmController::class, 'courses']);

Route::get('/courses', [MobileCrmController::class, 'courses']);
Route::get('/whatsapp-templates', [MobileCrmController::class, 'whatsappTemplates']);
Route::post('/whatsapp/log', [MobileCrmController::class, 'logWhatsapp']);
Route::get('/leads', [MobileCrmController::class, 'leads']);
Route::get('/followups', [MobileCrmController::class, 'followups']);
Route::patch('/followups/{followup}/complete', [MobileCrmController::class, 'completeFollowup']);
Route::get('/masters', [MobileCrmController::class, 'masters']);

Route::get('/dashboard-stats', [MobileCrmController::class, 'dashboardStats']);
});