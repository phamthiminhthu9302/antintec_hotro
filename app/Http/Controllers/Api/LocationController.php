<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Location;

class LocationController extends Controller
{
    use HttpResponses; 

    public function createLocation(Request $request){
        try {

            $useData = auth()->user();

            $validate = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',  
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            if ($validate->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validate->errors()
                ], 400);
            }

            Location::create([
                'technician_id' => $useData->user_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return $this->message("User's Location create successfully");

        }
        catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function updateLocation(Request $request){
        try {

            $useData = auth()->user();

            $location = Location::where('technician_id', $useData->user_id);

            $location->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);

            return $this->message("User's Location updates successfully");

        }
        catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getLocation(Request $request){
        try {

            $useData = auth()->user();

            $location = Location::where('technician_id', $useData->user_id)->get();

            return $this->success($location);

        }
        catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
