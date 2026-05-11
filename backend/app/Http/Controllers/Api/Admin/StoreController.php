<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Store::latest();

        if ($request->filled('search')) {
            $query->where('name',  'like', '%' . $request->search . '%')
                  ->orWhere('city',  'like', '%' . $request->search . '%')
                  ->orWhere('state', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('status')) {
            $request->status === 'active'
                ? $query->where('is_active', true)
                : $query->where('is_active', false);
        }

        $stores = $query->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => StoreResource::collection($stores),
            'meta'    => [
                'current_page' => $stores->currentPage(),
                'last_page'    => $stores->lastPage(),
                'total'        => $stores->total(),
            ],
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'            => 'required|string|max:150',
            'type'            => 'required|in:company_owned,dealer,distributor',
            'phone'           => 'nullable|string|max:15',
            'whatsapp'        => 'nullable|string|max:15',
            'email'           => 'nullable|email|max:150',
            'address'         => 'required|string',
            'landmark'        => 'nullable|string|max:150',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'required|string|min:6|max:10',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'google_maps_url' => 'nullable|url|max:500',
            'business_hours'  => 'nullable|array',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'boolean',
            'store_image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('store_image')) {
            $imagePath = $request->file('store_image')
                                 ->store('stores', 'public');
        }

        $store = Store::create([
            'name'            => $request->name,
            'type'            => $request->type,
            'phone'           => $request->phone,
            'whatsapp'        => $request->whatsapp,
            'email'           => $request->email,
            'address'         => $request->address,
            'landmark'        => $request->landmark,
            'city'            => $request->city,
            'state'           => $request->state,
            'pincode'         => $request->pincode,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'google_maps_url' => $request->google_maps_url,
            'business_hours'  => $request->business_hours,
            'sort_order'      => $request->sort_order ?? 0,
            'is_active'       => $request->boolean('is_active', true),
            'store_image'     => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully.',
            'data'    => new StoreResource($store),
        ], 201);
    }


    public function update(Request $request, int $id): JsonResponse
    {
        $store = Store::findOrFail($id);

        $request->validate([
            'name'            => 'required|string|max:150',
            'type'            => 'required|in:company_owned,dealer,distributor',
            'phone'           => 'nullable|string|max:15',
            'whatsapp'        => 'nullable|string|max:15',
            'email'           => 'nullable|email|max:150',
            'address'         => 'required|string',
            'landmark'        => 'nullable|string|max:150',
            'city'            => 'required|string|max:100',
            'state'           => 'required|string|max:100',
            'pincode'         => 'required|string|min:6|max:10',
            'latitude'        => 'nullable|numeric|between:-90,90',
            'longitude'       => 'nullable|numeric|between:-180,180',
            'google_maps_url' => 'nullable|url|max:500',
            'business_hours'  => 'nullable|array',
            'sort_order'      => 'nullable|integer|min:0',
            'is_active'       => 'boolean',
            'store_image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = [
            'name'            => $request->name,
            'type'            => $request->type,
            'phone'           => $request->phone,
            'whatsapp'        => $request->whatsapp,
            'email'           => $request->email,
            'address'         => $request->address,
            'landmark'        => $request->landmark,
            'city'            => $request->city,
            'state'           => $request->state,
            'pincode'         => $request->pincode,
            'latitude'        => $request->latitude,
            'longitude'       => $request->longitude,
            'google_maps_url' => $request->google_maps_url,
            'business_hours'  => $request->business_hours,
            'sort_order'      => $request->sort_order ?? $store->sort_order,
            'is_active'       => $request->boolean('is_active'),
        ];

        if ($request->hasFile('store_image')) {
            if ($store->store_image) {
                Storage::disk('public')->delete($store->store_image);
            }
            $data['store_image'] = $request->file('store_image')
                                           ->store('stores', 'public');
        }

        $store->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully.',
            'data'    => new StoreResource($store->fresh()),
        ]);
    }

 
    public function destroy(int $id): JsonResponse
    {
        $store = Store::findOrFail($id);

        if ($store->store_image) {
            Storage::disk('public')->delete($store->store_image);
        }

        $store->delete();

        return response()->json([
            'success' => true,
            'message' => 'Store deleted successfully.',
        ]);
    }

    public function toggle(int $id): JsonResponse
    {
        $store = Store::findOrFail($id);
        $store->update(['is_active' => !$store->is_active]);

        return response()->json([
            'success'   => true,
            'message'   => 'Store status updated.',
            'is_active' => $store->fresh()->is_active,
        ]);
    }
}