<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'max:50'],
            'email' => ['required', 'email', 'max:50', Rule::unique('users')->ignore(Auth::user()->user_id,'user_id')],
            'phone' => ['required', 'string', 'regex:/^(0[1-9]{1}[0-9]{8}|(84[1-9]{1}[0-9]{8}))$/', Rule::unique('users')->ignore(Auth::user()->user_id,'user_id')],
            'address' => ['max:70']
        ];
    }
}
