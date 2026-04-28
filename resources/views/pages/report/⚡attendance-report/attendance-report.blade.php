{{-- resources/views/livewire/pages/attendance-report/index.blade.php --}}

<div>
    <x-page-header title="Attendance Report" />

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-6 gap-3">

            <flux:select wire:model.live="mode" label="Mode">
                <option value="daily">Daily</option>
                <option value="monthly">Monthly</option>
            </flux:select>

            <flux:select wire:model.live="departmentId" label="Department">
                <option value="">All</option>
                @foreach ($this->departments as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="userId" label="User">
                <option value="">All</option>
                @foreach ($this->users as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </flux:select>

            @if ($mode === 'daily')
                <flux:input type="date" wire:model.live="startDate" label="Start Date" />
                <flux:input type="date" wire:model.live="endDate" label="End Date" />
            @else
                <flux:input type="month" wire:model.live="month" label="Month" />
            @endif

            {{-- EXPORT BUTTON --}}
            <div class="flex items-end">
                <a href="{{ route('report.attendance-report.export', [
                    'mode' => $mode,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'month' => $month,
                    'departmentId' => $departmentId,
                    'userId' => $userId,
                ]) }}"
                    class="w-full">
                    <flux:button variant="primary" class="w-full">
                        Export Excel
                    </flux:button>
                </a>
            </div>

        </div>
    </flux:card>

    <flux:card>

        {{-- DAILY --}}
        @if ($mode === 'daily')

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Department</flux:table.column>
                    <flux:table.column>Date</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Work</flux:table.column>
                    <flux:table.column>Late</flux:table.column>
                    <flux:table.column>Overtime</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->reports as $item)
                        <flux:table.row>
                            <flux:table.cell>{{ $item->user->name }}</flux:table.cell>
                            <flux:table.cell>{{ optional($item->user->employeeAssignment?->department)->name }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $item->date->format('d M Y') }}</flux:table.cell>
                            <flux:table.cell>{{ strtoupper($item->status) }}</flux:table.cell>
                            <flux:table.cell>{{ $item->work_minutes }}</flux:table.cell>
                            <flux:table.cell>{{ $item->late_minutes }}</flux:table.cell>
                            <flux:table.cell>{{ $item->overtime_minutes }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center">No data</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

            <div class="mt-3">
                {{ $this->reports->links() }}
            </div>

        @endif

        {{-- MONTHLY --}}
        @if ($mode === 'monthly')

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Department</flux:table.column>
                    <flux:table.column>Present</flux:table.column>
                    <flux:table.column>Absent</flux:table.column>
                    <flux:table.column>Leave</flux:table.column>
                    <flux:table.column>Work</flux:table.column>
                    <flux:table.column>Late</flux:table.column>
                    <flux:table.column>Overtime</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($this->reports as $item)
                        <flux:table.row>
                            <flux:table.cell>{{ $item['user']->name }}</flux:table.cell>
                            <flux:table.cell>{{ optional($item['user']->employeeAssignment?->department)->name }}
                            </flux:table.cell>
                            <flux:table.cell>{{ $item['present'] }}</flux:table.cell>
                            <flux:table.cell>{{ $item['absent'] }}</flux:table.cell>
                            <flux:table.cell>{{ $item['leave'] }}</flux:table.cell>
                            <flux:table.cell>{{ $item['work_minutes'] }}</flux:table.cell>
                            <flux:table.cell>{{ $item['late_minutes'] }}</flux:table.cell>
                            <flux:table.cell>{{ $item['overtime_minutes'] }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center">No data</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>

        @endif

    </flux:card>
</div>
