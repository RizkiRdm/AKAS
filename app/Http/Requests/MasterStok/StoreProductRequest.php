<?php

namespace App\Http\Requests\MasterStok;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        $id = is_object($product) ? $product->id : $product;

        $rule = Rule::unique('products', 'sku');
        if ($id) {
            $rule->ignore($id);
        }

        return [
            'name' => 'required|string|max:255',
            'sku' => ['nullable', 'string', 'max:50', $rule],
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'initial_stock' => 'nullable|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|gt:purchase_price',
        ];
    }
}
