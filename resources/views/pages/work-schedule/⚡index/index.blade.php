<div>
    <x-page-header title="Work Schedules" description="Manage work schedules and their daily shift configurations."
        button-label="Add Schedule" :button-href="route('work-schedules.create')" />

    <flux:card>
        <flux:table :paginate="$this->schedules">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Code</flux:table.column>
                <flux:table.column>Days</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->schedules as $schedule)
                    <flux:table.row :key="$schedule->id">
                        <flux:table.cell>
                            {{ $schedule->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $schedule->code }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($schedule->days as $day)
                                    <span class="text-xs px-2 py-1 rounded bg-gray-100">
                                        {{ $this->getDayLabel($day->day_of_week) }} :
                                        {{ $day->is_working_day ? $day->shift?->name ?? '-' : 'Off' }}
                                    </span>
                                @endforeach
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('work-schedules.edit', $schedule) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $schedule->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center">
                            No work schedules found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-schedule" heading="Delete Work Schedule?"
        message="You're about to delete this schedule.<br>This action cannot be reversed." action="destroy" />
</div>
