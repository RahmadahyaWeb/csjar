<?php

use App\Models\Team;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Teams')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(Team::class);
    }

    #[Computed()]
    public function teams()
    {
        return Team::with(['department', 'lead'])->paginate(10);
    }

    public function confirmDelete(int $id): void
    {
        $team = Team::findOrFail($id);

        $this->authorizeDelete($team);

        $this->deleteId = $id;

        $this->modal('delete-team')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {
            $team = Team::findOrFail($this->deleteId);

            $this->authorizeDelete($team);

            $team->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Team deleted successfully',
                variant: 'success'
            );

            $this->modal('delete-team')->close();
        });
    }
};
