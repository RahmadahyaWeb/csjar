<?php

use App\Models\Department;
use App\Models\EmployeeAssignment;
use App\Models\Leave;
use App\Models\LeaveApproval;
use App\Models\Position;
use App\Models\User;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Leave $leave = null;

    public $type = 'annual';

    public $start_date;

    public $end_date;

    public $reason;

    public $previewApprovers = [];

    public function mount(?Leave $leave = null)
    {
        if ($leave) {
            $leave = Leave::with('approvals.approver')->findOrFail($leave->id);

            $this->authorizeUpdate($leave);

            $this->leave = $leave;

            $this->type = $leave->type;
            $this->start_date = $leave->start_date;
            $this->end_date = $leave->end_date;
            $this->reason = $leave->reason;

            return;
        }

        $this->authorizeStore(Leave::class);

        $this->generatePreviewApprovers();
    }

    public function generatePreviewApprovers()
    {
        $assignment = EmployeeAssignment::where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (! $assignment) {
            return;
        }

        $approvers = collect();

        $position = Position::with('parent')->find($assignment->position_id);
        if ($position?->parent?->head_user_id) {
            $approvers->push($position->parent->head_user_id);
        }

        $department = Department::find($assignment->department_id);
        if ($department?->head_user_id) {
            $approvers->push($department->head_user_id);
        }

        // ambil HR Manager (level 1)
        $hrManagerPosition = Position::where('code', 'HR-H')->first();

        $hrManagerUserId = EmployeeAssignment::where('position_id', $hrManagerPosition->id)
            ->where('is_active', true)
            ->value('user_id');

        if ($hrManagerUserId) {
            $approvers->push($hrManagerUserId);
        }

        $this->previewApprovers = User::whereIn('id', $approvers->unique())->pluck('name')->toArray();
    }

    public function save()
    {
        $this->validate([
            'type' => ['required'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        return $this->transaction(function () {

            $leave = Leave::create([
                'user_id' => Auth::id(),
                'type' => $this->type,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'reason' => $this->reason,
            ]);

            $this->generatePreviewApprovers();

            foreach ($this->previewApprovers as $index => $name) {
                $user = User::where('name', $name)->first();

                LeaveApproval::create([
                    'leave_id' => $leave->id,
                    'approver_id' => $user->id,
                    'level' => $index + 1,
                ]);
            }

            Flux::toast(
                heading: 'Success',
                text: 'Leave submitted',
                variant: 'success'
            );

            $this->redirect(route('leaves.index'), navigate: true);
        });
    }
};
