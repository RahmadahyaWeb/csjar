<div>
    <x-page-header title="My Attendance History" />

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <flux:input type="date" wire:model.live="startDate" label="Start Date" />
            <flux:input type="date" wire:model.live="endDate" label="End Date" />

            <flux:select wire:model.live="status" label="Status">
                <option value="">All</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="leave">Leave</option>
                <option value="holiday">Holiday</option>
            </flux:select>
        </div>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Check In</flux:table.column>
                <flux:table.column>Check Out</flux:table.column>
                <flux:table.column>Work</flux:table.column>
                <flux:table.column>Late</flux:table.column>
                <flux:table.column>Overtime</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->attendances as $item)
                    <flux:table.row>
                        <flux:table.cell>{{ $item->date->format('d M Y') }}</flux:table.cell>

                        <flux:table.cell>
                            <span
                                class="px-2 py-1 rounded text-xs
                                @if ($item->status === 'present') bg-green-100 text-green-700
                                @elseif($item->status === 'absent') bg-red-100 text-red-700
                                @elseif($item->status === 'leave') bg-blue-100 text-blue-700
                                @else bg-gray-100 text-gray-700 @endif">
                                {{ strtoupper($item->status) }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>{{ optional($item->checkin_at)->format('H:i') }}</flux:table.cell>
                        <flux:table.cell>{{ optional($item->checkout_at)->format('H:i') }}</flux:table.cell>

                        <flux:table.cell>{{ $item->work_minutes }} min</flux:table.cell>
                        <flux:table.cell>{{ $item->late_minutes }} min</flux:table.cell>
                        <flux:table.cell>{{ $item->overtime_minutes }} min</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-gray-500">
                            No data available
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-3">
            {{ $this->attendances->links() }}
        </div>
    </flux:card>
</div>
