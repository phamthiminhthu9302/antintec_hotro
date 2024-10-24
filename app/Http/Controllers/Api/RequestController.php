<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Validation\Rule;
use App\Models\Request;
use DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RequestController extends Controller
{
    use HttpResponses;

    public function createRequest(HttpRequest $request): JsonResponse
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

    public function updateRequestStatus(HttpRequest $request): JsonResponse
    {
        $user = auth()->user();
        $validated = $request->validate([
            'request_id' => ['required', 'numeric', 'exists:requests,request_id'],
            'status' => ['required', 'string', Rule::in('pending', 'cancelled', 'completed', 'in_progress')]
        ],
            ['request_id.exists' => 'Invalid request id',
                'status.in' => 'Invalid status.  Valid status: [pending, cancelled, completed, in_progress]']);
        $userRequest = \App\Models\Request::where('request_id', $validated['request_id'])->with('service')->first();
        if (Auth::user()->user_id != $userRequest->technician_id) {
            return $this->fail("This request belongs to technician id " . $userRequest['technician_id']. ' while your id is ' . $user->user_id);
        }
        $userRequest->status = $validated['status'];
        $statusMessages = ['pending' => "đang chờ xác nhận",
            'in_progress' => "đang được thực hiện",
            'cancelled' => "đã bị hủy",
            'completed' => "đã hoàn thành"];
        $message = "Yêu cẩu " . $userRequest['service']['name'] . " của bạn " . $statusMessages[$validated['status']];
        Notification::create([
            'user_id' => $userRequest['customer_id'],
            'message' => $message,
            'is_read' => false
        ]);
        if($validated['status'] === 'completed') {
            $userRequest->completed_at = date("Y-m-d H:i:s");
        }
        $userRequest->save();
        return $this->success($message);
    }



    public function updateDescription(HttpRequest $request, $id): JsonResponse
    {
        $model = \App\Models\Request::find($id);
        $data = $model->description . "\n" . Carbon::now()->toDateTimeString() . ' (KTV): ' . $request->description;
        if (!$model) {
            return response()->json(['message' => 'No request found'], 404);
        }
        if ($model->status !== 'in_progress') {
            return response()->json(['message' => 'Request is no longer in progress']);
        }
        if ($model->technician_id != Auth::user()->user_id) {
            return response()->json(['message' => 'Not authorized'], 403);
        }
        if ($model->update(array('description' => $data))) {
            return response()->json(['message' => 'Description updated']);
        } else {
            return response()->json(['message' => 'failed']);
        }
    }

    public function findNearestTech($latitude, $longitude, $tech = [])
    {

        $distance = '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radian(longitude - radians(?)) +
        sin(radians(?)) * sin(radians(latitude)))) AS distance';

        $query = DB::table('locations')->select('technician_id, latitude, longitude, ' . $distance, [$latitude, $longitude, $latitude]);

        if (!empty($tech)) {
            $query->whereNotIn('technician_id', $tech);
        };

        return $query->orderBy('distance', 'ASC')->first();
    }

    public function create(HttpRequest $request)
    {

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

    public function statusRequest($request_id, $status)
    {

        $request = Request::find($request_id);

        if (!$request) {
            return $this->message("Don't find request");
        }

        $arrTech = [];

        if ($status == 'in_progress') {
            $request->update([
                'status' => 'in_progress'
            ]);

            return $this->message("Status update success");
        }

        if ($status == 'completed') {
            $request->update([
                'status' => 'completed'
            ]);

            $arrTech = [];

            return $this->message("Status update success");
        }

        if ($status == 'cancelled') {
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
