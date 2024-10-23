<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Validation\Rule;
use App\Models\Request;
use DB;

class RequestController extends Controller
{
    use HttpResponses;

    public function updateRequestStatus(HttpRequest $request): JsonResponse
    {
        $validated = $request->validate([
            'request_id' => ['required', 'numeric', 'exists:requests,request_id'],
            'status' => ['required', 'string', Rule::in('pending', 'cancelled', 'completed', 'in_progress')]
        ],
            ['request_id.exists' => 'Invalid request id',
                'status.in' => 'valid status:pending, cancelled, completed, in_progress']);

        $updated = \App\Models\Request::where('request_id', $validated['request_id'])->update(['status' => $validated['status']]);
        return $this->success([$updated]);

    }

    public function findNearestTech($latitude, $longitude, $tech = []){

        $distance = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radian(longitude - radians(?)) + 
        sin(radians(?)) * sin(radians(latitude)))) AS distance';

        $query = DB::table('locations')->select('technician_id, latitude, longitude, '. $distance,[$latitude, $longitude, $latitude]);

        if(!empty($tech)){
            $query->whereNotIn('technician_id', $tech);
        };

        return $query->orderBy('distance', 'ASC')->first();
    }

    public function create(HttpRequest $request){
        
        $request->validate([
            'service_id' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'required',
            'description' => 'required|string|max:255',
            'location' => 'required|string|max:255'
        ]);

        $useData = auth()->user();

        $customer_id = $useData->user_id;

        $tech = $this->findNearestTech($request->latitude, $request->longitude);

        $technician_id = $tech->technician_id;

        $result = Request::create([
            'customer_id' => $customer_id,
            'technician_id' => $technician_id,  
            'service_id' => $request->service_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $request->photo,
            'description' => $request->description,
            'status' => 'pending',   
            'location' => $request->location,
        ]);

        return $this->success($result);
    }

    public function statusRequest($request_id, $status){

        $request = Request::find($request_id);

        if(!$request){
            return $this->message("Don't find request");
        }

        $arrTech = [];

        if($status == 'in_progress'){
            $request->update([
                'status' => 'in_progress'
            ]);

            return $this->message("Status update success");
        }

        if($status == 'completed'){
            $request->update([
                'status' => 'completed'
            ]);

            $arrTech = [];

            return $this->message("Status update success");
        }

        if($status == 'cancelled'){
            $arrTech[] = $request->technician_id;
            $technician = $this->findNearestTech($request->latitude, $request->longitude, $arrTech);

            $request->update([
                'technician_id' => $technician->technician_id,
                'status' => 'pending'
            ]);

            return $this->success($request);
        }
    }
}
