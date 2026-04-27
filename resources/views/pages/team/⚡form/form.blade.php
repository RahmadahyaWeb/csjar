<div class="space-y-6">

    <x-page-header :title="$team ? 'Edit Team' : 'Create Team'" :description="$team
        ? 'Update team details and assign team leadership.'
        : 'Create a new team within a department and assign a leader.'" />

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
                <flux:label>Team Lead</flux:label>
                <flux:select wire:model="lead_user_id">
                    <option value="">Select User</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="lead_user_id" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $team ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
