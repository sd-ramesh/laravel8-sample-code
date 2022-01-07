<?php


use Illuminate\Support\Facades\Route;
use App\Services\Cognito\CognitoClient;
use Aws\CognitoIdentityProvider\Exception\CognitoIdentityProviderException;

use Illuminate\Http\Request;
use App\Http\Controllers\V1\Api\{AuthController, AddressController, QrCodeController, OrderController, ConsoleController, CardController, TopupController, WalletController, MessageController, SubscriptionController};

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
Route::middleware('XssSanitizer')->group(function () { 

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/auth'], function ($router) {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/sociallogin', [AuthController::class, 'socialLogin']);
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/miniregister', [AuthController::class, 'miniRegister']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
        Route::get('/user-profile', [AuthController::class, 'userProfile']);  
        Route::post('/change-password', [AuthController::class, 'updatePassword']);    

        Route::post('/forgot/password', [AuthController::class, 'passwordResetLink']); 
        Route::post('/verify/email', [AuthController::class, 'verifyEmail']);    
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);    
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);    
        Route::post('/update-password', [AuthController::class, 'updateNewPassword']); 
        Route::post('/store/deviceid', [AuthController::class, 'storeDeviceId']);    
        Route::get('/states', [AuthController::class, 'getStates']);    

    });
    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/address'], function ($router) {
        Route::post('/update', [AddressController::class, 'update']); 
        Route::post('/logo-upload', [AddressController::class, 'upload']); 
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/qr'], function ($router) {
        Route::get('/get', [QrCodeController::class, 'get']);  
        Route::get('/getdetail', [QrCodeController::class, 'getQrDetail']);  
        Route::post('/skip', [QrCodeController::class, 'skip']);   
        Route::post('/assign', [QrCodeController::class, 'assign']);  
        Route::post('/status', [QrCodeController::class, 'checkQR']);   
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/order'], function ($router) {
        Route::get('/get', [OrderController::class, 'get']); 
        Route::get('/test', [OrderController::class, 'test']); 
        Route::post('/update/status', [OrderController::class, 'updateStatus']); 
        Route::post('/get-detail', [OrderController::class, 'getOrderDetail']);  
        Route::post('/send-notification', [OrderController::class, 'SendLoopNotification']);  
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/console'], function ($router) {
        Route::get('/get-screen', [ConsoleController::class, 'getScreen']);  
        Route::post('/update/screen', [ConsoleController::class, 'updateScreen']);  
        Route::get('/get-notification', [ConsoleController::class, 'getNotification']);  
        Route::post('/update/notification', [ConsoleController::class, 'updateNotification']); 
        Route::get('/get/statics', [ConsoleController::class, 'getStatics']);  
        Route::get('/get/prevstatics', [ConsoleController::class, 'getPrevStatics']);  
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/card'], function ($router) {
        Route::get('/settings', [CardController::class, 'getVendorDetails']); 
        Route::post('/updatesettings', [CardController::class, 'updateVendorDetails']); 
        Route::post('/store', [CardController::class, 'store']); 
        Route::post('/default', [CardController::class, 'setDefault']);  
        Route::get('/show', [CardController::class, 'show']);  
        Route::get('/showall', [CardController::class, 'showAll']);  
        Route::post('/update', [CardController::class, 'update']);   
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/payment'], function ($router) {
        Route::post('/pay', [TopupController::class, 'processPayment']);     
        Route::get('/return', [TopupController::class, 'returnUrl'])->name('returnurl');     
        Route::get('/success', [TopupController::class, 'success'])->name('success');     
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/wallet'], function ($router) {
        Route::get('/show', [WalletController::class, 'show']);     
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/message'], function ($router) {
        Route::get('/show', [MessageController::class, 'show']);    
        Route::post('/update', [MessageController::class, 'update']);    
        Route::post('/delete', [MessageController::class, 'destroy']);     
        Route::get('/get/customer', [MessageController::class, 'getCustomers']);     
        Route::post('/sendpush', [MessageController::class, 'sendPushNotifications']); 
        Route::post('/sendsms', [MessageController::class, 'sendBulkMessage']);     
        Route::post('/exportemails', [MessageController::class, 'exportEmails']);     
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/invoice'], function ($router) {
        // Route::post('/create', [SubscriptionController::class, 'create']);
        Route::get('/list', [SubscriptionController::class, 'invoice']); 
        Route::get('/download', [SubscriptionController::class, 'download']); 
        Route::get('/pdfstatement', [SubscriptionController::class, 'pdfstatement']);  
    });

    Route::group([ 'middleware' => 'api', 'prefix' => 'api/v1/subscription'], function ($router) {
        Route::post('/create', [SubscriptionController::class, 'create']); 
    });

});

/**
 * User authentication / session routes
 */ 
/** User routes */
Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    require __DIR__ . '/app/user/index.php';
}); 

/** Webhooks routes */
Route::group(['prefix' => 'webhooks', 'as' => 'webhooks.'], function () {

    require __DIR__ . '/app/stripe/webhook.php';

});
