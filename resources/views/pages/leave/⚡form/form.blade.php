<div class="space-y-6">

    <x-page-header :title="$leave ? 'Leave Detail' : 'Request Leave'" description="Submit leave request and see approval flow." />

    <flux:card class="space-y-6">

        <form wire:submit.prevent="save" class="space-y-6">

            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model="type" :disabled="$leave">
                    <option value="annual">Annual</option>
                    <option value="sick">Sick</option>
                    <option value="permit">Permit</option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Start Date</flux:label>
                <flux:input wire:model="start_date" type="date" :disabled="$leave" />
            </flux:field>

            <flux:field>
                <flux:label>End Date</flux:label>
                <flux:input wire:model="end_date" type="date" :disabled="$leave" />
            </flux:field>

            <flux:field>
                <flux:label>Reason</flux:label>
                <flux:textarea wire:model="reason" rows="3" :disabled="$leave" />
            </flux:field>

            {{-- Approval Preview --}}
            <div>
                <flux:label>Approval Flow</flux:label>

                <div class="space-y-1 text-sm mt-2">
                    @if ($leave)
                        @foreach ($leave->approvals as $approval)
                            <div>
                                Level {{ $approval->level }} → {{ $approval->approver->name }}
                                ({{ $approval->status }})
                            </div>
                        @endforeach
                    @else
                        @foreach ($previewApprovers as $i => $name)
                            <div>
                                Level {{ $i + 1 }} → {{ $name }}
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            @unless ($leave)
                <div class="flex justify-end">
                    <flux:button type="submit" variant="primary">
                        Submit
                    </flux:button>
                </div>
            @endunless

        </form>

    </flux:card>

</div>
