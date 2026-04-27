<?php

namespace App\Policies;

use App\Models\BreakRule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BreakRulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('break-rule.view');

    }

    public function view(User $user, BreakRule $model): bool
    {
        return $user->can('break-rule.view');
    }

    public function create(User $user): bool
    {
        return $user->can('break-rule.create');
    }

    public function update(User $user, BreakRule $model): bool
    {
        return $user->can('break-rule.update');
    }

    public function delete(User $user, BreakRule $model): bool
    {
        return $user->can('break-rule.delete');
    }
}
