<div>
    <x-page-header title="Teams" description="Manage department teams and assign team leaders." button-label="Add Team"
        :button-href="route('teams.create')" />

    <flux:card>
        <flux:table :paginate="$this->teams">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Lead</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->teams as $team)
                    <flux:table.row :key="$team->id">
                        <flux:table.cell>
                            {{ $team->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $team->department?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $team->lead?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('teams.edit', $team) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $team->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center">
                            No teams found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-team" heading="Delete Team?"
        message="You're about to delete this team.<br>This action cannot be reversed." action="destroy" />
</div>
