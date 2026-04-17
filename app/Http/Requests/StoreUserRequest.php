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
        return $this->user()->can('manage-users');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'unique:users,username', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'nama_pegawai' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'in:admin,cashier'],
        ];
    }
}
