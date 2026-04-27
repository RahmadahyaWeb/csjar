<?php

namespace App\Policies;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShiftPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('shift.view');

    }

    public function view(User $user, Shift $model): bool
    {
        return $user->can('shift.view');
    }

    public function create(User $user): bool
    {
        return $user->can('shift.create');
    }

    public function update(User $user, Shift $model): bool
    {
        return $user->can('shift.update');
    }

    public function delete(User $user, Shift $model): bool
    {
        return $user->can('shift.delete');
    }
}
