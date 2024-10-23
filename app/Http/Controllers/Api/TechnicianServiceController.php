<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TechnicianServiceController extends Controller
{
    use HttpResponses;

    public function handleServiceRequest(Request $request)
    {

    }

    public function getTechnicianServices(): JsonResponse
    {
        $technician = auth()->user();
        /*$validated = $request->validate([
            'technician_id' => 'required, exists:users,user_id'
        ]);*/
        //$result = TechnicianService::where('technician_id', $validated['technician_id'])->get();
        $result = TechnicianService::where('technician_id', $technician->user_id)->get();
        return $this->success($result);
    }
}
