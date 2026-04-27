<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PositionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('position.view');

    }

    public function view(User $user, Position $model): bool
    {
        return $user->can('position.view');
    }

    public function create(User $user): bool
    {
        return $user->can('position.create');
    }

    public function update(User $user, Position $model): bool
    {
        return $user->can('position.update');
    }

    public function delete(User $user, Position $model): bool
    {
        return $user->can('position.delete');
    }
}
