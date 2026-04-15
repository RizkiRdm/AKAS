<?php

namespace App\Http\Requests\MasterStok;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unit = $this->route('unit');
        $id = is_object($unit) ? $unit->id : $unit;

        $rule = Rule::unique('units', 'name');
        if ($id) {
            $rule->ignore($id);
        }

        return [
            'name' => ['required', 'string', 'max:50', $rule],
        ];
    }
}
