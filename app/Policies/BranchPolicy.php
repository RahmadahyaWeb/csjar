<?php

namespace App\Policies;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('branch.view');

    }

    public function view(User $user, Branch $model): bool
    {
        return $user->can('branch.view');
    }

    public function create(User $user): bool
    {
        return $user->can('branch.create');
    }

    public function update(User $user, Branch $model): bool
    {
        return $user->can('branch.update');
    }

    public function delete(User $user, Branch $model): bool
    {
        return $user->can('branch.delete');
    }
}
