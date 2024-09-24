<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TechnicianDetail;
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

    public function updateInfoTech(Request $request){

        $useData = auth()->user();

        if($useData->role == "technician"){

            $techDetail = TechnicianDetail::firstOrNew(['technician_id' => $useData->user_id]);

            $techDetail->skills = $request->skills;
            $techDetail->certifications = $request->certifications;
            $techDetail->work_area = $request->work_area;

            $techDetail->save();

            return response()->json([
                'status' => true,
                'message' => 'Information Tech updated successfully.'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'error' => 'User is not a technician.'
        ], 403);
    }
}
