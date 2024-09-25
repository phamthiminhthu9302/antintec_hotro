<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use App\Models\Payment;

class ProfileController extends Controller
{


    public function profile()
    {
        $useData = auth()->user();

        return response()->json([
            'status' => true,
            'message' => "Profile Information",
            'data' => $useData,
        ], 200);
    }


    //HUYNH-DUC-TAM
    use HttpResponses;


    public function updateProfile(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'phone' => ['string', 'max:15', 'unique:users'],
            'address' => ['string', 'max:255'],
            'role' => ['string', 'max:15'],
        ]);

        $user = auth()->user();
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->role = $validated['role'];
        $user->save();


        return $this->success($user);
    }

    public function updateAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
        ]);

        $this->setAddress('address', $validated['address']);
        return $this->success(null);

    }

    public function deleteAddress(Request $request): JsonResponse
    {
        $this->setAddress('address', null);
        return $this->success(null);
    }

    public function setField($field, $value): void
    {
        $user = auth()->user();
        $user->$field = $value;
        $user->save();
    }

    //change password will revoke access token, user will have to log in again to obtain new access token
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => [Password::defaults()]
        ]);

        $this->setField('password', Hash::make($validated['password']));

        return $this->success(auth()->user());
    }

    public function updateRole(Request $request)
    {
        $validated = $request->validate([
            'role' => ['required', 'min:3']
        ]);
        $this->setField('role', $validated['role']);
        return $this->success(auth()->user());
    }


    public function updatePaymentMethod(Request $request): JsonResponse
    {
        $validated = request()->validate([
            'request_id' => ['required', 'numeric','min:1'],
            'payment_method' => ['required', Rule::in('cash', 'credit_card', 'e_wallet')],
        ], ['payment_method.in' => 'Payment method must be in [cash, credit_card, e_wallet]']);
        //1. get customer id
        $user = auth()->user();
        $user_id = $user->user_id;
        $requestId = $validated['request_id'];
        //2. from requests table with customer id, update payments method by request id
        //3. change payment methods for specific request id
        $requests = \App\Models\Request::where('customer_id', $user_id)->get();
        foreach ($requests as $request) {
            if ($request->request_id === $requestId) {
                $paymentUpdated = Payment::where('request_id', $request->request_id)->update(['payment_method' => $validated['payment_method']]);
                return $this->success($paymentUpdated);
            }
        }
        return $this->fail("Invalid request");
    }
    //-------------------------------------
}
