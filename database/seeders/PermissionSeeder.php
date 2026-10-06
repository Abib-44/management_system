<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Teaching
            'teaching.view',
            'teaching.create',
            'teaching.edit',
            'teaching.delete',

            // Members
            'members.view',
            'members.create',
            'members.edit',
            'members.delete',

            // Services
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',

            // Finance
            'finance.view',
            'finance.create',
            'finance.edit',
            'finance.delete',

            // Documents
            'documents.view',
            'documents.create',
            'documents.edit',
            'documents.delete',

            // System - Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',

            // System - Audit Logs
            'audit_logs.view',

            // System - Activities
            'activities.view',
            'activities.create',
            'activities.edit',
            'activities.delete',

            // System - Backup
            'backup.view',
            'backup.create',
            'backup.delete',

            // System - Permissions
            'permissions.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions(Permission::all());
    }
}
