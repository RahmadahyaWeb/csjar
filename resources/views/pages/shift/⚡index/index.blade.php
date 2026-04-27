<div>
    <x-page-header title="Shifts" description="Manage work shifts, schedules, and working hours configuration."
        button-label="Add Shift" :button-href="route('shifts.create')" />

    <flux:card>
        <flux:table :paginate="$this->shifts">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Code</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Overnight</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->shifts as $shift)
                    <flux:table.row :key="$shift->id">
                        <flux:table.cell>
                            {{ $shift->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $shift->code }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $shift->start_time }} - {{ $shift->end_time }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $shift->is_overnight ? 'Yes' : 'No' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('shifts.edit', $shift) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $shift->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            No shifts found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-shift" heading="Delete Shift?"
        message="You're about to delete this shift.<br>This action cannot be reversed." action="destroy" />
</div>
