<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){

        $validateUser = Validator::make($request->all(),
        [
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8',
            'email' => 'required|email|max:100|unique:users',
            'phone' => 'required',
            'role' => 'required|in:customer,technician',
        ]);

        if($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validate error',
                'errors' => $validateUser->errors(),
            ], 401);
        }

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'email' => $request->email,
            'phone' => $request->phone, 
            'role' => $request->role,
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'User create successfully',
            'token' => $token,
        ], 200);
    }
}
