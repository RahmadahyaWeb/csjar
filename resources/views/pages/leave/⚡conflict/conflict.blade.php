<div>
    <x-page-header title="Leave Conflicts" description="Monitor overlapping leave by department or position." />

    <flux:card class="space-y-4">

        {{-- FILTER --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <flux:field>
                <flux:label>Department</flux:label>
                <flux:select wire:model.live="department_id">
                    <option value="">All</option>
                    @foreach (\App\Models\Department::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Position</flux:label>
                <flux:select wire:model.live="position_id">
                    <option value="">All</option>
                    @foreach (\App\Models\Position::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
            </flux:field>

        </div>

        {{-- TABLE --}}
        <flux:table>

            <flux:table.columns>
                <flux:table.column>Employee</flux:table.column>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>Position</flux:table.column>
                <flux:table.column>Period</flux:table.column>
                <flux:table.column>Conflicts With</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->conflicts as $leave)

                    @php
                        $assignment = \App\Models\EmployeeAssignment::where('user_id', $leave->user_id)
                            ->where('is_active', true)
                            ->first();

                        $others = \App\Models\Leave::where('id', '!=', $leave->id)
                            ->where('status', 'pending')
                            ->get()
                            ->filter(
                                fn($o) => !($leave->end_date < $o->start_date || $leave->start_date > $o->end_date),
                            );
                    @endphp

                    <flux:table.row :key="$leave->id">

                        <flux:table.cell>
                            {{ $leave->user->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment?->department?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $assignment?->position?->name }}
                        </flux:table.cell>

                        <flux:table.cell>
                            {{ $leave->start_date }} → {{ $leave->end_date }}
                        </flux:table.cell>

                        <flux:table.cell>
                            <div class="text-xs space-y-1">
                                @foreach ($others as $o)
                                    <div>
                                        {{ $o->user->name }}
                                        ({{ $o->start_date }} → {{ $o->end_date }})
                                    </div>
                                @endforeach
                            </div>
                        </flux:table.cell>

                        <flux:table.cell align="end">
                            <flux:button size="sm" href="{{ route('leaves.edit', $leave->id) }}">
                                Review
                            </flux:button>
                        </flux:table.cell>

                    </flux:table.row>

                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="text-center">
                            No conflicts detected
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>

        </flux:table>

    </flux:card>
</div>
