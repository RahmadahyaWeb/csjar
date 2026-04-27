<?php

use App\Models\Holiday;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Holidays')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(Holiday::class);
    }

    #[Computed()]
    public function holidays()
    {
        return Holiday::orderBy('date', 'desc')->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $holiday = Holiday::findOrFail($id);

        $this->authorizeDelete($holiday);

        $this->deleteId = $id;

        $this->modal('delete-holiday')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $holiday = Holiday::findOrFail($this->deleteId);

            $this->authorizeDelete($holiday);

            $holiday->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Holiday deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-holiday')->close();
        });
    }
};
