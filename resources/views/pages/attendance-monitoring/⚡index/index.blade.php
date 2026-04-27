{{-- resources/views/livewire/pages/attendance-monitoring/index.blade.php --}}

<div>
    <x-page-header title="Attendance Monitoring" />

    {{-- SUMMARY --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <flux:card>
            <div class="text-sm text-gray-500">Present</div>
            <div class="text-xl font-semibold">{{ $this->summary['present'] }}</div>
        </flux:card>

        <flux:card>
            <div class="text-sm text-gray-500">Absent</div>
            <div class="text-xl font-semibold">{{ $this->summary['absent'] }}</div>
        </flux:card>

        <flux:card>
            <div class="text-sm text-gray-500">Leave</div>
            <div class="text-xl font-semibold">{{ $this->summary['leave'] }}</div>
        </flux:card>

        <flux:card>
            <div class="text-sm text-gray-500">Holiday</div>
            <div class="text-xl font-semibold">{{ $this->summary['holiday'] }}</div>
        </flux:card>
    </div>

    {{-- FILTER --}}
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

            <flux:select wire:model.live="userId" label="User">
                <option value="">All Users</option>

                @foreach ($this->users as $user)
                    <option value="{{ $user->id }}">
                        {{ $user->name }}
                    </option>
                @endforeach
            </flux:select>
        </div>
    </flux:card>

    {{-- TABLE --}}
    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Position</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Check In</flux:table.column>
                <flux:table.column>Check Out</flux:table.column>
                <flux:table.column>Work</flux:table.column>
                <flux:table.column>Break</flux:table.column>
                <flux:table.column>Late</flux:table.column>
                <flux:table.column>Early</flux:table.column>
                <flux:table.column>Overtime</flux:table.column>
                <flux:table.column>Description</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->attendances as $item)
                    <flux:table.row>

                        <flux:table.cell>{{ $item->user->name }}</flux:table.cell>

                        <flux:table.cell>
                            {{ optional($item->user->employeeAssignment?->department)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ optional($item->user->employeeAssignment?->position)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item->date->format('d M Y') }}
                        </flux:table.cell>

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

                        <flux:table.cell>{{ $item->work_minutes }}</flux:table.cell>
                        <flux:table.cell>{{ $item->break_minutes }}</flux:table.cell>
                        <flux:table.cell>{{ $item->late_minutes }}</flux:table.cell>
                        <flux:table.cell>{{ $item->early_leave_minutes }}</flux:table.cell>
                        <flux:table.cell>{{ $item->overtime_minutes }}</flux:table.cell>

                        <flux:table.cell>
                            {{ $item->description }}
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="13" class="text-center text-gray-500">
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
