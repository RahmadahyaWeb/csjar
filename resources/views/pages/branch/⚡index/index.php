<?php

use App\Models\Branch;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Branches')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(Branch::class);
    }

    #[Computed()]
    public function branches()
    {
        return Branch::paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $branch = Branch::findOrFail($id);

        $this->authorizeDelete($branch);

        $this->deleteId = $id;

        $this->modal('delete-branch')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $branch = Branch::findOrFail($this->deleteId);

            $this->authorizeDelete($branch);

            $branch->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Branch deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-branch')->close();
        });
    }
};
