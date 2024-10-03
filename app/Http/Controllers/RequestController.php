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
        // Tạo thông báo 
        $notification_c = Notification::create([
            'user_id' => $request->customer_id, // ID của khách hàng
            'message' => "Yêu cầu {$s->name} của bạn đang {$request->status}",
            'is_read' => false,
        ]);
        $notification_t = Notification::create([
            'user_id' => $request->technician_id, // ID của kỹ thuật viên
            'message' => "Bạn có một yêu cầu {$s->name} mới",
            'is_read' => false,
        ]);
        event(new ServiceRequestCreated($notification_c,   $notification_t, Auth::user()));

        return ['status' => 'Yêu cầu dịch vụ của bạn đã thành công!'];
    }
    public function updateStatus($request_id, $status)
    {
        $request = \App\Models\Request::where('request_id', $request_id)->first(); // Lấy một đối tượng mô hình
        if ($request) {
            $request->status = $status;
            $request->save();
        }
        $s = Service::where('service_id', $request->service_id)->first();
        // Tạo thông báo 
        $notification_c = Notification::create([
            'user_id' => $request->customer_id, // ID của khách hàng
            'message' => "Trạng thái yêu cầu {$s->name} của bạn đã đổi thành: {$request->status}.",
            'is_read' => false,
        ]);

        $notification_t = Notification::create([
            'user_id' => $request->technician_id, // ID của kỹ thuật viên
            'message' => "Bạn đã {$request->status} yêu cầu {$s->name}.",
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
}
