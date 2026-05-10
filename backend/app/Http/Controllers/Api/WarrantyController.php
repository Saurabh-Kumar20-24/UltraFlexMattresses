<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warranty\RegisterWarrantyRequest;
use App\Http\Resources\WarrantyResource;
use App\Models\Product;
use App\Models\Warranty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WarrantyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $warranties = Warranty::where('user_id', $request->user()->id)
                              ->latest()
                              ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => WarrantyResource::collection($warranties),
            'meta'    => [
                'current_page' => $warranties->currentPage(),
                'last_page'    => $warranties->lastPage(),
                'total'        => $warranties->total(),
            ],
        ]);
    }

    public function show(Request $request, string $number): JsonResponse
    {
        $warranty = Warranty::where('warranty_number', $number)
                            ->where('user_id', $request->user()->id)
                            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new WarrantyResource($warranty),
        ]);
    }


    public function store(RegisterWarrantyRequest $request): JsonResponse
    {
        $user    = $request->user();
        $product = Product::findOrFail($request->product_id);

        $warrantyYears = $product->warranty_years ?? 1;
        $purchaseDate  = \Carbon\Carbon::parse($request->purchase_date);
        $expiryDate    = $purchaseDate->copy()->addYears($warrantyYears);

        // Handle invoice image upload
        $invoicePath = null;
        if ($request->hasFile('invoice_image')) {
            $invoicePath = $request->file('invoice_image')
                                   ->store('warranties', 'public');
        }

        $warranty = Warranty::create([
            'user_id'          => $user->id,
            'product_id'       => $product->id,
            'customer_name'    => $request->customer_name,
            'customer_email'   => $request->customer_email,
            'customer_phone'   => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_city'    => $request->customer_city,
            'customer_state'   => $request->customer_state,
            'customer_pincode' => $request->customer_pincode,
            'product_name'     => $product->name,
            'product_sku'      => $request->variant_size
                                    ? $product->variants()
                                              ->where('size', $request->variant_size)
                                              ->value('sku')
                                    : null,
            'variant_size'     => $request->variant_size,
            'purchase_date'    => $request->purchase_date,
            'purchase_from'    => $request->purchase_from ?? 'online',
            'purchase_amount'  => $request->purchase_amount,
            'warranty_years'   => $warrantyYears,
            'expiry_date'      => $expiryDate,
            'status'           => 'active',
            'invoice_image'    => $invoicePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warranty registered successfully.',
            'data'    => new WarrantyResource($warranty),
        ], 201);
    }
}