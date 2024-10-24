<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceHistoryController extends Controller
{
    use HttpResponses;

    public function showRequestHistoryByToken() : JsonResponse
    {
        $user = auth()->user();
        try {
            $role = $user['role'] . '_id';
            return $this->success($this->serviceHistory($role, $user['user_id']));
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }

    public function showRequestHistoryByAdmin(Request $request, $userId) : JsonResponse
    {
        $isAdmin = $request->user()->tokenCan('admin');
        if ($isAdmin) {
            try {
                $user = $request->user();
                $role = $user['role'] . '_id';
                return $this->success($this->serviceHistory($role, $userId));
            } catch (\Exception $exception) {
                return $this->fail($exception->getMessage());
            }
        }
        return $this->fail("Unauthorized", 401);
    }

    private function serviceHistory($role, $userId)
    {
        //FROM REQUEST CALL TO SERVICE, FROM SERVICE CALL TO SERVICE_TYPE
        return \App\Models\Request::where($role, $userId)
            ->orderByDesc('created_at')
            //->with('service.serviceType')
                ->with('service')
            ->get();

    }

}
