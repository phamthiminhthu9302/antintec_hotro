<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TechnicianService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TechnicianServiceController extends Controller
{
    use HttpResponses;

    public function handleServiceRequest(Request $request)
    {

    }

    public function getTechnicianServices(): JsonResponse
    {
        $technician = auth()->user();
        $result = TechnicianService::where('technician_id', $technician->user_id)->get();
        return $this->success($result);
    }

    public function createTechnicianService(Request $request)
    {
        $technician = auth()->user();
        $validated = $request->validate([
            'service_id' => ['required', 'numeric', 'exists:services,service_id'],
            'status' => ['required', Rule::in('active', 'inactive')],
            'available_from' => ['required', 'date'],
            'available_to' => ['required', 'date'],
        ]);

        try {
            $result = TechnicianService::create([
                'technician_id' => $technician->user_id,
                'service_id' => $validated['service_id'],
                'status' => $validated['status'],
                'available_from' => $validated['available_from'],
                'available_to' => $validated['available_to'],
            ]);
            return $this->success($result);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
    }

    public function updateTechnicianService(Request $request): JsonResponse
    {
        $technician = auth()->user();
        $validated = $request->validate([
            'service_id' => ['required', 'numeric', 'exists:services,service_id'],
            'status' => ['required', Rule::in('active', 'inactive')],
            'available_from' => ['date'],
            'available_to' => ['date'],
        ]);
        try {
            $service = TechnicianService::where([
                ['technician_id', '=', $technician->user_id],
                ['service_id', '=', $validated['service_id']],
            ])->first();
            $sql = "update `technician_service` set ";

            foreach ($validated as $key => $value) {
                // Do something with $key and $value
                if ($value != null) {
                    $service[$key] = $value;
                    $sql .= "`" . $key . "` = '" . $value . "', ";
                }
            }
            $sql = substr_replace($sql, "", -2);
            $sql .= " where `technician_id` = " . $service->technician_id . " and `service_id` = " . $service->service_id;
            DB::update($sql);
            return $this->success($service);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
    }

    public function getAllAvailableTechniciansByServiceId(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'service_id' => ['required', 'numeric', 'exists:services,service_id']
        ]);
        try {
            //TODO query by status -> active or inactive and today < available_to
            $result = TechnicianService::where([
                ['service_id', '=', $validated['service_id']],
                ['status', '=', 'active'],
                ['available_to', '<', date('Y-m-d H:i:s')],
            ])->get();
            return $this->success($result);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }

    }

}
