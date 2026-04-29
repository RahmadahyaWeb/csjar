<?php

use App\Models\Leave;
use App\Models\LeaveApproval;
use App\Models\LeaveBalance;
use App\Traits\AuthorizesCrud;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Leaves')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public function mount()
    {
        $this->authorizeIndex(Leave::class);
    }

    #[Computed()]
    public function leaves()
    {
        $user = Auth::user();

        return Leave::with(['user', 'approvals.approver'])
            ->when(! $user->hasRole('super_admin'), function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->orWhereHas('approvals', fn ($q) => $q->where('approver_id', $user->id));
            })
            ->latest()
            ->paginate(10);
    }

    #[Computed()]
    public function balances()
    {
        return LeaveBalance::where('user_id', Auth::id())
            ->where('year', now()->year)
            ->get();
    }

    public function approve(int $approvalId)
    {
        $this->transaction(function () use ($approvalId) {

            $approval = LeaveApproval::with('leave.approvals')->findOrFail($approvalId);

            $this->authorize('approve', $approval->leave);

            $blocked = $approval->leave->approvals()
                ->where('level', '<', $approval->level)
                ->where('status', '!=', 'approved')
                ->exists();

            if ($blocked) {
                throw new Exception('Approval must be sequential');
            }

            $approval->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            $leave = $approval->leave;

            $pending = $leave->approvals()->where('status', 'pending')->exists();

            if (! $pending) {

                $leave->update(['status' => 'approved']);

                $days = Carbon::parse($leave->start_date)
                    ->diffInDays(Carbon::parse($leave->end_date)) + 1;

                $balance = LeaveBalance::where('user_id', $leave->user_id)
                    ->where('type', $leave->type)
                    ->where('year', now()->year)
                    ->lockForUpdate()
                    ->first();

                if (! $balance || $balance->remaining < $days) {
                    throw new Exception('Insufficient leave balance');
                }

                $balance->increment('used', $days);
                $balance->decrement('remaining', $days);
            }

            Flux::toast(
                heading: 'Success',
                text: 'Leave approved',
                variant: 'success'
            );
        });
    }

    public function reject(int $approvalId)
    {
        $this->transaction(function () use ($approvalId) {

            $approval = LeaveApproval::with('leave')->findOrFail($approvalId);

            $this->authorize('approve', $approval->leave);

            $approval->update([
                'status' => 'rejected',
                'approved_at' => now(),
            ]);

            $approval->leave->update(['status' => 'rejected']);

            Flux::toast(
                heading: 'Rejected',
                text: 'Leave rejected',
                variant: 'danger'
            );
        });
    }
};
