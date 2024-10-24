<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TechnicianDetail;
use App\Models\TechnicianAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateTechnicianRequest;
use App\Models\Location;
use App\Models\Service;
use App\Models\TechnicianService;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UpdateTechnicianAvailability;

class InfoUserController extends Controller
{

    public function create()
    {
        // $usersWithTechnicianAvailability = User::select('users.*', 'technician_availability.*')
        //     ->join('technician_availability', 'users.user_id', '=', 'technician_availability.technician_id')
        //     ->where('users.user_id', Auth::user()->user_id)
        //     ->first(); // Lấy bản ghi đầu tiên

        $usersWithTechnicianDetails = User::select(
            'users.user_id',
            'users.username',
            'users.email',
            'users.phone',
            'users.address',
            'technician_details.skills',
            'technician_details.certifications',
            'technician_details.work_area',
            'technician_availability.available_from',
            'technician_availability.available_to',
            'technician_availability.day_of_week'
        )
            ->leftJoin('technician_details', 'users.user_id', '=', 'technician_details.technician_id')
            ->leftJoin('technician_availability', 'users.user_id', '=', 'technician_availability.technician_id')
            ->where('users.user_id', Auth::user()->user_id)
            ->first(); // Lấy bản ghi đầu tiên

        // dd($usersWithTechnicianDetails);

        if (Auth::user()->role === 'technician') {
            if (is_string($usersWithTechnicianDetails->day_of_week)) {
                $selectedDays = explode(',', $usersWithTechnicianDetails->day_of_week);
            } else {
                $selectedDays = [];
            }

            $services = Service::all();

            // Lấy ID của kỹ thuật viên đang đăng nhập
            $currentTechnicianId = Auth::user()->user_id;

            $technicians = DB::table('users')
                ->join('technician_service', 'users.user_id', '=', 'technician_service.technician_id')
                ->join('services', 'technician_service.service_id', '=', 'services.service_id')
                ->select(
                    'users.user_id',
                    'users.username AS technician_name',
                    'services.name AS service_name',
                    'services.price AS service_price',
                    'services.service_id AS service_id'
                )
                ->where('users.role', 'technician')
                ->where('users.user_id', $currentTechnicianId)
                ->get();

            return view('laravel-examples/user-profile')->with([
                'usersWithTechnicianDetails' => $usersWithTechnicianDetails,
                'selectedDays' => $selectedDays,
                'services' => $services,
                'technicians' => $technicians
            ]);
        } else {
            return view('laravel-examples/user-profile')->with([
                'usersWithTechnicianDetails' => $usersWithTechnicianDetails
            ]);
        }
    }

    public function store(UpdateUserRequest $request)
    {
        User::where('user_id', Auth::user()->user_id)
            ->update($request->validated());

        if (Auth::user()->role === 'technician') {

            // Xác thực dữ liệu với UpdateTechnicianRequest
            $technicianAttributes = $request->validate((new UpdateTechnicianRequest)->rules());
            $this->updateTechnicianDetail($technicianAttributes);

            // Xác thực dữ liệu với UpdateTechnicianAvailability
            $availabilityAttributes = $request->validate((new UpdateTechnicianAvailability)->rules());
            $this->updateTechnicianAvailabilityRequest($availabilityAttributes);
        }
        return redirect('/user-profile/update')->with('success', 'Profile updated successfully');
    }

    protected function updateTechnicianDetail($technicianAttributes)
    {
        $technicianDetail = TechnicianDetail::firstOrNew(['technician_id' => Auth::id()]);
        $technicianDetail->fill($technicianAttributes);
        $technicianDetail->save();
    }

    protected function updateTechnicianAvailabilityRequest($availabilityAttributes)
    {
        // Đảm bảo rằng day_of_week là chuỗi trước khi lưu
        if (is_array($availabilityAttributes['day_of_week'])) {
            $availabilityAttributes['day_of_week'] = implode(',', $availabilityAttributes['day_of_week']);
        }
        // dd($availabilityAttributes);
        $technicianAvailability = TechnicianAvailability::firstOrNew(['technician_id' => Auth::id()]);
        $technicianAvailability->fill($availabilityAttributes);
        $technicianAvailability->save();
    }

    public function location()
    {
        $user_id = Auth::user()->user_id;
        return view('laravel-examples.user-location')->with("user_id", $user_id);
    }

    public function AddLocation(Request $request)
    {

        $location = Location::updateOrCreate(
            ['technician_id' => $request->id],
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Location added successfully']);
    }

    public function technicianService(Request $request)
    {
        try {
            $request->validate([
                'technician_id' => 'required|exists:users,user_id',
                'service_id' => 'required|exists:services,service_id',
            ]);

            // Kiểm tra xem kỹ thuật viên đã có dịch vụ này chưa
            $existingService = TechnicianService::where('technician_id', $request->technician_id)
                ->where('service_id', $request->service_id)
                ->first();

            // Nếu dịch vụ đã tồn tại, trả về thông báo lỗi
            if ($existingService) {
                return response()->json(['error' => 'Dịch vụ này đã được thêm rồi!'], 400);
            }

            // Nếu chưa có, tạo bản ghi mới
            TechnicianService::create([
                'technician_id' => $request->technician_id,
                'service_id' => $request->service_id,
            ]);

            return response()->json(['message' => 'Thêm kỹ năng thành công!'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra! ' . $e->getMessage()], 500);
        }
    }

    public function deleteTechnicianService($serviceId)
    {
        try {
            $technicianId = Auth::user()->user_id;

            $id = (int) $serviceId;

            TechnicianService::where('technician_id', $technicianId)
                ->where('service_id', $id)
                ->delete();

            return response()->json(['message' => 'Xóa dịch vụ thành công'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra! ' . $e->getMessage()], 500);
        }
    }
}
