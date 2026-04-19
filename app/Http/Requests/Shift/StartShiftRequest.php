<?php

declare(strict_types=1);

namespace App\Http\Requests\Shift;

use Illuminate\Foundation\Http\FormRequest;

class StartShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'starting_float' => 'required|numeric|min:0',
        ];
    }
}
