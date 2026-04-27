<div class="space-y-6">

    <x-page-header :title="$rule ? 'Edit Break Rule' : 'Create Break Rule'" description="Configure break rules for each shift." />

    <form wire:submit.prevent="save">
        <flux:card class="space-y-6">

            <flux:field>
                <flux:label>Shift</flux:label>
                <flux:select wire:model="shift_id">
                    <option value="">Select Shift</option>
                    @foreach ($shifts as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="shift_id" />
            </flux:field>

            <flux:field>
                <flux:label>Name</flux:label>
                <flux:input wire:model="name" type="text" />
            </flux:field>

            <flux:field>
                <flux:checkbox wire:model="is_flexible" label="Flexible Break" />
            </flux:field>

            @if (!$is_flexible)
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Start Time</flux:label>
                        <flux:input wire:model="start_time" type="time" />
                    </flux:field>

                    <flux:field>
                        <flux:label>End Time</flux:label>
                        <flux:input wire:model="end_time" type="time" />
                    </flux:field>
                </div>
            @else
                <flux:field>
                    <flux:label>Duration (minutes)</flux:label>
                    <flux:input wire:model="duration_minutes" type="number" />
                </flux:field>
            @endif

            <flux:field>
                <flux:checkbox wire:model="is_paid" label="Paid Break" />
            </flux:field>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $rule ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
