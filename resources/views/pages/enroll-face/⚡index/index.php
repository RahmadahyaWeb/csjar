<?php

use App\Models\UserFace;
use Livewire\Component;

new class extends Component
{
    public $hasFace = false;

    public function mount()
    {
        $this->hasFace = UserFace::where('user_id', auth()->id())->exists();
    }

    public function refreshFaceStatus()
    {
        $this->hasFace = true;
        $this->dispatch('face-registered');
    }

    public function resetFace()
    {
        UserFace::where('user_id', auth()->id())->delete();

        $this->hasFace = false;

        $this->dispatch('face-reset');
    }
};
