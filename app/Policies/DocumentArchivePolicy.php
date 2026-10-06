<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\DocumentArchive;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class DocumentArchivePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:DocumentArchive');
    }

    public function view(AuthUser $authUser, DocumentArchive $documentArchive): bool
    {
        return $authUser->can('View:DocumentArchive');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:DocumentArchive');
    }

    public function update(AuthUser $authUser, DocumentArchive $documentArchive): bool
    {
        return $authUser->can('Update:DocumentArchive');
    }

    public function delete(AuthUser $authUser, DocumentArchive $documentArchive): bool
    {
        return $authUser->can('Delete:DocumentArchive');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:DocumentArchive');
    }

    public function restore(AuthUser $authUser, DocumentArchive $documentArchive): bool
    {
        return $authUser->can('Restore:DocumentArchive');
    }

    public function forceDelete(AuthUser $authUser, DocumentArchive $documentArchive): bool
    {
        return $authUser->can('ForceDelete:DocumentArchive');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:DocumentArchive');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:DocumentArchive');
    }

    public function replicate(AuthUser $authUser, DocumentArchive $documentArchive): bool
    {
        return $authUser->can('Replicate:DocumentArchive');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:DocumentArchive');
    }
}
