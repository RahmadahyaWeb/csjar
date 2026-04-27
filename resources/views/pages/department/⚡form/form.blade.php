<div class="space-y-6">

    <x-page-header :title="$department ? 'Edit Department' : 'Create Department'" :description="$department
        ? 'Update department structure and leadership.'
        : 'Create a new department within a branch and assign leadership.'" />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

            <flux:field>
                <flux:label>Branch</flux:label>
                <flux:select wire:model="branch_id">
                    <option value="">Select Branch</option>
                    @foreach ($branches as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="branch_id" />
            </flux:field>

            <flux:field>
                <flux:label>Parent Department</flux:label>
                <flux:select wire:model="parent_id">
                    <option value="">None</option>
                    @foreach ($parents as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="parent_id" />
            </flux:field>

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>Code</flux:label>
                <flux:input wire:model="code" type="text" />
                <flux:error name="code" />
            </flux:field>

            <flux:field>
                <flux:label>Head of Department</flux:label>
                <flux:select wire:model="head_user_id">
                    <option value="">Select User</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="head_user_id" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $department ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
