<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WarrantyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'warranty_number'  => $this->warranty_number,
            'status'           => $this->status,
            'customer_name'    => $this->customer_name,
            'customer_email'   => $this->customer_email,
            'customer_phone'   => $this->customer_phone,
            'customer_address' => $this->customer_address,
            'customer_city'    => $this->customer_city,
            'customer_state'   => $this->customer_state,
            'customer_pincode' => $this->customer_pincode,
            'product_name'     => $this->product_name,
            'product_sku'      => $this->product_sku,
            'variant_size'     => $this->variant_size,
            'purchase_date'    => $this->purchase_date->toDateString(),
            'purchase_from'    => $this->purchase_from,
            'purchase_amount'  => $this->purchase_amount,
            'warranty_years'   => $this->warranty_years,
            'expiry_date'      => $this->expiry_date->toDateString(),
            'is_valid'         => $this->isValid(),
            'is_expired'       => $this->isExpired(),
            'claim_reason'     => $this->claim_reason,
            'claimed_at'       => $this->claimed_at?->toDateTimeString(),
            'admin_remarks'    => $this->admin_remarks,
            'invoice_image'    => $this->invoice_image
                                       ? asset('storage/' . $this->invoice_image)
                                       : null,
            'created_at'       => $this->created_at->toDateString(),
        ];
    }
}