<div class="space-y-6">

    <x-page-header :title="$position ? 'Edit Position' : 'Create Position'" :description="$position
        ? 'Update position hierarchy and leadership.'
        : 'Create a new organizational position and assign hierarchy.'" />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

            <flux:field>
                <flux:label>Department</flux:label>
                <flux:select wire:model="department_id">
                    <option value="">Select Department</option>
                    @foreach ($departments as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="department_id" />
            </flux:field>

            <flux:field>
                <flux:label>Parent Position</flux:label>
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
                <flux:label>Level</flux:label>
                <flux:input wire:model="level" type="number" min="1" />
                <flux:error name="level" />
            </flux:field>

            <flux:field>
                <flux:label>Head of Position</flux:label>
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
                    {{ $position ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
