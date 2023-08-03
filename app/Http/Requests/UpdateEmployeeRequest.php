<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:employees',
            'gender' => 'required|string',
            'age' => 'required|integer',
            'phone' => 'required|string',
            'photo' => 'nullable|image|max:2048',
            'team_id' => 'required|exists:teams,id',
            'role_id' => 'required|exists:roles,id',
        ];
    }
}
