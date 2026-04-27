<?php

use App\Models\WorkSchedule;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Work Schedules')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(WorkSchedule::class);
    }

    #[Computed()]
    public function schedules()
    {
        return WorkSchedule::with(['days.shift'])->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $schedule = WorkSchedule::findOrFail($id);

        $this->authorizeDelete($schedule);

        $this->deleteId = $id;

        $this->modal('delete-schedule')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $schedule = WorkSchedule::findOrFail($this->deleteId);

            $this->authorizeDelete($schedule);

            $schedule->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Work schedule deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-schedule')->close();
        });
    }

    public function getDayLabel($day)
    {
        return [
            0 => 'Sun',
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
        ][$day] ?? '-';
    }
};
