<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

use App\Http\Controllers\DashboardController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/broadcasts', [DashboardController::class, 'getBroadcasts']);
    Route::get('/dashboard/broadcasts/{id}', [DashboardController::class, 'getBroadcastDetails']);
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
});
use App\Http\Controllers\FileUploadController;

Route::post('/upload-contacts', [FileUploadController::class, 'uploadContacts'])->middleware('auth:sanctum');

//updating status
use App\Http\Controllers\SMSStatusController;

Route::get('/sms-status', [SMSStatusController::class, 'checkStatus'])->middleware('auth:sanctum');
//webhook to receive deliverly reports
use App\Http\Controllers\SMSDeliveryController;

Route::post('/sms-delivery-report', [SMSDeliveryController::class, 'updateDeliveryStatus']);




