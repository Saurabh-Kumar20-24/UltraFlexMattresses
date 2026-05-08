<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'               => $this->id,
            'order_number'     => $this->order_number,
            'status'           => $this->status,
            'payment_status'   => $this->payment_status,
            'payment_method'   => $this->payment_method,
            'subtotal'         => $this->subtotal,
            'discount_amount'  => $this->discount_amount,
            'shipping_charge'  => $this->shipping_charge,
            'total_amount'     => $this->total_amount,
            'shipping_address' => $this->shipping_address,
            'customer_notes'   => $this->customer_notes,
            'tracking_number'  => $this->tracking_number,
            'shipping_provider'=> $this->shipping_provider,
            'shipped_at'       => $this->shipped_at?->toDateTimeString(),
            'delivered_at'     => $this->delivered_at?->toDateTimeString(),
            'cancelled_at'     => $this->cancelled_at?->toDateTimeString(),
            'can_cancel'       => $this->isCancellable(),
            'items'            => OrderItemResource::collection(
                                      $this->whenLoaded('items')
                                  ),
            'items_count'      => $this->items_count ?? $this->items->count(),
            'created_at'       => $this->created_at->toDateTimeString(),
        ];
    }
}