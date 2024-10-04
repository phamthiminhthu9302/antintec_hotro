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
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UpdateTechnicianAvailability;

class InfoUserController extends Controller
{

    public function create()
    {
        // $usersWithTechnicianDetails = User::select('users.*', 'technician_details.*')
        //     ->join('technician_details', 'users.user_id', '=', 'technician_details.technician_id')
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

        if (Auth::user()->role === 'technician') {
            if (is_string($usersWithTechnicianDetails->day_of_week)) {
                $selectedDays = explode(',', $usersWithTechnicianDetails->day_of_week);
            } else {
                $selectedDays = [];
            }

            return view('laravel-examples/user-profile')->with([
                'usersWithTechnicianDetails' => $usersWithTechnicianDetails,
                'selectedDays' => $selectedDays,
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

    public function location(){
        $user_id = Auth::user()->user_id;
        return view('laravel-examples.user-location')->with("user_id", $user_id);
    }

    public function AddLocation(Request $request) {

        $location = Location::updateOrCreate(
            ['technician_id' => $request->id], 
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );
    
        return response()->json(['success' => true, 'message' => 'Location added successfully']);
    }
}
