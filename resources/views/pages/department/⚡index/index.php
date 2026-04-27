<?php

use App\Models\Department;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Departments')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(Department::class);
    }

    #[Computed()]
    public function departments()
    {
        return Department::with(['branch', 'parent', 'head'])->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $department = Department::findOrFail($id);

        $this->authorizeDelete($department);

        $this->deleteId = $id;

        $this->modal('delete-department')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $department = Department::findOrFail($this->deleteId);

            $this->authorizeDelete($department);

            $department->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Department deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-department')->close();
        });
    }
};
