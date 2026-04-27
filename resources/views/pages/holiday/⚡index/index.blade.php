<div>
    <x-page-header title="Holidays" description="Manage holiday calendar and non-working days." button-label="Add Holiday"
        :button-href="route('holidays.create')" />

    <flux:card>
        <flux:table :paginate="$this->holidays">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->holidays as $holiday)
                    <flux:table.row :key="$holiday->id">
                        <flux:table.cell>
                            {{ $holiday->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ \Carbon\Carbon::parse($holiday->date)->format('d M Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $holiday->is_national ? 'National' : 'Local' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('holidays.edit', $holiday) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $holiday->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center">
                            No holidays found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-holiday" heading="Delete Holiday?"
        message="You're about to delete this holiday.<br>This action cannot be reversed." action="destroy" />
</div>
