<div>
    <x-page-header title="Branches" description="Manage company branches, locations, and operational coverage."
        button-label="Add Branch" :button-href="route('branches.create')" />

    <flux:card>
        <flux:table :paginate="$this->branches">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Code</flux:table.column>
                <flux:table.column>Location</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->branches as $branch)
                    <flux:table.row :key="$branch->id">
                        <flux:table.cell>
                            {{ $branch->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $branch->code }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $branch->latitude }}, {{ $branch->longitude }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('branches.edit', $branch) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $branch->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4" class="text-center">
                            No branches found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-branch" heading="Delete Branch?"
        message="You're about to delete this branch.<br>This action cannot be reversed." action="destroy" />
</div>
