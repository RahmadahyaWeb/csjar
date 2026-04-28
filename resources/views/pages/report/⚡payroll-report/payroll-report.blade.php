{{-- resources/views/livewire/pages/payroll/report.blade.php --}}

<div>
    <x-page-header title="Payroll Report" />

    {{-- FILTER --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">

            <flux:input type="date" wire:model.live="startDate" label="Start Date" />

            <flux:input type="date" wire:model.live="endDate" label="End Date" />

            <flux:select wire:model.live="departmentId" label="Department">
                <option value="">All</option>
                @foreach ($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex items-end h-full">
                <a href="{{ route('report.payroll-report.export', [
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'departmentId' => $departmentId,
                ]) }}"
                    class="w-full">
                    <flux:button variant="primary" class="w-full">
                        Export Excel
                    </flux:button>
                </a>
            </div>

        </div>
    </flux:card>

    {{-- SUMMARY --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
        <flux:card>
            <div>Total Employee</div>
            <div class="text-xl">{{ $this->summary['total_employee'] }}</div>
        </flux:card>

        <flux:card>
            <div>Total Earning</div>
            <div class="text-xl">{{ number_format($this->summary['total_earning']) }}</div>
        </flux:card>

        <flux:card>
            <div>Total Deduction</div>
            <div class="text-xl">{{ number_format($this->summary['total_deduction']) }}</div>
        </flux:card>

        <flux:card>
            <div>Total Net</div>
            <div class="text-xl">{{ number_format($this->summary['total_net']) }}</div>
        </flux:card>
    </div>

    {{-- BY DEPARTMENT --}}
    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Earning</flux:table.column>
                <flux:table.column>Deduction</flux:table.column>
                <flux:table.column>Net</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->byDepartment as $dept => $data)
                    <flux:table.row>
                        <flux:table.cell>{{ $dept ?? '-' }}</flux:table.cell>
                        <flux:table.cell>{{ $data['total_employee'] }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($data['total_earning']) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($data['total_deduction']) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($data['total_net']) }}</flux:table.cell>
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
