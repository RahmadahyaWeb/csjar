<?php

namespace App\Policies;

use App\Models\EmployeeSchedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EmployeeSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('employee-schedule.view');

    }

    public function view(User $user, EmployeeSchedule $model): bool
    {
        return $user->can('employee-schedule.view');
    }

    public function create(User $user): bool
    {
        return $user->can('employee-schedule.create');
    }

    public function update(User $user, EmployeeSchedule $model): bool
    {
        return $user->can('employee-schedule.update');
    }

    public function delete(User $user, EmployeeSchedule $model): bool
    {
        return $user->can('employee-schedule.delete');
    }
}
