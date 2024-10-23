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
    public function index()
    {

        return view('dashboard');
    }

    public function filterServices(Request $request)
    {
        $customerLat = $request->latitude;
        $customerLon = $request->longitude;

        $customerLat = (float) $customerLat;
        $customerLon = (float) $customerLon;
        $maxDistance = 10;

        // Lấy danh sách kỹ thuật viên online trong vòng 5 phút
        $onlineTechnicians = DB::table('users as t')
            ->join('locations as l', 't.user_id', '=', 'l.technician_id')
            ->join('technician_availability as ta', 't.user_id', '=', 'ta.technician_id')
            ->select(
                't.user_id as technician_id',
                't.username as technician_name',
                'l.latitude',
                'l.longitude',
                'l.updated_at',
                'ta.available_from',
                'ta.available_to',
                'ta.day_of_week'
            )
            ->where('l.updated_at', '>=', Carbon::now()->subMinutes(5)) // Kiểm tra online trong vòng 5 phút
            ->where(function ($query) {
                $currentDay = Carbon::now()->format('l');
                $currentTime = Carbon::now()->format('H:i:s');

                $query->where('ta.day_of_week', 'like', '%' . $currentDay . '%')
                    ->where('ta.available_from', '<=', $currentTime)
                    ->where('ta.available_to', '>=', $currentTime);
            })
            ->get();

        $nearestTechnician = null;
        $shortestDistance = $maxDistance;

        foreach ($onlineTechnicians as $technician) {
            $distance = $this->haversineDistance($customerLat, $customerLon, $technician->latitude, $technician->longitude);

            if ($distance <= $shortestDistance) {
                $nearestTechnician = $technician;
                $shortestDistance = $distance;
            }
        }
        if ($nearestTechnician) {
            // Lấy tất cả dịch vụ của kỹ thuật viên gần nhất
            $technicianServices = DB::table('technician_service as ts')
                ->join('services as s', 'ts.service_id', '=', 's.service_id')
                ->select('s.service_id', 's.name', 's.description', 's.price')
                ->where('ts.technician_id', '=', $nearestTechnician->technician_id)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Tìm thấy kĩ thuật viên trong vòng bán kính 10km',
                'technician' => $nearestTechnician,
                'services' => $technicianServices
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không có kĩ thuật viên nào trong vòng bán kính 10km'
            ], 200);
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
