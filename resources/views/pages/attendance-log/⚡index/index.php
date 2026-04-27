<?php

use App\Models\AttendanceLog;
use App\Traits\AuthorizesCrud;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Attendance Logs')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public function mount()
    {
        $this->authorizeIndex(AttendanceLog::class);
    }

    #[Computed()]
    public function logs()
    {
        return AttendanceLog::with('user')
            ->latest('recorded_at')
            ->paginate(10);
    }
};
