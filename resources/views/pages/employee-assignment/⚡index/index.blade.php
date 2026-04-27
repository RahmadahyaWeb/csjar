<div>
    <x-page-header title="Employee Assignments" description="Manage employee organizational assignments and placement."
        button-label="Add Assignment" :button-href="route('employee-assignments.create')" />

    <flux:card>
        <flux:table :paginate="$this->assignments">
            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Position</flux:table.column>
                <flux:table.column>Team</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->assignments as $assignment)
                    <flux:table.row :key="$assignment->id">
                        <flux:table.cell>
                            {{ $assignment->user?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment->branch?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment->department?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment->position?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment->team?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil"
                                        href="{{ route('employee-assignments.edit', $assignment) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $assignment->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center">
                            No assignments found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-assignment" heading="Delete Assignment?"
        message="You're about to delete this employee assignment.<br>This action cannot be reversed."
        action="destroy" />
</div>
