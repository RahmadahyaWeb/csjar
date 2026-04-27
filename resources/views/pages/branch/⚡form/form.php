<?php

use App\Models\Branch;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Branch $branch = null;

    public $name;

    public $code;

    public $latitude;

    public $longitude;

    public $radius;

    public function mount(?Branch $branch = null)
    {
        if ($branch && $branch->exists) {
            $this->authorizeUpdate($branch);

            $this->branch = $branch;

            $this->name = $branch->name;
            $this->code = $branch->code;
            $this->latitude = $branch->latitude;
            $this->longitude = $branch->longitude;
            $this->radius = $branch->radius;
        } else {
            $this->authorizeStore(Branch::class);
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
                $this->branch
                    ? 'unique:branches,code,'.$this->branch->id
                    : 'unique:branches,code',
            ],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'radius' => ['nullable', 'integer'],
        ]);

        return $this->transaction(function () {

            if ($this->branch) {
                $this->authorizeUpdate($this->branch);

                $this->branch->update([
                    'name' => $this->name,
                    'code' => $this->code,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'radius' => $this->radius,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Branch updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(Branch::class);

                Branch::create([
                    'name' => $this->name,
                    'code' => $this->code,
                    'latitude' => $this->latitude,
                    'longitude' => $this->longitude,
                    'radius' => $this->radius,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Branch created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('branches.index'), navigate: true);
        });
    }
};
