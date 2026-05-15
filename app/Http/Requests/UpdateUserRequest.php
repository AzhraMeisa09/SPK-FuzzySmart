<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     */
    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'username')->ignore($userId, 'id_user'),
            ],
            'email'        => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId, 'id_user'),
            ],
            'password'     => 'nullable|string|min:8',
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
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'email.unique'    => 'Email sudah digunakan oleh user lain.',
            'role.in'         => 'Role tidak valid.',
        ];
    }
}
