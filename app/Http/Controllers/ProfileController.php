<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the user profile management page.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile', compact('user'));
    }

    /**
     * Update the logged-in user's profile information (Name & Email).
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        $user->avatar = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($validated['name']);
        $user->save();
        $user->flushUserCache();

        return redirect()->route('profile.edit')->with('success', 'Profile information updated successfully!');
    }

    /**
     * Update logged-in user's active status (online, away, busy, offline).
     */
    public function updateStatus(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'status' => 'required|string|in:online,away,busy,offline',
        ]);

        $user->status = $validated['status'];
        $user->save();
        $user->flushUserCache();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Status updated to " . ucfirst($user->status),
                'status' => $user->status,
            ]);
        }

        return back()->with('success', "Status set to " . ucfirst($user->status) . "!");
    }

    /**
     * Update logged-in user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided current password does not match your existing password.'
            ])->withInput();
        }

        $user->password = Hash::make($validated['password']);
        $user->save();
        $user->flushUserCache();

        return redirect()->route('profile.edit')->with('success', 'Password updated successfully! Your new password is now active.');
    }
}
