<div>
    <x-page-header title="Attendance Logs" description="Monitor all attendance activities." button-label="Add Log"
        :button-href="route('attendance-logs.create')" />

    <flux:card>
        <flux:table :paginate="$this->logs">

            <flux:table.columns>
                <flux:table.column>User</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Time</flux:table.column>
                <flux:table.column>Location</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->logs as $log)
                    <flux:table.row :key="$log->id">

                        <flux:table.cell>
                            {{ $log->user->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $log->type }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $log->recorded_at }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $log->latitude }}, {{ $log->longitude }}
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal" />

                                <flux:menu>
                                    <flux:menu.item icon="pencil" href="{{ route('attendance-logs.edit', $log) }}">
                                        Edit
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            No attendance logs found
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </flux:card>
</div>
