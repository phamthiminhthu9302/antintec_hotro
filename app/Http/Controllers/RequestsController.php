<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Request as RequestModel;
use Illuminate\Support\Facades\Auth;

class RequestsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        switch(Auth::user()->role){
            case 'customer':
                $Requests = RequestModel::where('customer_id',Auth::user()->user_id)->get();
                break;
            case 'technician':
                $Requests = RequestModel::where('technician_id',Auth::user()->user_id)->get();
                break;
        }
        
            return view('requests.index',[
            'requests'=>$Requests]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Requests = RequestModel::find($id);
        return view('requests.show',[
            'requests'=>$Requests]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
