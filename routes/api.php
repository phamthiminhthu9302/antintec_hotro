<?php

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

Route::post("check-login",[AuthController::class, "checkLogin"]);
Route::post("register",[AuthController::class, "register"]);

Route::group(['middleware' => ["auth:sanctum"]], function() {
    Route::get("profile", [ProfileController::class, "profile"]);
    Route::get("logout",[AuthController::class, "logout"]);
});
