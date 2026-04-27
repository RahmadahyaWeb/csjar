<?php

use App\Models\Shift;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Shifts')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(Shift::class);
    }

    #[Computed()]
    public function shifts()
    {
        return Shift::paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $shift = Shift::findOrFail($id);

        $this->authorizeDelete($shift);

        $this->deleteId = $id;

        $this->modal('delete-shift')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $shift = Shift::findOrFail($this->deleteId);

            $this->authorizeDelete($shift);

            $shift->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Shift deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-shift')->close();
        });
    }
};
