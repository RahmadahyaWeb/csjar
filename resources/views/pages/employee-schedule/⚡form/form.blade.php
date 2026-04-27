<div class="space-y-6">

    <x-page-header :title="$schedule ? 'Edit Employee Schedule' : 'Create Employee Schedule'" :description="$schedule ? 'Update employee work schedule assignment.' : 'Assign a work schedule to an employee.'" />

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
                <flux:label>Work Schedule</flux:label>
                <flux:select wire:model="work_schedule_id">
                    <option value="">Select Schedule</option>
                    @foreach ($schedules as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="work_schedule_id" />
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
                <flux:checkbox wire:model="is_active" label="Active" />
                <flux:error name="is_active" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $schedule ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
