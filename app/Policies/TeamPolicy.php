<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('team.view');

    }

    public function view(User $user, Team $model): bool
    {
        return $user->can('team.view');
    }

    public function create(User $user): bool
    {
        return $user->can('team.create');
    }

    public function update(User $user, Team $model): bool
    {
        return $user->can('team.update');
    }

    public function delete(User $user, Team $model): bool
    {
        return $user->can('team.delete');
    }
}
