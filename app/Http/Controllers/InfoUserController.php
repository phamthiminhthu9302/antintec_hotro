<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TechnicianDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

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

    public function store()
    {

        $attributes = request()->validate([
            'username' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->user_id, 'user_id')],
            'phone'     => ['digits_between:10,11', Rule::unique('users')->ignore(Auth::user()->phone, 'phone')],
            'address' => ['max:70'],

        ]);

        User::where('user_id', Auth::user()->user_id)
            ->update($attributes);

        if (Auth::user()->role === 'technician') {
            $technicianAttributes = request()->validate([
                'skills' => ['required', 'max:70'],
                'certifications' => ['required', 'max:70'],
                'work_area' => ['required', 'max:70'],
            ]);

            // Cập nhật dữ liệu cho bảng 'technician'
            $technicianDetail = TechnicianDetail::firstOrNew(
                ['technician_id' => Auth::user()->user_id] // Điều kiện tìm kiếm
            );
            
            // Cập nhật các thuộc tính cho bản ghi (mới hoặc đã tồn tại)
            $technicianDetail->fill($technicianAttributes);

            // Lưu bản ghi
            $technicianDetail->save();
        }

        return redirect('/user-profile/update')->with('success', 'Profile updated successfully');
    }
}
