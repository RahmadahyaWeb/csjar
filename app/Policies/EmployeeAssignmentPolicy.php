<?php

namespace App\Policies;

use App\Models\EmployeeAssignment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeAssignmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('employee-assignment.view');

    }

    public function view(User $user, EmployeeAssignment $model): bool
    {
        return $user->can('employee-assignment.view');
    }

    public function create(User $user): bool
    {
        return $user->can('employee-assignment.create');
    }

    public function update(User $user, EmployeeAssignment $model): bool
    {
        return $user->can('employee-assignment.update');
    }

    public function delete(User $user, EmployeeAssignment $model): bool
    {
        return $user->can('employee-assignment.delete');
    }
}
