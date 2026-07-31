<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    /**
     * Display a listing of agents & CRM team members.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $agents = $query->orderBy('created_at', 'desc')->get();

        return view('agents', compact('agents'));
    }

    /**
     * Store a newly created Agent / CRM team member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|string|in:agent,supervisor,admin',
            'password' => 'required|string|min:6',
        ]);

        $agent = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'offline',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($validated['name']),
        ]);

        return redirect()->route('agents.index')->with('success', "Agent {$agent->name} created successfully!");
    }

    /**
     * Toggle Agent Account Status (Activate / Deactivate & Invalidate Sessions).
     */
    public function toggleStatus(User $agent)
    {
        // Prevent admin deactivating their own active account
        if (auth()->id() === $agent->id) {
            return back()->withErrors(['error' => 'You cannot deactivate your own active session account.']);
        }

        if ($agent->status === 'disabled' || $agent->status === 'inactive') {
            // Reactivate Agent
            $agent->status = 'offline';
            $agent->save();
            $agent->flushUserCache();

            return redirect()->route('agents.index')->with('success', "Account for {$agent->name} reactivated successfully.");
        }

        // Deactivate Agent & Instantly Invalidate Session
        $agent->status = 'disabled';
        $agent->remember_token = Str::random(60); // Invalidate Remember Me Cookie
        $agent->save();

        // Instantly purge all active sessions from Database for this user
        DB::table('sessions')->where('user_id', $agent->id)->delete();

        $agent->flushUserCache();

        return redirect()->route('agents.index')->with('success', "Account for {$agent->name} has been deactivated. All active sessions invalidated instantly!");
    }

    /**
     * Delete Agent Account
     */
    public function destroy(User $agent)
    {
        if (auth()->id() === $agent->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // Invalidate sessions before deletion
        DB::table('sessions')->where('user_id', $agent->id)->delete();
        $agent->delete();

        return redirect()->route('agents.index')->with('success', "Agent account deleted successfully.");
    }
}
