<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesTransactionController;
use App\Http\Controllers\SalesDetailController;
use App\Http\Controllers\PurchaseTransactionController;
use App\Http\Controllers\PurchaseDetailController;
use App\Http\Controllers\ActivityLogController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResource('users', UserController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('customers', CustomerController::class);
Route::apiResource('sales-transactions', SalesTransactionController::class);
Route::apiResource('sales-details', SalesDetailController::class);
Route::apiResource('purchase-transactions', PurchaseTransactionController::class);
Route::apiResource('purchase-details', PurchaseDetailController::class);
Route::apiResource('activity-logs', ActivityLogController::class);
