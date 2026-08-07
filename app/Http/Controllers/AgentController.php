<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        $agents = $query->orderBy('created_at', 'desc')->with('permissions')->get();
        
        $roles = Cache::rememberForever('roles', function () {
            return Role::orderBy('name')->get(); // Agent view doesn't need to load Role permissions necessarily, but we'll fetch them normally.
        });
        
        $permissions = Cache::rememberForever('permissions', function () {
            return Permission::orderBy('name')->get();
        });

        return view('agents', compact('agents', 'roles', 'permissions'));
    }

    /**
     * Store a newly created Agent / CRM team member (Admin Only).
     */
    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage-agents')) {
            abort(403, 'Unauthorized. Only administrators can create new agent accounts.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'role' => 'required|string|exists:roles,slug',
            'password' => 'required|string|min:6',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $agent = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'status' => 'offline',
            'avatar' => 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($validated['name']),
        ]);

        if (isset($validated['permissions'])) {
            $agent->permissions()->sync($validated['permissions']);
        }

        return redirect()->route('agents.index')->with('success', "Agent {$agent->name} created successfully!");
    }

    /**
     * Update an agent's profile details (Admin Only).
     */
    public function update(Request $request, User $agent)
    {
        if (!auth()->user()->hasPermissionTo('manage-agents')) {
            abort(403, 'Unauthorized. Only administrators can update other agent profiles.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $agent->id,
            'role' => 'required|string|exists:roles,slug',
            'password' => 'nullable|string|min:6',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Prevent admin from removing their own admin role
        if (auth()->id() === $agent->id && $validated['role'] !== 'admin') {
            return back()->withErrors(['error' => 'You cannot remove administrator privileges from your own account.']);
        }

        $agent->name = $validated['name'];
        $agent->email = strtolower($validated['email']);
        $agent->role = $validated['role'];

        if (!empty($validated['password'])) {
            $agent->password = Hash::make($validated['password']);
        }

        $agent->avatar = 'https://api.dicebear.com/7.x/avataaars/svg?seed=' . urlencode($validated['name']);
        $agent->save();

        $agent->permissions()->sync($validated['permissions'] ?? []);

        $agent->flushUserCache();

        return redirect()->route('agents.index')->with('success', "Profile for {$agent->name} updated successfully!");
    }

    /**
     * Toggle Agent Account Status (Admin Only).
     */
    public function toggleStatus(User $agent)
    {
        if (!auth()->user()->hasPermissionTo('manage-agents')) {
            abort(403, 'Unauthorized. Only administrators can change agent account status.');
        }

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
     * Delete Agent Account (Admin Only).
     */
    public function destroy(User $agent)
    {
        if (!auth()->user()->hasPermissionTo('manage-agents')) {
            abort(403, 'Unauthorized. Only administrators can delete agent accounts.');
        }

        if (auth()->id() === $agent->id) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        // Invalidate sessions before deletion
        DB::table('sessions')->where('user_id', $agent->id)->delete();
        $agent->delete();

        return redirect()->route('agents.index')->with('success', "Agent account deleted successfully.");
    }
}
