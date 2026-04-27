<?php

use App\Models\EmployeeSchedule;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Employee Schedules')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(EmployeeSchedule::class);
    }

    #[Computed()]
    public function schedules()
    {
        return EmployeeSchedule::with(['user', 'workSchedule'])->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $schedule = EmployeeSchedule::findOrFail($id);

        $this->authorizeDelete($schedule);

        $this->deleteId = $id;

        $this->modal('delete-employee-schedule')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $schedule = EmployeeSchedule::findOrFail($this->deleteId);

            $this->authorizeDelete($schedule);

            $schedule->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Employee schedule deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-employee-schedule')->close();
        });
    }
};
