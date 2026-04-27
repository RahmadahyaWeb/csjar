<?php

use App\Models\BreakRule;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

new #[Title('Break Rules')] class extends Component
{
    use AuthorizesCrud;
    use WithoutUrlPagination, WithPagination;

    public $deleteId;

    public function mount()
    {
        $this->authorizeIndex(BreakRule::class);
    }

    #[Computed()]
    public function breakRules()
    {
        return BreakRule::with('shift')->latest()->paginate(10);
    }

    public function confirmDelete(int $id)
    {
        $rule = BreakRule::findOrFail($id);

        $this->authorizeDelete($rule);

        $this->deleteId = $id;

        $this->modal('delete-break-rule')->show();
    }

    public function destroy()
    {
        $this->transaction(function () {

            $rule = BreakRule::findOrFail($this->deleteId);

            $this->authorizeDelete($rule);

            $rule->delete();

            Flux::toast(
                heading: 'Success',
                text: 'Break rule deleted',
                variant: 'success'
            );

            $this->modal('delete-break-rule')->close();
        });
    }
};
