<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreferenceController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $query = UserPreference::with('user')->latest();

        if ($request->filled('shopping_for')) {
            $query->where('shopping_for', $request->shopping_for);
        }

        if ($request->filled('sleep_concern')) {
            $query->where('sleep_concern', $request->sleep_concern);
        }

        if ($request->filled('budget_range')) {
            $query->where('budget_range', $request->budget_range);
        }

        if ($request->filled('completed')) {
            $query->where('modal_completed',
                $request->completed === 'true' ? true : false
            );
        }

        $preferences = $query->paginate(15);

        $stats = [
            'total'         => UserPreference::count(),
            'completed'     => UserPreference::where('modal_completed', true)->count(),
            'shopping_for'  => UserPreference::where('modal_completed', true)
                                             ->selectRaw('shopping_for, count(*) as count')
                                             ->groupBy('shopping_for')
                                             ->pluck('count', 'shopping_for'),
            'sleep_concern' => UserPreference::where('modal_completed', true)
                                             ->selectRaw('sleep_concern, count(*) as count')
                                             ->groupBy('sleep_concern')
                                             ->pluck('count', 'sleep_concern'),
            'budget_range'  => UserPreference::where('modal_completed', true)
                                             ->selectRaw('budget_range, count(*) as count')
                                             ->groupBy('budget_range')
                                             ->pluck('count', 'budget_range'),
        ];

        return response()->json([
            'success' => true,
            'stats'   => $stats,
            'data'    => $preferences->map(fn($pref) => [
                'id'             => $pref->id,
                'user'           => $pref->user ? [
                    'id'    => $pref->user->id,
                    'name'  => $pref->user->name,
                    'email' => $pref->user->email,
                ] : null,
                'session_id'     => $pref->session_id,
                'shopping_for'   => $pref->shopping_for,
                'sleep_concern'  => $pref->sleep_concern,
                'budget_range'   => $pref->budget_range,
                'modal_completed'=> $pref->modal_completed,
                'created_at'     => $pref->created_at->toDateString(),
            ]),
            'meta'    => [
                'current_page' => $preferences->currentPage(),
                'last_page'    => $preferences->lastPage(),
                'total'        => $preferences->total(),
            ],
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $preference = UserPreference::findOrFail($id);
        $preference->delete();

        return response()->json([
            'success' => true,
            'message' => 'Preference deleted successfully.',
        ]);
    }
}