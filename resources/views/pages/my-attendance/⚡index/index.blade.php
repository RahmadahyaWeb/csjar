<div class="space-y-6">

    <x-page-header title="My Attendance" description="Track your attendance and manage daily activity." />

    {{-- MAP --}}
    <flux:card>

        <div id="attendance-map" class="h-72 rounded-xl" data-office-lat="{{ $this->officeLat }}"
            data-office-lng="{{ $this->officeLng }}" data-office-radius="{{ $this->officeRadius }}" wire:ignore></div>

        <div class="flex items-center justify-between mt-3 text-sm">
            <span class="text-zinc-500">
                Distance to office
            </span>

            <span class="font-medium">
                {{ number_format($this->distance, 1) }} m
            </span>
        </div>

        <div class="flex gap-2 mt-3">
            <flux:button size="sm" id="btnUserLocation" class="flex-1">
                My Location
            </flux:button>

            <flux:button size="sm" id="btnOfficeLocation" class="flex-1">
                Office
            </flux:button>
        </div>

    </flux:card>

    {{-- STATUS --}}
    <flux:card class="space-y-3">

        <div class="text-sm text-zinc-500">
            Today Status
        </div>

        <div class="text-lg font-semibold">
            @if (!$this->state['has_checkin'])
                Not Checked In
            @elseif ($this->state['has_checkout'])
                Completed
            @elseif ($this->state['is_on_break'])
                On Break
            @else
                Working
            @endif
        </div>

        <div class="text-xs text-zinc-500 space-y-1">
            @if ($this->state['checkin_at'])
                <div>Check In: {{ $this->state['checkin_at'] }}</div>
            @endif

            @if ($this->state['checkout_at'])
                <div>Check Out: {{ $this->state['checkout_at'] }}</div>
            @endif
        </div>

    </flux:card>

    {{-- ACTION BUTTONS --}}
    <flux:card>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            <flux:button wire:click="checkIn"
                :disabled="!app(\App\Services\AttendanceService::class)->canCheckIn($this->state)">
                Check In
            </flux:button>

            <flux:button wire:click="startBreak"
                :disabled="!app(\App\Services\AttendanceService::class)->canStartBreak($this->state)">
                Start Break
            </flux:button>

            <flux:button wire:click="endBreak"
                :disabled="!app(\App\Services\AttendanceService::class)->canEndBreak($this->state)">
                End Break
            </flux:button>

            <flux:button wire:click="checkOut"
                :disabled="!app(\App\Services\AttendanceService::class)->canCheckOut($this->state)">
                Check Out
            </flux:button>

        </div>

    </flux:card>

</div>
