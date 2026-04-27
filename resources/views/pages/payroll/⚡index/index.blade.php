{{-- resources/views/livewire/pages/payroll/index.blade.php --}}

<div>
    <x-page-header title="Payroll" />

    <flux:card class="mb-4">
        <div class="flex items-end gap-3">

            <flux:input type="date" wire:model.live="startDate" label="Start Date" />
            <flux:input type="date" wire:model.live="endDate" label="End Date" />

            <flux:button wire:click="generate">
                Generate
            </flux:button>

        </div>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
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

                        <flux:table.cell>{{ $item->user->name }}</flux:table.cell>

                        <flux:table.cell>
                            {{ \Carbon\Carbon::parse($item->start_date)->format('d M Y') }}
                            -
                            {{ \Carbon\Carbon::parse($item->end_date)->format('d M Y') }}
                        </flux:table.cell>

                        <flux:table.cell>{{ number_format($item->total_earning) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($item->total_deduction) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($item->net_salary) }}</flux:table.cell>

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
                        <flux:table.cell colspan="7" class="text-center">
                            No data
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div class="mt-3">
            {{ $this->payrolls->links() }}
        </div>
    </flux:card>
</div>
