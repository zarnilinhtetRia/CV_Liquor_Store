<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\Version1\LoginAPIController;
use App\Http\Controllers\API\Version1\OrderAPIController;
use App\Http\Controllers\API\Version1\LocationAPIController;
use App\Http\Controllers\API\Version1\SenderCustomerAPIController;
use App\Http\Controllers\API\Version1\ReceiverCustomerAPIController;
use App\Http\Controllers\API\Version1\TrackHistoryAPIController;
use App\Models\TrackHistory;

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
Route::post('user_logout', [LoginAPIController::class, 'logout']);
Route::post('change_password/{id}', [LoginAPIController::class, 'change_password']);


//location_management
Route::get('/location', [LocationAPIController::class, 'index']);
Route::post('/location_register', [LocationAPIController::class, 'store']);
Route::put('/location_update/{id}', [LocationAPIController::class, 'update']);
Route::delete('/location_destroy/{id}', [LocationAPIController::class, 'destroy']);
Route::get('/location_show/{id}', [LocationAPIController::class, 'show']);

//sender_customer_management
Route::get('/sender_customers', [SenderCustomerAPIController::class, 'index']);
Route::post('/sender_customer_register', [SenderCustomerAPIController::class, 'store']);
Route::put('/sender_customer_update/{id}', [SenderCustomerAPIController::class, 'update']);
Route::delete('/sender_customer_destroy/{id}', [SenderCustomerAPIController::class, 'destroy']);
Route::get('/sender_customer_show/{id}', [SenderCustomerAPIController::class, 'show']);

//receiver_customer_management
Route::get('/receiver_customers', [ReceiverCustomerAPIController::class, 'index']);
Route::post('/receiver_customer_register', [ReceiverCustomerAPIController::class, 'store']);
Route::put('/receiver_customer_update/{id}', [ReceiverCustomerAPIController::class, 'update']);
Route::delete('/receiver_customer_destroy/{id}', [ReceiverCustomerAPIController::class, 'destroy']);
Route::get('/receiver_customer_show/{id}', [ReceiverCustomerAPIController::class, 'show']);


//order_management
Route::get('/orders', [OrderAPIController::class, 'index']);
Route::post('/order_register', [OrderAPIController::class, 'store']);
Route::put('/order_update/{id}', [OrderAPIController::class, 'update']);
Route::delete('/order_destroy/{id}', [OrderAPIController::class, 'destroy']);
Route::get('/order_show/{id}', [OrderAPIController::class, 'show']);
Route::post('/change_order_status/{id}', [OrderAPIController::class, 'change_status']);

//Track History
Route::get('/track_histories', [TrackHistoryAPIController::class, 'index']);
Route::post('/track_history_register', [TrackHistoryAPIController::class, 'store']);
Route::put('/track_history_update/{id}', [TrackHistoryAPIController::class, 'update']);
Route::delete('/track_history_destroy/{id}', [TrackHistoryAPIController::class, 'destroy']);
Route::get('/track_history_show/{id}', [TrackHistoryAPIController::class, 'show']);
