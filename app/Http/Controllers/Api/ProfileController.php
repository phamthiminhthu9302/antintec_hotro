<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TechnicianDetail;
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
            'phone' => ['string', 'max:15', 'unique:users', 'nullable'],
            'address' => ['string', 'max:255', 'nullable'],
            'role' => ['string', 'max:15', 'nullable'],
        ]);
        $user = auth()->user();
        foreach ($validated as $key => $value) {
            if ($key != null) {
                $user->$key = $value;
            }
        }
        $user->save();
        return $this->success($user);
    }

//    public function updateAddress(Request $request): JsonResponse
//    {
//        $validated = $request->validate([
//            'address' => ['required', 'string', 'max:255'],
//        ]);
//
//        $this->setAddress('address', $validated['address']);
//        return $this->success(null);
//
//    }

//    public function deleteAddress(Request $request): JsonResponse
//    {
//        $this->setAddress('address', null);
//        return $this->success(null);
//    }

    public function setField($field, $value): void
    {
        $user = auth()->user();
        $user->$field = $value;
        $user->save();
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'max:100']//[Password::defaults()]
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
            'request_id' => ['required', 'numeric', 'min:1'],
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
            if ($request->request_id === $requestId && $request->customer_id === $user_id) {
                $paymentUpdated = Payment::where('request_id', $request->request_id)->update(['payment_method' => $validated['payment_method']]);
                return $this->success($paymentUpdated);
            }
        }
        return $this->fail('Please check you are the owner of this request.');
    }

    //-------------------------------------


    public function updateInfoTech(Request $request)
    {

        $useData = auth()->user();

        if ($useData->role == "technician") {

            $techDetail = TechnicianDetail::firstOrNew(['technician_id' => $useData->user_id]);

            $techDetail->skills = $request->skills;
            $techDetail->certifications = $request->certifications;
            $techDetail->work_area = $request->work_area;

            $techDetail->save();

            return response()->json([
                'status' => true,
                'message' => 'Information Tech updated successfully.'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'error' => 'User is not a technician.'
        ], 403);
    }

}
