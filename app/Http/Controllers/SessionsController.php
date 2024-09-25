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
            'identifier' => 'required', 
            'password' => 'required',
        ],[
            'identifier.required' => 'Please enter your email or phone number'
        ]); 
        $field = filter_var($attributes['identifier'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
    
        
        if (Auth::attempt([$field => $attributes['identifier'], 'password' => $attributes['password']])) {
            session()->regenerate();
            return redirect('dashboard')->with(['success' => 'You are logged in.']);
        } else {
            return back()->withErrors(['identifier' => 'Email/Phone number or password invalid.']);
        }
    }
    
    
    public function destroy()
    {

        Auth::logout();

        return redirect('/login')->with(['success'=>'You\'ve been logged out.']);
    }
}
