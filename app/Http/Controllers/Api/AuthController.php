<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    use HttpResponses;

    public function register(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(), [
                'username' => 'required|string|max:50|unique:users',
                'password' => 'required|string|min:8|confirmed', 
                'password_confirmation' => 'required|string|min:8',
                'email' => 'required|email|max:100|unique:users',
                'phone' => [
                    'required',
                    'string',
                    'regex:/^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/',
                    'unique:users',
                ],
                'role' => 'required|in:customer,technician',
                'address' => 'required|string'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'address' => $request->address,
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'User create successfully',
                'user' => $user,
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function username(): string
    {
        $login = request()->input('username');

        if (is_numeric($login)) {
            $field = 'phone';
        } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
        } else {
            $field = 'username';
        }

        request()->merge([$field => $login]);
        return $field;
    }

    public function checkLogin(Request $request)
    {
        try {
            $validateUser = Validator::make($request->all(),
                [
                    'email' => 'email|max:100',
                    'password' => 'required|string|min:8',
                    'phone' => 'string|max:50',
                ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            if ($request->has('email')) {
                $credentials = $request->only('email', 'password');
            } elseif ($request->has('phone')) {
                $credentials = $request->only('phone', 'password');
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or phone is required',
                ], 401);
    }
            if (Auth::attempt($credentials)) {
                session()->regenerate();
                $user = Auth::user();
                $token = $user->createToken('API Token', ['role:USER'])->plainTextToken;
                return response()->json([
                    'status' => true,
                    'message' => 'User login successfully',
                    'token' => $token,
                ], 200);
            }
            return response()->json([
                'status' => false,
                'message' => 'Email or password does not match our record',
            ], 401);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        auth()->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => true,
            'message' => "User logout successfully",
            'data' => [],
        ], 200);

    }
}
