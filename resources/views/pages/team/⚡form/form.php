<?php

use App\Models\Department;
use App\Models\Team;
use App\Models\User;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Team $team = null;

    public $department_id;

    public $name;

    public $code;

    public $lead_user_id;

    public $departments = [];

    public $users = [];

    public function mount(?Team $team = null)
    {
        $this->departments = Department::pluck('name', 'id');
        $this->users = User::pluck('name', 'id');

        if ($team && $team->exists) {
            $this->authorizeUpdate($team);

            $this->team = $team;

            $this->department_id = $team->department_id;
            $this->name = $team->name;
            $this->code = $team->code;
            $this->lead_user_id = $team->lead_user_id;
        } else {
            $this->authorizeStore(Team::class);
        }
    }

    public function save()
    {
        $this->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                $this->team
                    ? 'unique:teams,code,'.$this->team->id
                    : 'unique:teams,code',
            ],
            'lead_user_id' => ['nullable', 'exists:users,id'],
        ]);

        return $this->transaction(function () {

            if ($this->team) {
                $this->authorizeUpdate($this->team);

                $this->team->update([
                    'department_id' => $this->department_id,
                    'name' => $this->name,
                    'code' => $this->code,
                    'lead_user_id' => $this->lead_user_id,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Team updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(Team::class);

                Team::create([
                    'department_id' => $this->department_id,
                    'name' => $this->name,
                    'code' => $this->code,
                    'lead_user_id' => $this->lead_user_id,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Team created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('teams.index'), navigate: true);
        });
    }
};
