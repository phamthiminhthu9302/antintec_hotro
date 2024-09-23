<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AuthController extends Controller
{
    public function register(Request $request){

        try{
            $validateUser = Validator::make($request->all(),
            [
                'username' => 'required|string|max:50|unique:users',
                'password' => 'required|string|min:8',
                'email' => 'required|email|max:100|unique:users',
                'phone' => 'required',
                'role' => 'required|in:customer,technician',
            ]);

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
        catch(\Throwable $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkLogin(Request $request){
        try{
            $validateUser = Validator::make($request->all(),
            [
                'email' => 'required|email|max:100',
                'password' => 'required|string|min:8',
            ]);

            
            if($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validate error',
                    'errors' => $validateUser->errors(),
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email or password does not match our record',
                ], 401);
            }

            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('API Token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'User login successfully',
                    'token' => $token,
                ], 200);
            }

        }
        catch(\Throwable $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
        
    }

    public function logout(){
        auth()->user()->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => "User logout successfully",
            'data' => [],
        ], 200);
    }
}
