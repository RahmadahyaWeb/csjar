<?php

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('My Attendance History')] class extends Component
{
    use WithoutUrlPagination, WithPagination;

    public $startDate;

    public $endDate;

    public $status;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->toDateString();
    }

    #[Computed]
    public function attendances()
    {
        return Attendance::query()
            ->where('user_id', Auth::id())
            ->when($this->startDate, fn ($q) => $q->whereDate('date', '>=', $this->startDate))
            ->when($this->endDate, fn ($q) => $q->whereDate('date', '<=', $this->endDate))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->latest('date')
            ->paginate(10);
    }
};
