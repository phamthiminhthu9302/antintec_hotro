<?php

namespace App\Http\Controllers;

use App\Events\ServiceRequestCreated;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    public function createRequest(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|integer',
            'technician_id' => 'required|integer',
            'service_id' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|string',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'location' => 'required|string',
            'requested_at' => 'required|date',
        ]);

        \App\Models\Request::create([
            'customer_id' => $request->customer_id,
            'technician_id' => $request->technician_id,
            'service_id' => $request->service_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'photo' => $request->photo,
            'description' => $request->description,
            'status' => $request->status,
            'location' => $request->location,
            'requested_at' => $request->requested_at,
        ]);
        $s = Service::where('service_id', $request->service_id)->first();
        $status = '';
        if ($request->status == 'pending') {
            $status = 'đang chờ xử lý';
        }
        $notification_c = Notification::create([
            'user_id' => $request->customer_id,
            'message' => "Yêu cầu {$s->name} của bạn {$status}",
            'is_read' => false,
        ]);
        $notification_t = Notification::create([
            'user_id' => $request->technician_id,
            'message' => "Bạn có một yêu cầu {$s->name} mới",
            'is_read' => false,
        ]);
        event(new ServiceRequestCreated($notification_c,   $notification_t, Auth::user()));

        return ['status' => 'Yêu cầu dịch vụ của bạn đã thành công!'];
    }
    public function updateStatus($request_id, $status)
    {
        $request = \App\Models\Request::where('request_id', $request_id)->first();
        if ($request) {
            $request->status = $status;
            $request->save();
        }
        $s = Service::where('service_id', $request->service_id)->first();

        if ($status == 'in_progress') {
            $status = 'đang tiến hành';
        }
        if ($status == 'completed') {
            $status = ' đã hoàn thành';
        }
        if ($status == 'cancelled') {
            $status = 'đã hủy bỏ';
        }
        $notification_c = Notification::create([
            'user_id' => $request->customer_id,
            'message' => "Yêu cầu {$s->name} của bạn $status.",
            'is_read' => false,
        ]);

        $notification_t = Notification::create([
            'user_id' => $request->technician_id,
            'message' => "Bạn {$status} yêu cầu {$s->name}.",
            'is_read' => false,
        ]);
        event(new ServiceRequestCreated($notification_c, $notification_t, Auth::user()));

        return  ['status' => 'Trạng thái yêu cầu đã được cập nhật'];
    }
    public function markAsRead(Request $request)
    {

        $notification = Notification::where('notification_id', $request->notification_id)->first();

        if ($notification) {
            $notification->is_read = true;
            $notification->save();
        }

        return response()->json($notification);
    }

    //Xem lịch sử sử dụng dịch vụ
    public function index()
    {
        switch (Auth::user()->role) {
            case 'customer':
                $Requests = \App\Models\Request::where('customer_id', Auth::user()->user_id)->get();
                break;
            case 'technician':
                $Requests = \App\Models\Request::where('technician_id', Auth::user()->user_id)->get();
                break;
        }

        return view('requests.index', [
            'requests' => $Requests
        ]);
    }
    public function show(string $id)
    {
        $Requests = \App\Models\Request::find($id);
        return view('requests.show', [
            'requests' => $Requests
        ]);
    }
}
