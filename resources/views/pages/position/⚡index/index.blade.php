<div>
    <x-page-header title="Positions" description="Manage organizational positions and job hierarchy."
        button-label="Add Position" :button-href="route('positions.create')" />

    <flux:card>
        <flux:table :paginate="$this->positions">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Parent</flux:table.column>
                <flux:table.column>Level</flux:table.column>
                <flux:table.column>Head</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->positions as $position)
                    <flux:table.row :key="$position->id">
                        <flux:table.cell>
                            {{ $position->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $position->department?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $position->parent?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $position->level }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $position->head?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('positions.edit', $position) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $position->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center">
                            No positions found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-position" heading="Delete Position?"
        message="You're about to delete this position.<br>This action cannot be reversed." action="destroy" />
</div>
