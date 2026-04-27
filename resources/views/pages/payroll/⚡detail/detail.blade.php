{{-- resources/views/livewire/pages/payroll/form.blade.php --}}

<div>
    <x-page-header title="Payroll Detail" />

    <flux:card class="mb-4">
        <div class="grid grid-cols-2 gap-3 text-sm">

            <div>
                <div class="text-gray-500">Name</div>
                <div class="font-medium">{{ $payroll->user->name }}</div>
            </div>

            <div>
                <div class="text-gray-500">Period</div>
                <div class="font-medium">
                    {{ \Carbon\Carbon::parse($payroll->start_date)->format('d M Y') }}
                    -
                    {{ \Carbon\Carbon::parse($payroll->end_date)->format('d M Y') }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Status</div>
                <div class="font-medium uppercase">{{ $payroll->status }}</div>
            </div>

            <div>
                <div class="text-gray-500">Net Salary</div>
                <div class="font-semibold">
                    {{ number_format($payroll->net_salary) }}
                </div>
            </div>

        </div>
    </flux:card>

    <flux:card class="mb-4">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Component</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($payroll->details as $detail)
                    <flux:table.row>
                        <flux:table.cell>{{ $detail->component_name }}</flux:table.cell>
                        <flux:table.cell>{{ strtoupper($detail->type) }}</flux:table.cell>
                        <flux:table.cell>{{ number_format($detail->amount) }}</flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="3" class="text-center">
                            No detail
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </flux:card>

    {{-- ACTION BUTTONS (SEJAJAR & TERKONTROL) --}}
    <div class="flex items-center gap-2">

        <flux:button wire:click="approve" variant="primary" :disabled="$payroll->status !== 'draft'">
            Approve
        </flux:button>

        <flux:button wire:click="markAsPaid" :disabled="$payroll->status !== 'approved'">
            Mark as Paid
        </flux:button>

        <flux:button href="{{ route('payrolls.slip', $payroll->id) }}">
            Download Slip
        </flux:button>

    </div>
</div>
