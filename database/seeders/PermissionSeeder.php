<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Core Permissions
        $permissions = [
            'manage-agents' => 'Manage Agents',
            'manage-roles' => 'Manage Roles',
            'manage-canned-responses' => 'Manage Canned Responses',
            'view-reports' => 'View Reports & Analytics',
            'delete-tickets' => 'Delete Tickets',
            'assign-tickets' => 'Assign Tickets',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }

        // 2. Assign Baseline Permissions to Roles
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->syncWithoutDetaching($allPermissions);
        }

        $supervisorRole = Role::where('slug', 'supervisor')->first();
        
        if ($supervisorRole) {
            $supervisorPermissions = Permission::whereIn('slug', [
                'manage-canned-responses',
                'view-reports',
                'assign-tickets',
            ])->get();
            
            $supervisorRole->permissions()->syncWithoutDetaching($supervisorPermissions);
        }

        // 3. Assign Special Direct Permissions to Individual Agents
        // Let's grab the agent we created in OmnichannelSeeder (Sarah Connor)
        $agentSarah = User::where('email', 'sarah@helpdesk.com')->first();
        
        if ($agentSarah) {
            $specialPermission = Permission::where('slug', 'view-reports')->first();
            if ($specialPermission) {
                // Sarah gets to view reports even though she is just an agent
                $agentSarah->permissions()->syncWithoutDetaching([$specialPermission->id]);
            }
        }
    }
}
