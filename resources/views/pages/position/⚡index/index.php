<?php

use App\Models\Position;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Positions')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(Position::class);
    }

    #[Computed()]
    public function positions()
    {
        return Position::with(['department', 'parent', 'head'])->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $position = Position::findOrFail($id);

        $this->authorizeDelete($position);

        $this->deleteId = $id;

        $this->modal('delete-position')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $position = Position::findOrFail($this->deleteId);

            $this->authorizeDelete($position);

            $position->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Position deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-position')->close();
        });
    }
};
