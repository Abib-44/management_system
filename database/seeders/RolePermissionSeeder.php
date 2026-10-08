<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    private const ROLES = [
        'super_admin',
        'admin',
        'secretary',
        'treasurer',
        'teacher',
        'services',
    ];

    /**
     * Admin: gestione completa (CRUD) di tutte le risorse.
     */
    private const ADMIN_RESOURCES = [
        'User',
        'Student',
        'Member',
        'Activity',
        'Attendance',
        'ClassRoom',
        'Lesson',
        'Grade',
        'Material',
        'SchoolYear',
        'MembershipFee',
        'StudentPayment',
        'FinancialCategory',
        'FinancialTransaction',
        'DocumentArchive',
        'ServiceAssignment',
    ];

    /**
     * Permessi per ruolo:
     *  - manage: CRUD completo (ViewAny, View, Create, Update, Delete)
     *  - view:   sola lettura (ViewAny, View)
     */
    private const ROLE_RESOURCES = [
        // Ambito: Membri + Documenti
        'secretary' => [
            'manage' => [
                'Member',
                'MembershipFee',
                'DocumentArchive',
            ],
            'view' => [
                'Student',
                'Activity',
                'Attendance',
                'ClassRoom',
                'SchoolYear',
            ],
        ],

        // Ambito: Finanze
        'treasurer' => [
            'manage' => [
                'FinancialCategory',
                'FinancialTransaction',
                'StudentPayment',
                'MembershipFee',
            ],
            'view' => [
                'DocumentArchive',
                'Student',
                'Member',
            ],
        ],

        // Ambito: Didattica (tutto)
        'teacher' => [
            'manage' => [
                'Student',
                'ClassRoom',
                'Lesson',
                'Grade',
                'Attendance',
                'Material',
                'SchoolYear',
                'TeachingDashboard',
                'FinancialTransaction',
            ],
            'view' => [
                'Activity',
            ],
        ],

        // Ambito: Servizi
        'services' => [
            'manage' => [
                'ServiceAssignment',
                'Activity',
            ],
        ],
    ];

    private const PAGE_PERMISSIONS = [
        'admin' => [
            'View:Dashboard',
            'View:FinanceDashboard',
            'View:SchoolFinanceDashboard',
            'View:TeachingDashboard',
            'View:MembersDashboard',
            'View:ServicesDashboard',
            'View:DocumentsDashboard',
        ],

        'secretary' => [
            'View:Dashboard',
            'View:MembersDashboard',
            'View:DocumentsDashboard',
        ],

        'treasurer' => [
            'View:Dashboard',
            'View:FinanceDashboard',
            'View:SchoolFinanceDashboard',
            'View:DocumentsDashboard',
        ],

        'teacher' => [
            'View:Dashboard',
            'View:TeachingDashboard',
            'View:TeachingAgenda',
            'View:SchoolFinanceDashboard',
        ],

        'services' => [
            'View:Dashboard',
            'View:ServicesDashboard',
        ],
    ];

    public function run(): void
    {
        Filament::setCurrentPanel('admin');

        $this->seedPermissions();
        $this->seedRoles();
        $this->assignRolePermissions();
    }

    private function seedPermissions(): void
    {
        $permissions = FilamentShield::getEntitiesPermissions();

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Permessi pagina non rilevati automaticamente da Shield:
        // li creiamo comunque, così syncPagePermissions() li trova.
        foreach (self::PAGE_PERMISSIONS as $pagePermissions) {
            foreach ($pagePermissions as $permission) {
                Permission::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => 'web',
                ]);
            }
        }
    }

    private function seedRoles(): void
    {
        foreach (self::ROLES as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'web',
            ]);
        }
    }

    private function assignRolePermissions(): void
    {
        $this->syncSuperAdmin();
        $this->syncAdmin();
        $this->syncDomainRoles();
        $this->syncPagePermissions();
    }

    private function syncSuperAdmin(): void
    {
        $this->role('super_admin')->syncPermissions(
            Permission::query()
                ->where('guard_name', 'web')
                ->get()
        );
    }

    private function syncAdmin(): void
    {
        $permissions = [];

        foreach (self::ADMIN_RESOURCES as $resource) {
            $permissions = [...$permissions, ...$this->crud($resource)];
        }

        $this->sync('admin', $permissions);
    }

    private function syncDomainRoles(): void
    {
        foreach (self::ROLE_RESOURCES as $role => $groups) {
            $permissions = [];

            foreach ($groups['view'] ?? [] as $resource) {
                $permissions = [...$permissions, ...$this->view($resource)];
            }

            // manage dopo view: nessun conflitto, array_unique gestisce i duplicati
            foreach ($groups['manage'] ?? [] as $resource) {
                $permissions = [...$permissions, ...$this->crud($resource)];
            }

            $this->sync($role, $permissions);
        }
    }

    private function syncPagePermissions(): void
    {
        foreach (self::PAGE_PERMISSIONS as $role => $permissions) {
            $this->role($role)->givePermissionTo(
                $this->existingPermissions($permissions)
            );
        }
    }

    private function crud(string $resource): array
    {
        return $this->existingPermissions([
            "ViewAny:{$resource}",
            "View:{$resource}",
            "Create:{$resource}",
            "Update:{$resource}",
            "Delete:{$resource}",
        ]);
    }

    private function view(string $resource): array
    {
        return $this->existingPermissions([
            "ViewAny:{$resource}",
            "View:{$resource}",
        ]);
    }

    private function existingPermissions(array $permissions): array
    {
        return Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $permissions)
            ->pluck('name')
            ->all();
    }

    private function sync(string $role, array $permissions): void
    {
        $this->role($role)->syncPermissions(
            array_values(array_unique($permissions))
        );
    }

    private function role(string $role): Role
    {
        return Role::findByName($role, 'web');
    }
}