<?php

use App\Constants\RouteConstants;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// v1 routes
Route::middleware('throttle:api')->group(function () {

    Route::group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers'], function () {
        
        // Rutas del webhook (sin autenticación)
        Route::get(RouteConstants::webhook, [WebhookController::class, 'verify']);
        Route::post(RouteConstants::webhook, [WebhookController::class, 'receive']);
        
        // Rutas protegidas
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get(RouteConstants::message, [MessageController::class, 'index']);
            Route::post(RouteConstants::message, [MessageController::class, 'store']);
        });
    });
});
