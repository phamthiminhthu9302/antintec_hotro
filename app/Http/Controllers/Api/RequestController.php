<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class RequestController extends Controller
{
    use HttpResponses;

    public function createRequest(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'technician_id' => 'required|integer',
            'service_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in('pending', 'in_progress', 'completed', 'cancelled')],
            'location' => 'required|string',
            'requested_at' => 'required|date',
        ], ['status.in' => 'Invalid status. Valid status: [pending, in_progress, completed,cancelled]']);
        \App\Models\Request::create([
            'customer_id' => $user['user_id'],
            'technician_id' => $validated['technician_id'],
            'service_id' => $validated['service_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'photo' => $validated['photo'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'location' => $validated['location'],
            'requested_at' => $validated['requested_at'],
        ]);
        $service = Service::where('service_id', $validated['service_id'])->first();
        $statusMessages = ['pending' => "đang chờ xác nhận",
            'in_progress' => "đang được thực hiện",
            'cancelled' => "đã bị hủy",
            'completed' => "đã hoàn thành"];
        $message = "Yêu cẩu " . $service['name'] . " của bạn " . $statusMessages[$validated['status']];
        Notification::create([
            'user_id' => $user['user_id'],
            'message' => $message,
            'is_read' => false
        ]);
        return $this->success("$message");
    }

    public function updateRequestStatus(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'request_id' => ['required', 'numeric', 'exists:requests,request_id'],
            'status' => ['required', 'string', Rule::in('pending', 'cancelled', 'completed', 'in_progress')]
        ],
            ['request_id.exists' => 'Invalid request id',
                'status.in' => 'Invalid status.  Valid status: [pending, cancelled, completed, in_progress]']);
        $userRequest = \App\Models\Request::where('request_id', $validated['request_id'])->with('service')->first();
        $userRequest->status = $validated['status'];
        $statusMessages = ['pending' => "đang chờ xác nhận",
            'in_progress' => "đang được thực hiện",
            'cancelled' => "đã bị hủy",
            'completed' => "đã hoàn thành"];
        $message = "Yêu cẩu " . $userRequest['service']['name'] . " của bạn " . $statusMessages[$validated['status']];
        Notification::create([
            'user_id' => $user['user_id'],
            'message' => $message,
            'is_read' => false
        ]);
        $userRequest->save();
        return $this->success($userRequest);
    }

    public function updateReadNotification(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            "notification_id" => ['required', 'numeric'],
        ]);

        $notification = Notification::where('notification_id', $validated['notification_id'])->first();
        if ($notification['user_id'] === $user['user_id']) {
            $notification->is_read = true;
            $notification->save();
            return $this->success($notification);
        }
        return $this->fail("Invalid user");
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
