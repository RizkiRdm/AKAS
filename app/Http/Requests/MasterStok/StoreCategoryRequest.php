<?php

namespace App\Http\Requests\MasterStok;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $id = is_object($category) ? $category->id : $category;

        $rule = Rule::unique('categories', 'name');
        if ($id) {
            $rule->ignore($id);
        }

        return [
            'name' => ['required', 'string', 'max:100', $rule],
        ];
    }
}
