<div>
    <x-page-header title="Break Rules" description="Manage break rules per shift." button-label="Add Break Rule"
        :button-href="route('break-rules.create')" />

    <flux:card>
        <flux:table :paginate="$this->breakRules">

            <flux:table.columns>
                <flux:table.column>Shift</flux:table.column>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Paid</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->breakRules as $rule)
                    <flux:table.row :key="$rule->id">

                        <flux:table.cell>
                            {{ $rule->shift?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $rule->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $rule->is_flexible ? 'Flexible' : 'Fixed' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $rule->is_paid ? 'Paid' : 'Unpaid' }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('break-rules.edit', $rule->id) }}">
                                        Edit
                                    </flux:menu.item>

                                    <flux:menu.separator />

                                    <flux:menu.item icon="trash" variant="danger"
                                        wire:click="confirmDelete({{ $rule->id }})">
                                        Delete
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            No break rules found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </flux:card>

    <x-delete-modal name="delete-break-rule" heading="Delete Break Rule?" message="This action cannot be undone."
        action="destroy" />
</div>
