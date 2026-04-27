<?php

namespace App\Policies;

use App\Models\Holiday;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class HolidayPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('holiday.view');

    }

    public function view(User $user, Holiday $model): bool
    {
        return $user->can('holiday.view');
    }

    public function create(User $user): bool
    {
        return $user->can('holiday.create');
    }

    public function update(User $user, Holiday $model): bool
    {
        return $user->can('holiday.update');
    }

    public function delete(User $user, Holiday $model): bool
    {
        return $user->can('holiday.delete');
    }
}
