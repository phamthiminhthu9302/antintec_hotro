<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillingInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class BillingInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $BillingInfo = BillingInfo::where('customer_id',Auth::user()->user_id)->get();
        return view('billing',[
            'BillingInfo'=> $BillingInfo
        ]);
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
        $attributes = request()->validate([
            'card_holder_name' => ['required', 'max:50'],
            'card_number' => ['required', 'max:50'],
            'card_security_code'     => ['required','min:3'],
            'billing_address' => ['required','max:70'],
        ]);
        $attributes['customer_id'] = Auth::user()->user_id;
        $attributes['card_expiration_date'] = $request->input('card_expiration_date');
        $attributes['payment_method'] = $request->input('payment_method');
        
        BillingInfo::create($attributes);
        return redirect('/billing')->with('success','Payment method added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
       
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
    public function update(Request $request)
    {
        $attributes = request()->validate([
            'card_holder_name' => ['required', 'max:50'],
            'card_number' => ['required', 'max:50'],
            'card_security_code'     => ['required','min:3'],
            'billing_address' => ['required','max:70'],
            
           
        ]);
        $attributes['card_expiration_date'] = $request->input('card_expiration_date');
        $attributes['payment_method'] = $request->input('payment_method');
        
        BillingInfo::where('billing_id',$request->input('billing_id'))
        ->update($attributes);
        return redirect('/billing')->with('success','Payment method updated successfully');   
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        BillingInfo::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
