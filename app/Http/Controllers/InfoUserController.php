<?php

namespace App\Http\Controllers;

use App\Models\User;
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
    

        return view('laravel-examples/user-profile')->with('usersWithTechnicianDetails',$usersWithTechnicianDetails);
    }

    public function store(Request $request)
    {

        $attributes = request()->validate([
            'username' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->user_id,'user_id')],
            'phone'     => ['digits_between:10,11',Rule::unique('users')->ignore(Auth::user()->phone,'phone')],
            'address' => ['max:70'],
           
        ]);
        
        User::where('user_id',Auth::user()->user_id)
        ->update($attributes);

        return redirect('/user-profile')->with('success','Profile updated successfully');
    }
}
