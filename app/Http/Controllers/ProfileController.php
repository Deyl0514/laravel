<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->gender = $validated['gender'] ?? null;
        $user->address = $validated['address'] ?? null;

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');

            if (! $file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Upload failed: ' . $file->getErrorMessage(),
                ], 422);
            }

            try {
                $disk = Storage::disk('public');

                if ($user->profile_picture && $disk->exists($user->profile_picture)) {
                    $disk->delete($user->profile_picture);
                }

                $path = $file->store('profile-pictures', 'public');

                if (! $path) {
                    throw new \RuntimeException('Storage::store() returned false');
                }

                $this->ensurePublicStorageLink();

                $user->profile_picture = $path;
            } catch (\Throwable $e) {
                Log::error('Profile picture upload failed', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'storage_path' => storage_path('app/public'),
                    'storage_writable' => is_writable(storage_path('app/public')),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Could not save profile picture: ' . $e->getMessage(),
                ], 500);
            }
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'user' => $user->fresh(),
        ]);
    }

    private function ensurePublicStorageLink(): void
    {
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (file_exists($link) || is_link($link)) {
            return;
        }

        try {
            if (function_exists('symlink')) {
                @symlink($target, $link);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not create storage symlink: ' . $e->getMessage());
        }
    }
}
