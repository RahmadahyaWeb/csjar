<?php

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Position $position = null;

    public $department_id;

    public $parent_id;

    public $name;

    public $code;

    public $level = 1;

    public $head_user_id;

    public $departments = [];

    public $parents = [];

    public $users = [];

    public function mount(?Position $position = null)
    {
        $this->departments = Department::pluck('name', 'id');
        $this->parents = Position::pluck('name', 'id');
        $this->users = User::pluck('name', 'id');

        if ($position && $position->exists) {
            $this->authorizeUpdate($position);

            $this->position = $position;

            $this->department_id = $position->department_id;
            $this->parent_id = $position->parent_id;
            $this->name = $position->name;
            $this->code = $position->code;
            $this->level = $position->level;
            $this->head_user_id = $position->head_user_id;
        } else {
            $this->authorizeStore(Position::class);
        }
    }

    public function save()
    {
        $this->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'parent_id' => ['nullable', 'exists:positions,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                $this->position
                    ? 'unique:positions,code,'.$this->position->id
                    : 'unique:positions,code',
            ],
            'level' => ['required', 'integer', 'min:1'],
            'head_user_id' => ['nullable', 'exists:users,id'],
        ]);

        return $this->transaction(function () {

            if ($this->position) {
                $this->authorizeUpdate($this->position);

                $this->position->update([
                    'department_id' => $this->department_id,
                    'parent_id' => $this->parent_id,
                    'name' => $this->name,
                    'code' => $this->code,
                    'level' => $this->level,
                    'head_user_id' => $this->head_user_id,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Position updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(Position::class);

                Position::create([
                    'department_id' => $this->department_id,
                    'parent_id' => $this->parent_id,
                    'name' => $this->name,
                    'code' => $this->code,
                    'level' => $this->level,
                    'head_user_id' => $this->head_user_id,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Position created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('positions.index'), navigate: true);
        });
    }
};
