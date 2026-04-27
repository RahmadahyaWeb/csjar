<div class="space-y-6">

    <x-page-header :title="$schedule ? 'Edit Work Schedule' : 'Create Work Schedule'" :description="$schedule
        ? 'Update schedule and daily shift configuration.'
        : 'Create a new schedule and define shifts for each day.'" />

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

            <div class="space-y-4">
                <flux:label>Schedule Days</flux:label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach ($days as $day => $config)
                        <div class="rounded-xl border p-4 space-y-3">

                            {{-- Header --}}
                            <div class="flex items-center justify-between">
                                <div class="font-medium">
                                    {{ $this->getDayLabel($day) }}
                                </div>

                                <flux:checkbox wire:model="days.{{ $day }}.is_working_day" label="Working" />
                            </div>

                            {{-- Content --}}
                            @if ($config['is_working_day'])
                                <div>
                                    <flux:label class="text-xs text-gray-500">Shift</flux:label>
                                    <flux:select wire:model="days.{{ $day }}.shift_id">
                                        <option value="">Select Shift</option>
                                        @foreach ($shifts as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </flux:select>
                                </div>
                            @else
                                <div class="text-xs text-gray-400 italic">
                                    Off day
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex justify-end">
                <flux:button type="submit" variant="primary">
                    {{ $schedule ? 'Update' : 'Create' }}
                </flux:button>
            </div>

        </flux:card>
    </form>

</div>
