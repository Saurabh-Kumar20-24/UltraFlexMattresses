<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Resources\AccountResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
   
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new AccountResource($request->user()),
        ]);
    }

   
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'city'    => $request->city,
            'state'   => $request->state,
            'pincode' => $request->pincode,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $path = $request->file('avatar')
                            ->store('avatars', 'public');
            $data['avatar'] = $path;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data'    => new AccountResource($user->fresh()),
        ]);
    }


    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.',
                'errors'  => [
                    'current_password' => ['Current password is incorrect.']
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again.',
        ]);
    }


    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.',
        ]);
    }
}