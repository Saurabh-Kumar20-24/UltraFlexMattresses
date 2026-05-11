<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:150',
            'name'  => 'nullable|string|max:100',
        ]);

        $existing = NewsletterSubscriber::where('email', $request->email)->first();

        if ($existing && $existing->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed.',
            ], 422);
        }

        if ($existing && !$existing->is_active) {
            $existing->update([
                'is_active'        => true,
                'subscribed_at'    => now(),
                'unsubscribed_at'  => null,
                'name'             => $request->name ?? $existing->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'You have been resubscribed successfully.',
            ]);
        }

        NewsletterSubscriber::create([
            'email'         => $request->email,
            'name'          => $request->name,
            'is_active'     => true,
            'subscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing!',
        ], 201);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:newsletter_subscribers,email',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        $subscriber->update([
            'is_active'       => false,
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You have been unsubscribed successfully.',
        ]);
    }
}