<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TechnicianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateTechnicianRequest;

class InfoUserController extends Controller
{

    public function create()
    {
        $usersWithTechnicianDetails = User::select('users.*', 'technician_details.*')
            ->join('technician_details', 'users.user_id', '=', 'technician_details.technician_id')
            ->where('users.user_id', Auth::user()->user_id)
            ->first(); // Lấy bản ghi đầu tiên


        return view('laravel-examples/user-profile')->with('usersWithTechnicianDetails', $usersWithTechnicianDetails);
    }

    public function store(UpdateUserRequest $request)
    {
        User::where('user_id', Auth::user()->user_id)
            ->update($request->validated());

            if (Auth::user()->role === 'technician') {
                // Xác thực dữ liệu với UpdateTechnicianRequest
                $technicianAttributes = $request->validate((new UpdateTechnicianRequest)->rules());
                $this->updateTechnicianDetail($technicianAttributes);
            }
        return redirect('/user-profile/update')->with('success', 'Profile updated successfully');
    }
    protected function updateTechnicianDetail($technicianAttributes)
    {
        $technicianDetail = TechnicianDetail::firstOrNew(['technician_id' => Auth::id()]);
        $technicianDetail->fill($technicianAttributes);
        $technicianDetail->save();
    }
}
