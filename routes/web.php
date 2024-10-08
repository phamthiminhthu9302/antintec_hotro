<?php

use App\Events\MessageSent;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\BillingInfoController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\RequestController;
use App\Models\BillingInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use App\Models\TechnicianDetail;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::group(['middleware' => 'auth'], function () {

	Route::get('/', [HomeController::class, 'home']);
	Route::get('/dashboard',[DashboardUserController::class,'getServiceTypes'])->name('dashboard');
	Route::get('/getServices', [DashboardUserController::class,'getAllServices']);

	Route::post('/billing', [BillingInfoController::class, 'insertUpdate']);
	Route::get('/billing', [BillingInfoController::class, 'index']);
	Route::patch('/billing', [BillingInfoController::class, 'update']);
	Route::delete('/billing/{id}', [BillingInfoController::class, 'destroy']);

	//profile
	Route::get('user-profile', function () {
		return view('profile');
	})->name('user-profile');

	Route::get('rtl', function () {
		return view('rtl');
	})->name('rtl');

	Route::get('user-management', function () {
		return view('laravel-examples/user-management');
	})->name('user-management');

	Route::get('tables', function () {
		return view('tables');
	})->name('tables');

	Route::get('virtual-reality', function () {
		return view('virtual-reality');
	})->name('virtual-reality');

	Route::get('static-sign-in', function () {
		return view('static-sign-in');
	})->name('sign-in');

	Route::get('static-sign-up', function () {
		return view('static-sign-up');
	})->name('sign-up');

	Route::get('/logout', [SessionsController::class, 'destroy']);
	Route::get('/user-profile/update', [InfoUserController::class, 'create']);
	Route::post('/user-profile/update', [InfoUserController::class, 'store']);
	Route::get('/user-profile/location', [InfoUserController::class, 'location']);
	Route::post('/location/add', [InfoUserController::class, 'AddLocation']);
	Route::get('/login', function () {
		return view('dashboard');
	})->name('sign-up');

	Route::get('/chat', function () {
		return view('chat');
	});
	// Route xử lý gửi tin nhắn
	Route:
	Route::match(['get', 'post'], '/dashboard/send/{request_id}/{receiver_id}/{message}', [ChatController::class, 'sendMessage'])->name('chat.send');
	Route::get('/dashboard/get/{request_id}/{receiver_id}', [ChatController::class, 'getMessages'])->name('chat.messages');
	Route::match(['get', 'post'], '/dashboard/seen/{messageIds}', [ChatController::class, 'markAsSeen'])->name('markAsSeen');
	Route::get('/dashboard/update/{request_id}/{status}', [RequestController::class, 'updateStatus']);
	Route::get('/dashboard/read/{notification_id}', [RequestController::class, 'markAsRead']);
	Route::get('/dashboard/usercurrent', [ChatController::class, 'getUserCurrent']);
});

Route::get('/requests',[RequestController::class,'index']);
Route::get('/requests/{id}',[RequestController::class,'show']);


Route::group(['middleware' => 'guest'], function () {
	Route::get('/register', [RegisterController::class, 'create']);
	Route::post('/register', [RegisterController::class, 'store']);
	Route::get('/login', [SessionsController::class, 'create']);
	Route::post('/session', [SessionsController::class, 'store']);
	Route::get('/login/forgot-password', [ResetController::class, 'create']);
	Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
	Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
	Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', function () {
	return view('session/login-session');
})->name('login');
