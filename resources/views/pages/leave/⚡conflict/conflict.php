<?php

use App\Models\EmployeeAssignment;
use App\Models\Leave;
use App\Traits\AuthorizesCrud;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Leave Conflicts')] class extends Component
{
    use AuthorizesCrud;

    public $department_id = null;

    public $position_id = null;

    public function mount()
    {
        $this->authorizeIndex(Leave::class);
    }

    #[Computed()]
    public function conflicts()
    {
        $leaves = Leave::with(['user'])
            ->where('status', 'pending')
            ->get();

        return $leaves->filter(function ($leave) use ($leaves) {

            $assignment = EmployeeAssignment::where('user_id', $leave->user_id)
                ->where('is_active', true)
                ->first();

            if (! $assignment) {
                return false;
            }

            // filter by selected department / position
            if ($this->department_id && $assignment->department_id != $this->department_id) {
                return false;
            }

            if ($this->position_id && $assignment->position_id != $this->position_id) {
                return false;
            }

            return $leaves->filter(function ($other) use ($leave) {

                if ($other->id === $leave->id) {
                    return false;
                }

                $otherAssignment = EmployeeAssignment::where('user_id', $other->user_id)
                    ->where('is_active', true)
                    ->first();

                if (! $otherAssignment) {
                    return false;
                }

                // SAME DEPARTMENT
                if ($this->department_id &&
                    $otherAssignment->department_id != $this->department_id) {
                    return false;
                }

                // SAME POSITION
                if ($this->position_id &&
                    $otherAssignment->position_id != $this->position_id) {
                    return false;
                }

                return ! (
                    $leave->end_date < $other->start_date ||
                    $leave->start_date > $other->end_date
                );

            })->count() > 0;
        });
    }
};
