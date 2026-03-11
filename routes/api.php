<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShortUrlController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| Routes are grouped by visibility: public (no auth) and protected (auth:sanctum).
|
*/

// ─── Public Authentication Routes ─────────────────────────────────────────────

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ─── Protected Routes (Requires Sanctum Token) ────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);

    // User Profile
    Route::get('/user', [UserController::class, 'show']);
    Route::put('/user', [UserController::class, 'update']);
    Route::patch('/user', [UserController::class, 'update']);
    Route::delete('/user', [UserController::class, 'destroy']);

    // URL Management
    Route::apiResource('urls', ShortUrlController::class);
});
