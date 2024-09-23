<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class ProfileController extends Controller
{
    public function profile(){
        $useData = auth()->user();

        return response()->json([
            'status' => true,
            'message' => "Profile Information",
            'data' => $useData,
        ], 200);
    } 
}
