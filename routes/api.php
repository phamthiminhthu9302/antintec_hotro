<?php

use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\RequestController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\ServicesController;
use App\Http\Controllers\api\ReviewController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post("check-login", [AuthController::class, "checkLogin"]);
Route::post("register", [AuthController::class, "register"]);


Route::group(['middleware' => ["auth:sanctum"]], function () {
    Route::put("profile", [ProfileController::class, "updateProfile"]);
    Route::get("profile/address", [ProfileController::class, "profileAddress"]);
    Route::put("profile/address", [ProfileController::class, "updateAddress"]);
    Route::delete("profile/address", [ProfileController::class, "deleteAddress"]);

    Route::put("profile/update-password", [ProfileController::class, "updatePassword"]);
    Route::put("profile/payment", [ProfileController::class, "updatePaymentMethod"]);

    Route::get("logout", [AuthController::class, "logout"]);
    Route::get("profile", [ProfileController::class, "profile"]);
    Route::post("profile/updateTech", [ProfileController::class, "updateInfoTech"]);

    Route::get('/messages', [ChatController::class, 'getMessagesByToken']);

    Route::post('/messages', [ChatController::class, 'sendMessage']);

    Route::put('/messages/seen', [ChatController::class, 'seenMessage']);

    //only admin can access messages by path variable
    Route::get('/messages/{id}', [ChatController::class, 'getMessagesBySenderId'])->middleware('restrictRole:admin');

    Route::put('/requests/status', [RequestController::class, 'updateRequestStatus']);

    Route::get('/location', [LocationController::class, 'getLocation']);
    Route::post('/location/add', [LocationController::class, 'createLocation']);
    Route::put('/location/update', [LocationController::class, 'updateLocation']);

    Route::post('services/search', [ServicesController::class, 'searchServices']);
    Route::post('services/store', [ServicesController::class, 'store']);
    Route::delete('services/delete/{i}', [ServicesController::class, 'delete']);

    Route::apiResource('reviews',ReviewController::class);
});