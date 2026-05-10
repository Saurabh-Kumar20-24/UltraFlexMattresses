<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarrantyResource;
use App\Models\Warranty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarrantyController extends Controller
{
    // GET /api/admin/warranties
    public function index(Request $request): JsonResponse
    {
        $query = Warranty::with(['user', 'product'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where('warranty_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name',  'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
        }

        $warranties = $query->paginate(15);

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

    // PATCH /api/admin/warranties/{id}
    public function update(Request $request, int $id): JsonResponse
    {
        $warranty = Warranty::findOrFail($id);

        $request->validate([
            'status'        => 'required|in:active,expired,claimed,rejected',
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $data = [
            'status'        => $request->status,
            'admin_remarks' => $request->admin_remarks,
        ];

        if ($request->status === 'claimed') {
            $data['claimed_at'] = now();
        }

        $warranty->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Warranty updated successfully.',
            'data'    => new WarrantyResource($warranty->fresh()),
        ]);
    }
}