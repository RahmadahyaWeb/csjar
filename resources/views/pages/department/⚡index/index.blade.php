<div>
    <x-page-header title="Departments" description="Manage organizational departments and structure."
        button-label="Add Department" :button-href="route('departments.create')" />

    <flux:card>
        <flux:table :paginate="$this->departments">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Parent</flux:table.column>
                <flux:table.column>Head</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->departments as $department)
                    <flux:table.row :key="$department->id">
                        <flux:table.cell>
                            {{ $department->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $department->branch?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $department->parent?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $department->head?->name ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('departments.edit', $department) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $department->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            No departments found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-department" heading="Delete Department?"
        message="You're about to delete this department.<br>This action cannot be reversed." action="destroy" />
</div>
