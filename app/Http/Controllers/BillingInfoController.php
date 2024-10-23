<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BillingInfo;
use App\Models\TechnicianDetail;
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
        $BillingInfo = BillingInfo::where('customer_id', Auth::user()->user_id)->get();
        $totalAmount = TechnicianDetail::where('technician_id', Auth::user()->user_id)->sum('amount');


        return view('billing', [
            'BillingInfo' => $BillingInfo,
            'totalAmount' => $totalAmount,


        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createDeposit(Request $request)
    {
        $attributes = request()->validate([
            'card_holder_name_new' => ['required', 'max:50', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'card_number_new' => ['required', 'digits:12', 'numeric'],
            'card_security_code_new'     => ['required', 'min:3', 'numeric'],
            'billing_address_new' => ['required', 'max:70'],
            'card_expiration_date_new' => ['required'],
            'amount_new' => ['required', 'numeric', 'min:1'],
        ]);
        $attributes['customer_id'] = Auth::user()->user_id;
        $existingBilling = BillingInfo::where('card_number', $attributes['card_number_new'])
            ->where('card_holder_name', $attributes['card_holder_name_new'])
            ->where('billing_address', $attributes['billing_address_new'])
            ->where('card_expiration_date', $attributes['card_expiration_date_new'])
            ->where('customer_id', $attributes['customer_id'])
            ->first();

        if ($existingBilling) {
            return redirect('/billing')->with('error_exist', 'true');
        }
        BillingInfo::create([
            'card_holder_name' => $attributes['card_holder_name_new'],
            'card_number' => $attributes['card_number_new'],
            'card_security_code' => $attributes['card_security_code_new'],
            'billing_address' => $attributes['billing_address_new'],
            'card_expiration_date' => $attributes['card_expiration_date_new'],
            'customer_id' => $attributes['customer_id'],
        ]);
        $tech = TechnicianDetail::where('technician_id', $attributes['customer_id'])->first();
        if ($tech) {
            if ($tech->amount) {
                $tech->update(['amount' => $attributes['amount_new'] + $tech->amount]);
            } else {
                $tech->update(['amount' => $attributes['amount_new']]);
            }
        } else {
            TechnicianDetail::create(['technician_id ' => Auth::user()->user_id, 'amount' => $attributes['amount_new']]);
        }

        return redirect('/billing')->with('success_deposit', 'Deposit successfully');
    }
    public function updateDeposit(Request $request)
    {
        $attributes = request()->validate([
            'amount_available' => ['required', 'numeric', 'min:1'],
            'payment_method_available' => ['required']
        ]);
        $tech = TechnicianDetail::where('technician_id', Auth::user()->user_id)->first();
        if ($tech) {
            if ($tech->amount) {
                $tech->update(['amount' => $attributes['amount_available'] + $tech->amount]);
            } else {
                $tech->update(['amount' => $attributes['amount_available']]);
            }
        } else {
            TechnicianDetail::create(['technician_id' => Auth::user()->user_id, 'amount' => $attributes['amount_available']]);
        }
        return redirect('/billing')->with('success_deposit', 'Deposit successfully');
    }


    public function createBilling(Request $request)
    {
        $attributes = request()->validate([
            'card_holder_name' => ['required', 'max:50', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'card_number' => ['required', 'digits:12', 'numeric'],
            'card_security_code'     => ['required', 'min:3', 'numeric'],
            'billing_address' => ['required', 'max:70'],
            'card_expiration_date' => ['required']
        ]);
        $attributes['customer_id'] = Auth::user()->user_id;
        $attributes['payment_method'] = $request->payment_method;
        BillingInfo::create($attributes);
        return redirect('/billing')->with('success', 'Payment method added successfully');
    }
    public function updateBilling(Request $request)
    {
        $attributes = request()->validate([
            'card_holder_name_update' => ['required', 'max:50', 'string', 'regex:/^[a-zA-Z\s]+$/'],
            'card_number_update' => ['required', 'digits:12', 'numeric'],
            'card_security_code_update'     => ['required', 'min:3', 'numeric'],
            'billing_address_update' => ['required', 'max:70'],
            'card_expiration_date_update' => ['required']
        ]);
        $attributes['customer_id'] = Auth::user()->user_id;
        $attributes['payment_method'] = $request->payment_method;
        BillingInfo::where('billing_id', $request->input('billing_id'))->update($attributes);
        return redirect('/billing')->with('success', 'Payment method updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
