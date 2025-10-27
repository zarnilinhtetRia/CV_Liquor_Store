<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Version1\LoginAPIController;
use App\Http\Controllers\API\Version1\LocationAPIController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/users', [LoginAPIController::class, 'index']);

//user_login
Route::post('/user_register', [LoginAPIController::class, 'user_register']);
Route::post('/user_login', [LoginAPIController::class, 'user_login']);
Route::put('/user_update/{id}', [LoginAPIController::class, 'update']);
Route::delete('/user_destroy/{id}', [LoginAPIController::class, 'destroy']);
Route::get('/user_show/{id}', [LoginAPIController::class, 'show']);

//location_management
Route::get('/location', [LocationAPIController::class, 'index']);
Route::post('/location_register', [LocationAPIController::class, 'store']);
Route::put('/location_update/{id}', [LocationAPIController::class, 'update']);
Route::delete('/location_destroy/{id}', [LocationAPIController::class, 'destroy']);
Route::get('/location_show/{id}', [LocationAPIController::class, 'show']);
