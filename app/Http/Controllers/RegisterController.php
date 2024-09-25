<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Logger\ConsoleLogger;

class RegisterController extends Controller
{
    public function create()
    {
        return view('session.register');
    }

    public function store()
    {
        $attributes = request()->validate([
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8',
            'email' => 'required|email|max:100|unique:users',
            'phone' => ['required','unique:users', 'regex:/^(0?)(3[2-9]|5[6|8|9]|7[0|6-9]|8[0-6|8|9]|9[0-4|6-9])[0-9]{7}$/'], // Số điện thoại gồm 10 số nếu có nhập số 0 ở đầu tiên. Nếu không nhập 0 thì còn 9 số.,
            'role' => 'required|in:customer,technician',

        ]);

        $attributes['password'] = bcrypt($attributes['password']);



        session()->flash('success', 'Your account has been created.');
        $user = User::create($attributes);

        Auth::login($user);
        return redirect('/dashboard');
    }
}
