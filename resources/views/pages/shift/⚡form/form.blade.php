<div class="space-y-6">

    <x-page-header :title="$shift ? 'Edit Shift' : 'Create Shift'" :description="$shift
        ? 'Update shift configuration including working hours and tolerance settings.'
        : 'Create a new shift with working hours and tolerance settings.'" />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

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
                <flux:label>Start Time</flux:label>
                <flux:input wire:model="start_time" type="time" />
                <flux:error name="start_time" />
            </flux:field>

            <flux:field>
                <flux:label>End Time</flux:label>
                <flux:input wire:model="end_time" type="time" />
                <flux:error name="end_time" />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="is_overnight" label="Overnight Shift" />
                <flux:error name="is_overnight" />
            </flux:field>

            <flux:field>
                <flux:label>Tolerance Late (minutes)</flux:label>
                <flux:input wire:model="tolerance_late" type="number" min="0" />
                <flux:error name="tolerance_late" />
            </flux:field>

            <flux:field>
                <flux:label>Tolerance Early Leave (minutes)</flux:label>
                <flux:input wire:model="tolerance_early_leave" type="number" min="0" />
                <flux:error name="tolerance_early_leave" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $shift ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
