<?php

use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use App\Traits\AuthorizesCrud;
use Flux\Flux;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

new class extends Component
{
    use AuthorizesCrud, AuthorizesRequests;

    public ?Department $department = null;

    public $branch_id;

    public $parent_id;

    public $name;

    public $code;

    public $head_user_id;

    public $branches = [];

    public $parents = [];

    public $users = [];

    public function mount(?Department $department = null)
    {
        $this->branches = Branch::pluck('name', 'id');
        $this->parents = Department::pluck('name', 'id');
        $this->users = User::pluck('name', 'id');

        if ($department && $department->exists) {
            $this->authorizeUpdate($department);

            $this->department = $department;

            $this->branch_id = $department->branch_id;
            $this->parent_id = $department->parent_id;
            $this->name = $department->name;
            $this->code = $department->code;
            $this->head_user_id = $department->head_user_id;
        } else {
            $this->authorizeStore(Department::class);
        }
    }

    public function save()
    {
        $this->validate([
            'branch_id' => ['required', 'exists:branches,id'],
            'parent_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                $this->department
                    ? 'unique:departments,code,'.$this->department->id
                    : 'unique:departments,code',
            ],
            'head_user_id' => ['nullable', 'exists:users,id'],
        ]);

        return $this->transaction(function () {

            if ($this->department) {
                $this->authorizeUpdate($this->department);

                $this->department->update([
                    'branch_id' => $this->branch_id,
                    'parent_id' => $this->parent_id,
                    'name' => $this->name,
                    'code' => $this->code,
                    'head_user_id' => $this->head_user_id,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Department updated successfully',
                    variant: 'success'
                );
            } else {
                $this->authorizeStore(Department::class);

                Department::create([
                    'branch_id' => $this->branch_id,
                    'parent_id' => $this->parent_id,
                    'name' => $this->name,
                    'code' => $this->code,
                    'head_user_id' => $this->head_user_id,
                ]);

                Flux::toast(
                    heading: 'Success',
                    text: 'Department created successfully',
                    variant: 'success'
                );
            }

            $this->redirect(route('departments.index'), navigate: true);
        });
    }
};
