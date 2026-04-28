{{-- resources/views/livewire/pages/employee-report/index.blade.php --}}

<div>
    <x-page-header title="Employee Report" description="Employee master data overview" />

    {{-- FILTER --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">

            <flux:input wire:model.live="search" label="Search" placeholder="Name or email..." />

            <flux:select wire:model.live="departmentId" label="Department">
                <option value="">All</option>
                @foreach ($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex items-end h-full">
                <a href="{{ route('report.employee.export', [
                    'departmentId' => $departmentId,
                    'search' => $search,
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
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Email</flux:table.column>
                <flux:table.column>Branch</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Position</flux:table.column>
                <flux:table.column>Team</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->employees as $emp)
                    <flux:table.row>

                        <flux:table.cell>{{ $emp->name }}</flux:table.cell>
                        <flux:table.cell>{{ $emp->email }}</flux:table.cell>

                        <flux:table.cell>
                            {{ optional($emp->employeeAssignment?->branch)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ optional($emp->employeeAssignment?->department)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ optional($emp->employeeAssignment?->position)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ optional($emp->employeeAssignment?->team)->name }}
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center">
                            No data
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>
    </flux:card>
</div>
