<?php

use App\Models\Branch;
use App\Models\Department;
use App\Models\EmployeeAssignment;
use App\Models\Position;
use App\Models\Team;
use App\Models\User;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?EmployeeAssignment $assignment = null;

    public $user_id;

    public $branch_id;

    public $department_id;

    public $position_id;

    public $team_id;

    public $start_date;

    public $end_date;

    public $is_active = true;

    public $users = [];

    public $branches = [];

    public $departments = [];

    public $positions = [];

    public $teams = [];

    public function mount(?EmployeeAssignment $assignment = null)
    {
        $this->users = User::pluck('name', 'id');
        $this->branches = Branch::pluck('name', 'id');
        $this->departments = Department::pluck('name', 'id');
        $this->positions = Position::pluck('name', 'id');
        $this->teams = Team::pluck('name', 'id');

        if ($assignment && $assignment->exists) {
            $this->authorizeUpdate($assignment);

            $this->assignment = $assignment;

            $this->user_id = $assignment->user_id;
            $this->branch_id = $assignment->branch_id;
            $this->department_id = $assignment->department_id;
            $this->position_id = $assignment->position_id;
            $this->team_id = $assignment->team_id;
            $this->start_date = $assignment->start_date?->format('Y-m-d');
            $this->end_date = $assignment->end_date?->format('Y-m-d');
            $this->is_active = $assignment->is_active;
        } else {
            $this->authorizeStore(EmployeeAssignment::class);

            $this->start_date = now()->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate([
            'user_id' => ['required', 'exists:users,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['required', 'boolean'],
        ]);

        return $this->transaction(function () {

            $payload = [
                'user_id' => $this->user_id,
                'branch_id' => $this->branch_id,
                'department_id' => $this->department_id,
                'position_id' => $this->position_id,
                'team_id' => $this->team_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'is_active' => $this->is_active,
            ];

            if ($this->assignment) {
                $this->authorizeUpdate($this->assignment);

                $this->assignment->update($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Assignment updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(EmployeeAssignment::class);

                EmployeeAssignment::create($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Assignment created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('employee-assignments.index'), navigate: true);
        });
    }
};
