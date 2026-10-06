<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceAssignment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ServiceAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ServiceAssignment');
    }

    public function view(AuthUser $authUser, ServiceAssignment $serviceAssignment): bool
    {
        return $authUser->can('View:ServiceAssignment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ServiceAssignment');
    }

    public function update(AuthUser $authUser, ServiceAssignment $serviceAssignment): bool
    {
        return $authUser->can('Update:ServiceAssignment');
    }

    public function delete(AuthUser $authUser, ServiceAssignment $serviceAssignment): bool
    {
        return $authUser->can('Delete:ServiceAssignment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ServiceAssignment');
    }

    public function restore(AuthUser $authUser, ServiceAssignment $serviceAssignment): bool
    {
        return $authUser->can('Restore:ServiceAssignment');
    }

    public function forceDelete(AuthUser $authUser, ServiceAssignment $serviceAssignment): bool
    {
        return $authUser->can('ForceDelete:ServiceAssignment');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ServiceAssignment');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ServiceAssignment');
    }

    public function replicate(AuthUser $authUser, ServiceAssignment $serviceAssignment): bool
    {
        return $authUser->can('Replicate:ServiceAssignment');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ServiceAssignment');
    }
}
