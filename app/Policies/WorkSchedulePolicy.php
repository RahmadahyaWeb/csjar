<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkSchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('work-schedule.view');

    }

    public function view(User $user, WorkSchedule $model): bool
    {
        return $user->can('work-schedule.view');
    }

    public function create(User $user): bool
    {
        return $user->can('work-schedule.create');
    }

    public function update(User $user, WorkSchedule $model): bool
    {
        return $user->can('work-schedule.update');
    }

    public function delete(User $user, WorkSchedule $model): bool
    {
        return $user->can('work-schedule.delete');
    }
}
