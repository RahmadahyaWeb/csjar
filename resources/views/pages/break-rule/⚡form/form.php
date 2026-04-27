<?php

use App\Models\BreakRule;
use App\Models\Shift;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?BreakRule $rule = null;

    public $shift_id;

    public $name;

    public $start_time;

    public $end_time;

    public $duration_minutes;

    public $is_paid = true;

    public $is_flexible = false;

    public $shifts = [];

    public function mount(?int $rule = null)
    {
        $this->shifts = Shift::pluck('name', 'id');

        if ($rule) {
            $rule = BreakRule::findOrFail($rule);

            $this->authorizeUpdate($rule);

            $this->rule = $rule;

            $this->shift_id = $rule->shift_id;
            $this->name = $rule->name;
            $this->start_time = $rule->start_time;
            $this->end_time = $rule->end_time;
            $this->duration_minutes = $rule->duration_minutes;
            $this->is_paid = $rule->is_paid;
            $this->is_flexible = $rule->is_flexible;

            return;
        }

        $this->authorizeStore(BreakRule::class);
    }

    public function save()
    {
        $this->validate([
            'shift_id' => ['required', 'exists:shifts,id'],
            'name' => ['required', 'string'],
            'start_time' => ['nullable'],
            'end_time' => ['nullable'],
            'duration_minutes' => ['nullable', 'integer'],
            'is_paid' => ['required', 'boolean'],
            'is_flexible' => ['required', 'boolean'],
        ]);

        return $this->transaction(function () {

            $data = [
                'shift_id' => $this->shift_id,
                'name' => $this->name,
                'start_time' => $this->is_flexible ? null : $this->start_time,
                'end_time' => $this->is_flexible ? null : $this->end_time,
                'duration_minutes' => $this->is_flexible ? $this->duration_minutes : null,
                'is_paid' => $this->is_paid,
                'is_flexible' => $this->is_flexible,
            ];

            if ($this->rule) {
                $this->authorizeUpdate($this->rule);

                $this->rule->update($data);

                Flux::toast(
                    heading: 'Success',
                    text: 'Break rule updated',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(BreakRule::class);

                BreakRule::create($data);

                Flux::toast(
                    heading: 'Success',
                    text: 'Break rule created',
                    variant: 'success'
                );
            }

            $this->redirect(route('break-rules.index'), navigate: true);
        });
    }
};
