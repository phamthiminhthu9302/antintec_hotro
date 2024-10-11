<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceTypes;
use Illuminate\Http\Request;

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
}
