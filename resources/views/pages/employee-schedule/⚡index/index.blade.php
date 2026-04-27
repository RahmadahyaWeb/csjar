<div>
    <x-page-header title="Employee Schedules" description="Assign work schedules to employees." button-label="Add Schedule"
        :button-href="route('employee-schedules.create')" />

    <flux:card>
        <flux:table :paginate="$this->schedules">
            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Schedule</flux:table.column>
                <flux:table.column>Start</flux:table.column>
                <flux:table.column>End</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->schedules as $schedule)
                    <flux:table.row :key="$schedule->id">
                        <flux:table.cell>
                            {{ $schedule->user?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $schedule->workSchedule?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $schedule->start_date }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $schedule->end_date ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $schedule->is_active ? 'Active' : 'Inactive' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil"
                                        href="{{ route('employee-schedules.edit', $schedule) }}">
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
                        <flux:table.cell colspan="6" class="text-center">
                            No employee schedules found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-employee-schedule" heading="Delete Schedule?"
        message="You're about to delete this employee schedule.<br>This action cannot be reversed." action="destroy" />
</div>
