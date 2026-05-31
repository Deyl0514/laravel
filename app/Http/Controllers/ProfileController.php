<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = auth()->user()->fresh();
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:500',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')
                ->store('profile-pictures', 'public');
        } else {
            unset($validated['profile_picture']);
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
        ] + (isset($validated['profile_picture'])
            ? ['profile_picture' => $validated['profile_picture']]
            : []))->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user->fresh(),
        ]);
    }
}
