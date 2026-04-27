<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('department.view');

    }

    public function view(User $user, Department $model): bool
    {
        return $user->can('department.view');
    }

    public function create(User $user): bool
    {
        return $user->can('department.create');
    }

    public function update(User $user, Department $model): bool
    {
        return $user->can('department.update');
    }

    public function delete(User $user, Department $model): bool
    {
        return $user->can('department.delete');
    }
}
