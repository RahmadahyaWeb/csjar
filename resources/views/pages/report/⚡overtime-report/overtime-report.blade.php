{{-- resources/views/livewire/pages/attendance-overtime/index.blade.php --}}

<div>
    <x-page-header title="Overtime Report" description="Employee overtime report and ranking" />

    {{-- FILTER --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            <flux:input type="date" wire:model.live="startDate" label="Start Date" />

            <flux:input type="date" wire:model.live="endDate" label="End Date" />

            <flux:select wire:model.live="departmentId" label="Department">
                <option value="">All</option>
                @foreach ($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex items-end h-full">
                <a href="{{ route('report.overtime.export', [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'departmentId' => $departmentId,
                ]) }}"
                    class="w-full">
                    <flux:button class="w-full" variant="primary">
                        Export Excel
                    </flux:button>
                </a>
            </div>

        </div>
    </flux:card>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">

        <flux:card>
            <div class="text-sm text-gray-500">Employee Overtime</div>
            <div class="text-xl font-semibold">
                {{ $this->summary['total_employee'] }}
            </div>
        </flux:card>

        <flux:card>
            <div class="text-sm text-gray-500">Total Overtime (Minutes)</div>
            <div class="text-xl font-semibold">
                {{ number_format($this->summary['total_overtime_minutes']) }}
            </div>
        </flux:card>

        <flux:card>
            <div class="text-sm text-gray-500">Overtime Days</div>
            <div class="text-xl font-semibold">
                {{ $this->summary['total_overtime_days'] }}
            </div>
        </flux:card>

    </div>

    {{-- TABLE --}}
    <flux:card>
        <flux:table>

            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Overtime Days</flux:table.column>
                <flux:table.column>Total Overtime (Minutes)</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->rankings as $index => $item)
                    <flux:table.row>

                        <flux:table.cell>{{ $index + 1 }}</flux:table.cell>

                        <flux:table.cell>{{ $item['user']->name }}</flux:table.cell>

                        <flux:table.cell>{{ $item['department'] ?? '-' }}</flux:table.cell>

                        <flux:table.cell>{{ $item['overtime_days'] }}</flux:table.cell>

                        <flux:table.cell>
                            {{ number_format($item['total_overtime_minutes']) }}
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">
                            No data
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </flux:card>
</div>
