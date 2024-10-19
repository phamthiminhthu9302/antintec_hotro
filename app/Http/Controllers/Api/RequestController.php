<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


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
    public function updateDescription(Request $request,$id):JsonResponse{
        $model = \App\Models\Request::find($id);
        $data = $model->description."\n".Carbon::now()->toDateTimeString().' (KTV): '.$request->description;
        if(!$model)
        {
            return response()->json(['message' => 'No request found' ],404);
        }
        if($model->status !== 'in_progress'){
            return response()->json(['message' => 'Request is no longer in progress' ]);
        }
        if($model->technician_id != Auth::user()->user_id){
            return response()->json(['message' => 'Not authorized' ],403);
        }
        if($model->update(array('description'=>$data))){
            return response()->json(['message' => 'Description updated' ]);
        }
        else {
            return response()->json(['message' => 'failed' ]);
        }
    }
}
