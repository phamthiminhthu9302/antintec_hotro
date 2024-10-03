<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RequestController extends Controller
{
    use HttpResponses;

    public function updateRequestStatus(Request $request): JsonResponse
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
}
