{{-- resources/views/livewire/pages/overtime-approval/index.blade.php --}}

<div>
    <x-page-header title="Overtime Approval" />

    <flux:card class="mb-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
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
        </div>
    </flux:card>

    <flux:card>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Name</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Date</flux:table.column>
                <flux:table.column>Overtime</flux:table.column>
                <flux:table.column>Action</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->attendances as $item)
                    <flux:table.row>
                        <flux:table.cell>{{ $item->user->name }}</flux:table.cell>
                        <flux:table.cell>{{ optional($item->user->employeeAssignment?->department)->name }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $item->date->format('d M Y') }}</flux:table.cell>
                        <flux:table.cell>{{ $item->overtime_minutes }}</flux:table.cell>

                        <flux:table.cell class="flex gap-2">
                            <flux:button wire:click="approve({{ $item->id }})" variant="primary">
                                Approve
                            </flux:button>

                            <flux:button wire:click="reject({{ $item->id }})">
                                Reject
                            </flux:button>
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

        <div class="mt-3">
            {{ $this->attendances->links() }}
        </div>
    </flux:card>
</div>
