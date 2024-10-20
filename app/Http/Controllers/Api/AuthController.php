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

    public function register(CreateUserRequest $request)
    {
        try {
            $request->validated($request->all());
            if ($request->password !== $request->password_confirmation) {
                return $this->fail("Password mismatch");
            }
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'address' => $request->address,
            ]);
            $token = $user->createToken('API Token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User create successfully',
                'token' => $token,
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
                    'username' => 'string|max:50',
                    'phone' => 'string|max:50',
                ]);
            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            $credentials = $request->only($request->has('email') ? 'email' : 'phone', 'password', 'password');

            $usernameCredentials = $request->only(['username', 'password']);


            if (Auth::attempt($credentials) || Auth::attempt($usernameCredentials)) {
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
