<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;


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
            'phone' => ['string', 'max:15'],
            'address' => ['string', 'max:255'],
            'role' => ['string', 'max:15'],
            'payment_method' => ['string', 'max:50', 'in:cash,credit_card,e_wallet'],
        ]);

        $user = auth()->user();
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->role = $validated['role'];
        $user->payment_method = $validated['payment_method'];
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
    //-------------------------------------
}
