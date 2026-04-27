<?php

use App\Models\Holiday;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Holiday $holiday = null;

    public $name;

    public $date;

    public $is_national = true;

    public $description;

    public function mount(?Holiday $holiday = null)
    {
        if ($holiday && $holiday->exists) {
            $this->authorizeUpdate($holiday);

            $this->holiday = $holiday;

            $this->name = $holiday->name;
            $this->date = $holiday->date;
            $this->is_national = $holiday->is_national;
            $this->description = $holiday->description;
        } else {
            $this->authorizeStore(Holiday::class);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'date' => [
                'required',
                'date',
                $this->holiday
                    ? 'unique:holidays,date,'.$this->holiday->id
                    : 'unique:holidays,date',
            ],
            'is_national' => ['required', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        return $this->transaction(function () {

            $payload = [
                'name' => $this->name,
                'date' => $this->date,
                'is_national' => $this->is_national,
                'description' => $this->description,
            ];

            if ($this->holiday) {
                $this->authorizeUpdate($this->holiday);

                $this->holiday->update($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Holiday updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(Holiday::class);

                Holiday::create($payload);

                Flux::toast(
                    heading: 'Success',
                    text: 'Holiday created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('holidays.index'), navigate: true);
        });
    }
};
