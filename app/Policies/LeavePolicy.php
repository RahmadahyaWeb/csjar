<?php

namespace App\Policies;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('leave.view');

    }

    public function view(User $user, Leave $model): bool
    {
        return $user->can('leave.view');
    }

    public function create(User $user): bool
    {
        return $user->can('leave.create');
    }

    public function update(User $user, Leave $model): bool
    {
        return $user->can('leave.update');
    }

    public function delete(User $user, Leave $model): bool
    {
        return $user->can('leave.delete');
    }
}
