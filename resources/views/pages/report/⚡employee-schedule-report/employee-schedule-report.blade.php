{{-- resources/views/livewire/pages/employee-schedule-report/index.blade.php --}}

<div>
    <x-page-header title="Employee Schedule Report" description="Employee work schedule overview" />

    {{-- FILTER --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            <flux:input type="month" wire:model.live="month" label="Month" />

            <flux:select wire:model.live="departmentId" label="Department">
                <option value="">All</option>
                @foreach ($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="userId" label="User">
                <option value="">All</option>
                @foreach ($this->users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex items-end h-full">
                <a href="{{ route('report.employee-schedule.export', [
                    'month' => $month,
                    'departmentId' => $departmentId,
                    'userId' => $userId,
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
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Schedule</flux:table.column>
                <flux:table.column>Start</flux:table.column>
                <flux:table.column>End</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->schedules as $item)
                    <flux:table.row>

                        <flux:table.cell>{{ $item->user->name }}</flux:table.cell>

                        <flux:table.cell>
                            {{ optional($item->user->employeeAssignment?->department)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item->workSchedule->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item->start_date }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item->end_date ?? '-' }}
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
