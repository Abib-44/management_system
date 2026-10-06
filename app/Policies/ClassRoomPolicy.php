<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ClassRoom;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ClassRoomPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ClassRoom');
    }

    public function view(AuthUser $authUser, ClassRoom $classRoom): bool
    {
        return $authUser->can('View:ClassRoom');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ClassRoom');
    }

    public function update(AuthUser $authUser, ClassRoom $classRoom): bool
    {
        return $authUser->can('Update:ClassRoom');
    }

    public function delete(AuthUser $authUser, ClassRoom $classRoom): bool
    {
        return $authUser->can('Delete:ClassRoom');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ClassRoom');
    }

    public function restore(AuthUser $authUser, ClassRoom $classRoom): bool
    {
        return $authUser->can('Restore:ClassRoom');
    }

    public function forceDelete(AuthUser $authUser, ClassRoom $classRoom): bool
    {
        return $authUser->can('ForceDelete:ClassRoom');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ClassRoom');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ClassRoom');
    }

    public function replicate(AuthUser $authUser, ClassRoom $classRoom): bool
    {
        return $authUser->can('Replicate:ClassRoom');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ClassRoom');
    }
}
