{{-- resources/views/livewire/pages/leave-report/index.blade.php --}}

<div>
    <x-page-header title="Leave Report" description="Leave balance and approval tracking" />

    {{-- FILTER --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

            <flux:input type="number" wire:model.live="year" label="Year" />

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
                <a href="{{ route('report.leave.export', [
                    'year' => $year,
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

    {{-- SUMMARY --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <flux:card>
            <div>Pending</div>
            <div class="text-xl">{{ $this->summary['pending'] }}</div>
        </flux:card>

        <flux:card>
            <div>Approved</div>
            <div class="text-xl">{{ $this->summary['approved'] }}</div>
        </flux:card>

        <flux:card>
            <div>Rejected</div>
            <div class="text-xl">{{ $this->summary['rejected'] }}</div>
        </flux:card>
    </div>

    {{-- BALANCE --}}
    <flux:card class="mb-4">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Quota</flux:table.column>
                <flux:table.column>Used</flux:table.column>
                <flux:table.column>Remaining</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->balances as $item)
                    <flux:table.row>
                        <flux:table.cell>{{ $item['user']->name }}</flux:table.cell>
                        <flux:table.cell>{{ $item['department'] }}</flux:table.cell>
                        <flux:table.cell>{{ $item['quota'] }}</flux:table.cell>
                        <flux:table.cell>{{ $item['used'] }}</flux:table.cell>
                        <flux:table.cell>{{ $item['remaining'] }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">No data</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- APPROVAL LIST --}}
    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Start</flux:table.column>
                <flux:table.column>End</flux:table.column>
                <flux:table.column>Status</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->approvals as $leave)
                    <flux:table.row>
                        <flux:table.cell>{{ $leave->user->name }}</flux:table.cell>
                        <flux:table.cell>{{ optional($leave->user->employeeAssignment?->department)->name }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $leave->start_date }}</flux:table.cell>
                        <flux:table.cell>{{ $leave->end_date }}</flux:table.cell>
                        <flux:table.cell>{{ strtoupper($leave->status) }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="5" class="text-center">No data</flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
