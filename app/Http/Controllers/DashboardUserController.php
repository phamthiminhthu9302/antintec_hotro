<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceTypes;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\TechnicianLocationUpdated;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class DashboardUserController extends Controller
{
    public function getAllServices()
    {
        $services = Service::all();
        return view('dashboard')->with(['services' => $services]);
    }

    public function filterServices(Request $request)
    {
        try {
            // dd($request);
            $userId = (int) $request->input('userId');
            $role = $request->input('role');
            $serviceId = (int) $request->input('service_id');
            $customerLat = (float) $request->input('latitude');
            $customerLon = (float) $request->input('longitude');

            if ($userId && $role === 'customer') {
                $customerLat = (float) $request->input('latitude');
                $customerLon = (float) $request->input('longitude');
            }

            // if (is_null($customerLat) || is_null($customerLon) || !$serviceId) {
            //     return response()->json(['error' => 'Thiếu thông tin hoặc dữ liệu không hợp lệ'], 400);
            // }

            $technicians = DB::table('users as t')
                ->join('technician_service as ts', 't.user_id', '=', 'ts.technician_id')
                ->join('services as s', 'ts.service_id', '=', 's.service_id')
                ->join('locations as l', 't.user_id', '=', 'l.technician_id')
                ->join('technician_availability as ta', 't.user_id', '=', 'ta.technician_id')
                ->select(
                    't.user_id as technician_id',
                    't.username as technician_name',
                    'ts.service_id',
                    's.name',
                    's.price',
                    's.description',
                    'l.latitude',
                    'l.longitude',
                    'l.updated_at',
                    'ta.available_from',
                    'ta.available_to',
                    'ta.day_of_week'
                )
                ->where('ts.service_id', $serviceId)
                ->where('l.updated_at', '>=', Carbon::now()->subMinutes(5)) // Kiểm tra online trong vòng 5 phút
                ->where(function ($query) {
                    $currentDay = Carbon::now()->format('l');
                    $currentTime = Carbon::now()->format('H:i:s');

                    $query->where('ta.day_of_week', 'like', '%' . $currentDay . '%')
                        ->where('ta.available_from', '<=', $currentTime)
                        ->where('ta.available_to', '>=', $currentTime);
                })
                ->get();


            // Lọc kỹ thuật viên theo khoảng cách 10km
            $filteredTechnicians = $technicians->filter(function ($technician) use ($customerLat, $customerLon) {
                $distance = $this->haversineDistance($customerLat, $customerLon, $technician->latitude, $technician->longitude);
                return $distance <= 10;
            });

            // Kiểm tra nếu danh sách kỹ thuật viên bị rỗng
            // if ($filteredTechnicians->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Không có kỹ thuật viên nào trong vòng bán kính 10km',
            //     ], 404);  
            // }

            return response()->json([
                'success' => true,
                'message' => 'Tìm thấy các kĩ thuật viên trong vòng bán kính 10km',
                'listTechnicians' => $filteredTechnicians,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi trong quá trình tìm kiếm kỹ thuật viên',
                'trace' => $e->getMessage(),
            ], 500);
        }
    }

    private function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        $distance = $earthRadius * $c;

        return $distance;
    }

    public function saveLocation(Request $request)
    {
        $user = User::find($request->id);

        if ($user && $user->role === 'technician') {
            $location = Location::updateOrCreate(
                ['technician_id' => $request->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]
            );

            broadcast(new TechnicianLocationUpdated($request->id, $request->latitude, $request->longitude))->toOthers();

            return response()->json(['message' => 'Vị trí đã được lưu vào database!']);
        } else {
            return response()->json(['message' => 'Chỉ cập nhật tọa độ với kỹ thuật viên!']);
        }
    }
}
