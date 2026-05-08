<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => 'required|string|min:2|max:100',
            'phone'   => 'nullable|string|min:10|max:15',
            'email'   => [
                            'required',
                            'email',
                            Rule::unique('users', 'email')
                                ->ignore($this->user()->id),
                         ],
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'pincode' => 'nullable|string|min:6|max:10',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => 'Full name is required.',
            'email.required' => 'Email address is required.',
            'email.unique'   => 'This email is already used by another account.',
            'avatar.image'   => 'Avatar must be an image file.',
            'avatar.max'     => 'Avatar must not exceed 2MB.',
        ];
    }
}