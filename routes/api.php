<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;

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

    Route::get("logout",[AuthController::class, "logout"]);
    Route::get("profile", [ProfileController::class, "profile"]);
    Route::post("profile/updateTech", [ProfileController::class, "updateInfoTech"]);
    
});
