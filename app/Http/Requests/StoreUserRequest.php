<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|max:255|unique:users,email',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:admin,guru,kepala_sekolah,wali_murid',
            'no_hp'        => 'nullable|string|max:20',
            'alamat'       => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah terdaftar.',
            'role.in'         => 'Role tidak valid.',
        ];
    }
}
