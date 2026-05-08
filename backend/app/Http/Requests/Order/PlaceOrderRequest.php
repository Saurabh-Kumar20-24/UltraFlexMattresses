<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Shipping address
            'shipping_address'          => 'required|array',
            'shipping_address.name'     => 'required|string|max:100',
            'shipping_address.phone'    => 'required|string|min:10|max:15',
            'shipping_address.line1'    => 'required|string|max:255',
            'shipping_address.line2'    => 'nullable|string|max:255',
            'shipping_address.city'     => 'required|string|max:100',
            'shipping_address.state'    => 'required|string|max:100',
            'shipping_address.pincode'  => 'required|string|min:6|max:10',

            // Payment
            'payment_method'            => 'required|in:cod,razorpay,upi',
            'payment_id'                => 'nullable|string|max:100',
            'razorpay_order_id'         => 'nullable|string|max:100',

            // Optional
            'customer_notes'            => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.name.required'    => 'Recipient name is required.',
            'shipping_address.phone.required'   => 'Phone number is required.',
            'shipping_address.line1.required'   => 'Address line 1 is required.',
            'shipping_address.city.required'    => 'City is required.',
            'shipping_address.state.required'   => 'State is required.',
            'shipping_address.pincode.required' => 'Pincode is required.',
            'payment_method.required'           => 'Payment method is required.',
            'payment_method.in'                 => 'Invalid payment method selected.',
        ];
    }
}