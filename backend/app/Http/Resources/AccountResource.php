<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'role'           => $this->role,
            'avatar'         => $this->avatar
                                    ? asset('storage/' . $this->avatar)
                                    : null,
            'address'        => $this->address,
            'city'           => $this->city,
            'state'          => $this->state,
            'pincode'        => $this->pincode,
            'email_verified' => !is_null($this->email_verified_at),
            'created_at'     => $this->created_at->toDateString(),
        ];
    }
}