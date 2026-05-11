<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = Store::active()->ordered();

        if ($request->filled('city')) {
            $query->byCity($request->city);
        }

        if ($request->filled('state')) {
            $query->byState($request->state);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $stores = $query->get();

        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->lat;
            $lng = (float) $request->lng;

            $stores = $stores->map(function ($store) use ($lat, $lng) {
                $store->distance = $store->distanceFrom($lat, $lng);
                return $store;
            })->sortBy('distance')->values();
        }

        return response()->json([
            'success' => true,
            'data'    => StoreResource::collection($stores),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $store = Store::active()->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => new StoreResource($store),
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
        ]);

        $keyword = $request->query;

        $stores = Store::active()
                       ->ordered()
                       ->where(function ($q) use ($keyword) {
                           $q->where('name',    'like', '%' . $keyword . '%')
                             ->orWhere('city',   'like', '%' . $keyword . '%')
                             ->orWhere('state',  'like', '%' . $keyword . '%')
                             ->orWhere('address','like', '%' . $keyword . '%');
                       })
                       ->get();

        return response()->json([
            'success' => true,
            'data'    => StoreResource::collection($stores),
            'total'   => $stores->count(),
        ]);
    }


    public function cities(): JsonResponse
    {
        $cities = Store::active()
                       ->distinct()
                       ->orderBy('city')
                       ->pluck('city');

        return response()->json([
            'success' => true,
            'data'    => $cities,
        ]);
    }

 
    public function states(): JsonResponse
    {
        $states = Store::active()
                       ->distinct()
                       ->orderBy('state')
                       ->pluck('state');

        return response()->json([
            'success' => true,
            'data'    => $states,
        ]);
    }
}