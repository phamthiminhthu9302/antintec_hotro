<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class SessionsController extends Controller
{
    public function create()
    {
        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email' => 'required', 
            'password' => 'required',
        ],[
            'email.required' => 'Please enter your email or phone number'
        ]); 
        $field = filter_var($attributes['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    
        
        if (Auth::attempt([$field => $attributes['email'], 'password' => $attributes['password']])) {
            session()->regenerate();
            return redirect('dashboard')->with(['success' => 'You are logged in.']);
        } else {
            return back()->withErrors(['email' => 'Email/Phone number or password invalid.']);
        }
    }
    
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
