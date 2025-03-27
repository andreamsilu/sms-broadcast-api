<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\SMSStatusController;
use App\Http\Controllers\SMSDeliveryController;
use App\Http\Controllers\SMSBillingController;
use App\Http\Controllers\AzamPesaController;
use App\Http\Controllers\PaymentDashboardController;




// Auth Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// User Routes (Requires Authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Dashboard Routes
    Route::get('/dashboard/broadcasts', [DashboardController::class, 'getBroadcasts']);
    Route::get('/dashboard/broadcasts/{id}', [DashboardController::class, 'getBroadcastDetails']);
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);

    // File Upload Routes
    Route::post('/upload-contacts', [FileUploadController::class, 'uploadContacts']);

    // SMS Status Route
    Route::get('/sms-status', [SMSStatusController::class, 'checkStatus']);


    Route::get('/sms-packages', [SMSBillingController::class, 'getPackages']);
    Route::post('/purchase-package', [SMSBillingController::class, 'purchasePackage']);
    Route::get('/sms-balance', [SMSBillingController::class, 'getUserBalance']);


    Route::post('/azam-pesa/pay', [AzamPesaController::class, 'initiatePayment']);
    Route::post('/azam-pesa/callback', [AzamPesaController::class, 'paymentCallback']);



    Route::get('/payments/history', [PaymentDashboardController::class, 'getPaymentHistory']);
    Route::get('/payments/{id}', [PaymentDashboardController::class, 'getPaymentDetails']);
    Route::get('/payments/stats', [PaymentDashboardController::class, 'getPaymentStats']);
    Route::get('/payments/sms-balance', [PaymentDashboardController::class, 'getSMSBalance']);

});

// SMS Delivery Report Webhook (Does not require Authentication)
Route::post('/sms-delivery-report', [SMSDeliveryController::class, 'updateDeliveryStatus']);

