<?php

namespace App\Http\Requests\Warranty;

use Illuminate\Foundation\Http\FormRequest;

class RegisterWarrantyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Customer details
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'nullable|email|max:150',
            'customer_phone'   => 'required|string|min:10|max:15',
            'customer_address' => 'nullable|string|max:255',
            'customer_city'    => 'nullable|string|max:100',
            'customer_state'   => 'nullable|string|max:100',
            'customer_pincode' => 'nullable|string|min:6|max:10',

            // Product details
            'product_id'       => 'required|integer|exists:products,id',
            'variant_size'     => 'nullable|string|max:50',

            // Purchase details
            'purchase_date'    => 'required|date|before_or_equal:today',
            'purchase_from'    => 'nullable|string|max:100',
            'purchase_amount'  => 'nullable|numeric|min:0',

            // Invoice image
            'invoice_image'    => 'nullable|image|mimes:jpg,jpeg,png|max:3072',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required'  => 'Customer name is required.',
            'customer_phone.required' => 'Phone number is required.',
            'product_id.required'     => 'Please select a product.',
            'product_id.exists'       => 'Selected product does not exist.',
            'purchase_date.required'  => 'Purchase date is required.',
            'purchase_date.before_or_equal' => 'Purchase date cannot be in the future.',
            'invoice_image.image'     => 'Invoice must be an image file.',
            'invoice_image.max'       => 'Invoice image must not exceed 3MB.',
        ];
    }
}