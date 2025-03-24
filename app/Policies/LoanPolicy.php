<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoanPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_loan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Loan $loan): bool
    {
        return $user->can('view_loan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_loan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Loan $loan): bool
    {
        return $user->can('update_loan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Loan $loan): bool
    {
        return $user->can('delete_loan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_loan');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Loan $loan): bool
    {
        return $user->can('restore_loan');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Loan $loan): bool
    {
        return $user->can('force_delete_loan');
    }

    public function validateReturn(User $user, Loan $loan): bool
    {
        if($user->hasRole('super_admin') || $user->hasRole('admin'))
        {
            return true;
        }

        return false;
    }

    public function return(User $user, Loan $loan): bool
    {
        if($user->hasRole('user'))
        {
            return $loan->status === 'in_progress';
        }

        return false;
    }
}
