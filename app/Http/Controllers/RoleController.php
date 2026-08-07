<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermissionTo('manage-roles')) {
            abort(403, 'Unauthorized action.');
        }

        $roles = Cache::rememberForever('roles', function () {
            return Role::orderBy('name')->with('permissions')->get();
        });
        
        $permissions = Cache::rememberForever('permissions', function () {
            return Permission::orderBy('name')->get();
        });
        
        return view('roles', compact('roles', 'permissions'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermissionTo('manage-roles')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        if (isset($validated['permissions'])) {
            $role->permissions()->sync($validated['permissions']);
        }

        Cache::forget('roles');

        return redirect()->back()->with('success', 'Role created successfully.');
    }

    public function update(Request $request, Role $role)
    {
        if (!auth()->user()->hasPermissionTo('manage-roles')) {
            abort(403, 'Unauthorized action.');
        }

        // Prevent modifying the default admin role
        if ($role->slug === 'admin') {
            return back()->withErrors(['error' => 'You cannot modify the core administrator role.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        $role->permissions()->sync($validated['permissions'] ?? []);

        Cache::forget('roles');

        return redirect()->back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (!auth()->user()->hasPermissionTo('manage-roles')) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($role->slug, ['admin', 'supervisor', 'agent'])) {
            return back()->withErrors(['error' => 'You cannot delete core system roles.']);
        }

        $role->delete();

        Cache::forget('roles');

        return redirect()->back()->with('success', 'Role deleted successfully.');
    }
}
