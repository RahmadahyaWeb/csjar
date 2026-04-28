{{-- resources/views/livewire/pages/attendance-late-ranking/index.blade.php --}}

<div>
    <x-page-header title="Late Ranking" description="Employee lateness ranking based on selected period" />

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
                <a href="{{ route('report.late-ranking.export', [
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

    {{-- TABLE --}}
    <flux:card>
        <flux:table>

            <flux:table.columns>
                <flux:table.column>#</flux:table.column>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Late Days</flux:table.column>
                <flux:table.column>Total Late (Minutes)</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->rankings as $index => $item)
                    <flux:table.row>

                        <flux:table.cell>
                            {{ $index + 1 }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item['user']->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item['department'] ?? '-' }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item['late_days'] }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ number_format($item['total_late_minutes']) }}
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
