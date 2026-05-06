<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category'    => 'nullable|string|exists:categories,slug',
            'min_price'   => 'nullable|numeric|min:0',
            'max_price'   => 'nullable|numeric|min:0',
            'featured' => 'nullable|string|in:true,false,0,1',
            'search'      => 'nullable|string|max:100',
            'sort_by'     => 'nullable|in:price_low,price_high,newest,popular',
            'per_page'    => 'nullable|integer|min:1|max:50',
        ];
    }
}