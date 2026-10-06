<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MembershipFee;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MembershipFeePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:MembershipFee');
    }

    public function view(AuthUser $authUser, MembershipFee $membershipFee): bool
    {
        return $authUser->can('View:MembershipFee');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:MembershipFee');
    }

    public function update(AuthUser $authUser, MembershipFee $membershipFee): bool
    {
        return $authUser->can('Update:MembershipFee');
    }

    public function delete(AuthUser $authUser, MembershipFee $membershipFee): bool
    {
        return $authUser->can('Delete:MembershipFee');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:MembershipFee');
    }

    public function restore(AuthUser $authUser, MembershipFee $membershipFee): bool
    {
        return $authUser->can('Restore:MembershipFee');
    }

    public function forceDelete(AuthUser $authUser, MembershipFee $membershipFee): bool
    {
        return $authUser->can('ForceDelete:MembershipFee');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:MembershipFee');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:MembershipFee');
    }

    public function replicate(AuthUser $authUser, MembershipFee $membershipFee): bool
    {
        return $authUser->can('Replicate:MembershipFee');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:MembershipFee');
    }
}
