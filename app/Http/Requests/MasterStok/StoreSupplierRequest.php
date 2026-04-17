<?php

namespace App\Http\Requests\MasterStok;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:100',
            'address' => 'nullable|string',
        ];
    }
}
