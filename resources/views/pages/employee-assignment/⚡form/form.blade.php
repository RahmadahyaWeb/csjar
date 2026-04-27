<div class="space-y-6">

    <x-page-header :title="$assignment ? 'Edit Assignment' : 'Create Assignment'" :description="$assignment
        ? 'Update employee organizational placement and assignment details.'
        : 'Assign an employee to branch, department, position, and team.'" />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

            <flux:field>
                <flux:label>Employee</flux:label>
                <flux:select wire:model="user_id">
                    <option value="">Select Employee</option>
                    @foreach ($users as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="user_id" />
            </flux:field>

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
                <flux:label>Position</flux:label>
                <flux:select wire:model="position_id">
                    <option value="">Select Position</option>
                    @foreach ($positions as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="position_id" />
            </flux:field>

            <flux:field>
                <flux:label>Team</flux:label>
                <flux:select wire:model="team_id">
                    <option value="">Select Team</option>
                    @foreach ($teams as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="team_id" />
            </flux:field>

            <flux:field>
                <flux:label>Start Date</flux:label>
                <flux:input wire:model="start_date" type="date" />
                <flux:error name="start_date" />
            </flux:field>

            <flux:field>
                <flux:label>End Date</flux:label>
                <flux:input wire:model="end_date" type="date" />
                <flux:error name="end_date" />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="is_active" label="Active Assignment" />
                <flux:error name="is_active" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $assignment ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
