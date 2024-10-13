<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceTypes;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\TechnicianLocationUpdated;

class DashboardUserController extends Controller
{
    public function getAllServices()
    {
        $services = Service::all()->toArray();

        return response()->json($services);
    }

    public function getServiceTypes()
    {
        $serviceTypes = ServiceTypes::all();
        $servicePrices = Service::select('price')->distinct()->orderBy('price')->get();

        return view('dashboard')->with([
            'serviceTypes' => $serviceTypes,
            'servicePrices' => $servicePrices,
        ]);
    }

    public function saveLocation(Request $request)
    {
        $user = User::find($request->id);

        if($user && $user->role === 'technician'){
            $location = Location::updateOrCreate(
                ['technician_id' => $request->id], 
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]
            );

            broadcast(new TechnicianLocationUpdated($request->id, $request->latitude, $request->longitude))->toOthers();

            return response()->json(['message' => 'Vị trí đã được lưu vào database!']);
        }else{
            return response()->json(['message' => 'Chỉ cập nhật tọa độ với kỹ thuật viên!']);
        }
    }
}
