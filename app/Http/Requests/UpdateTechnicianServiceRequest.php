<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTechnicianServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'technician_id' => 'required|exists:users,user_id', // Kiểm tra technician_id phải tồn tại trong bảng users
            'service_id' => 'required|exists:service,service_id', // Kiểm tra service_id phải tồn tại trong bảng service
        ];
    }
}
