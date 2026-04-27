{{-- resources/views/livewire/pages/payroll/index.blade.php --}}

<div>
    <x-page-header title="Payroll" />

    {{-- FILTER --}}
    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

            <flux:input type="date" wire:model.live="startDate" label="Start Date" />

            <flux:input type="date" wire:model.live="endDate" label="End Date" />

            <flux:select wire:model.live="departmentId" label="Department">
                <option value="">All Departments</option>
                @foreach ($this->departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="userId" label="User">
                <option value="">All Users</option>
                @foreach ($this->users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </flux:select>

            <div class="flex items-end">
                <flux:button wire:click="generate" class="w-full">
                    Generate
                </flux:button>
            </div>

        </div>
    </flux:card>

    {{-- TABLE --}}
    <flux:card>
        <flux:table :paginate="$this->payrolls">
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Period</flux:table.column>
                <flux:table.column>Earning</flux:table.column>
                <flux:table.column>Deduction</flux:table.column>
                <flux:table.column>Net</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->payrolls as $item)
                    <flux:table.row>

                        <flux:table.cell>
                            {{ $item->user->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ optional($item->user->employeeAssignment?->department)->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $item->start_date->format('d M Y') }}
                            -
                            {{ $item->end_date->format('d M Y') }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ number_format($item->total_earning) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ number_format($item->total_deduction) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ number_format($item->net_salary) }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <span
                                class="px-2 py-1 text-xs rounded
                                @if ($item->status === 'draft') bg-gray-100 text-gray-700
                                @elseif($item->status === 'approved') bg-blue-100 text-blue-700
                                @else bg-green-100 text-green-700 @endif">
                                {{ strtoupper($item->status) }}
                            </span>
                        </flux:table.cell>

                        <flux:table.cell>
                            <flux:dropdown>
                                <flux:button variant="ghost" size="sm" icon:trailing="ellipsis-horizontal">
                                </flux:button>

                                <flux:menu>
                                    <flux:menu.item href="{{ route('payrolls.detail', $item->id) }}">
                                        Detail
                                    </flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>

                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8" class="text-center text-gray-500">
                            No data available
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>
