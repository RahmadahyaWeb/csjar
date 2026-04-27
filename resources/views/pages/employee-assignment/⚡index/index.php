<?php

use App\Models\EmployeeAssignment;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Employee Assignments')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(EmployeeAssignment::class);
    }

    #[Computed()]
    public function assignments()
    {
        return EmployeeAssignment::with([
            'user',
            'branch',
            'department',
            'position',
            'team',
        ])->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $assignment = EmployeeAssignment::findOrFail($id);

        $this->authorizeDelete($assignment);

        $this->deleteId = $id;

        $this->modal('delete-assignment')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $assignment = EmployeeAssignment::findOrFail($this->deleteId);

            $this->authorizeDelete($assignment);

            $assignment->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Assignment deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-assignment')->close();
        });
    }
};
