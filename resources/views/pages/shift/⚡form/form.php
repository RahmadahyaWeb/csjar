<?php

use App\Models\Shift;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Shift $shift = null;

    public $name;

    public $code;

    public $start_time;

    public $end_time;

    public $is_overnight = false;

    public $tolerance_late = 0;

    public $tolerance_early_leave = 0;

    public function mount(?Shift $shift = null)
    {
        if ($shift && $shift->exists) {
            $this->authorizeUpdate($shift);

            $this->shift = $shift;

            $this->name = $shift->name;
            $this->code = $shift->code;
            $this->start_time = $shift->start_time;
            $this->end_time = $shift->end_time;
            $this->is_overnight = $shift->is_overnight;
            $this->tolerance_late = $shift->tolerance_late;
            $this->tolerance_early_leave = $shift->tolerance_early_leave;
        } else {
            $this->authorizeStore(Shift::class);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                $this->shift
                    ? 'unique:shifts,code,'.$this->shift->id
                    : 'unique:shifts,code',
            ],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'is_overnight' => ['required', 'boolean'],
            'tolerance_late' => ['nullable', 'integer', 'min:0'],
            'tolerance_early_leave' => ['nullable', 'integer', 'min:0'],
        ]);

        return $this->transaction(function () {

            $payload = [
                'name' => $this->name,
                'code' => $this->code,
                'start_time' => $this->start_time,
                'end_time' => $this->end_time,
                'is_overnight' => $this->is_overnight,
                'tolerance_late' => $this->tolerance_late,
                'tolerance_early_leave' => $this->tolerance_early_leave,
            ];

            if ($this->shift) {
                $this->authorizeUpdate($this->shift);

                $this->shift->update($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Shift updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(Shift::class);

                Shift::create($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Shift created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('shifts.index'), navigate: true);
        });
    }
};
