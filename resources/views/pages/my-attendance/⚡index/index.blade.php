<div class="space-y-6">

    <x-page-header title="My Attendance" description="Track your attendance and manage daily activity." />

    {{-- CAMERA + STATUS --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- CAMERA --}}
        <flux:card>
            <div class="flex justify-center">
                <div class="w-full max-w-md">
                    <video id="video" autoplay muted playsinline
                        class="w-full rounded-xl aspect-video object-cover border shadow-sm">
                    </video>

                    <div class="mt-3 text-center text-xs text-gray-500">
                        Face auto-detection active
                    </div>
                </div>
            </div>
        </flux:card>

        {{-- STATUS --}}
        <flux:card class="flex flex-col justify-between">

            <div>
                <div class="text-sm text-zinc-500">
                    Today Status
                </div>

                <div class="text-xl font-semibold mt-2">
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
            </div>

            <div class="mt-4 text-xs text-zinc-500 space-y-1">
                @if ($this->state['checkin_at'])
                    <div>Check In: {{ $this->state['checkin_at'] }}</div>
                @endif

                @if ($this->state['checkout_at'])
                    <div>Check Out: {{ $this->state['checkout_at'] }}</div>
                @endif
            </div>

        </flux:card>

    </div>

    {{-- MAP --}}
    <flux:card>

        <div id="attendance-map" class="h-64 md:h-72 rounded-xl border overflow-hidden"
            data-office-lat="{{ $this->officeLat }}" data-office-lng="{{ $this->officeLng }}"
            data-office-radius="{{ $this->officeRadius }}" wire:ignore>
        </div>

        <div class="flex items-center justify-between mt-3 text-sm">
            <span class="text-zinc-500">
                Distance to office
            </span>

            <span class="font-semibold">
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

    {{-- ACTION --}}
    <flux:card>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

            <flux:button onclick="checkInWithFace()" variant="primary" class="w-full"
                :disabled="!app(\App\Services\AttendanceService::class)->canCheckIn($this->state)">
                Check In
            </flux:button>

            <flux:button wire:click="startBreak" class="w-full"
                :disabled="!app(\App\Services\AttendanceService::class)->canStartBreak($this->state)">
                Start Break
            </flux:button>

            <flux:button wire:click="endBreak" class="w-full"
                :disabled="!app(\App\Services\AttendanceService::class)->canEndBreak($this->state)">
                End Break
            </flux:button>

            <flux:button onclick="checkOutWithFace()" variant="danger" class="w-full"
                :disabled="!app(\App\Services\AttendanceService::class)->canCheckOut($this->state)">
                Check Out
            </flux:button>

        </div>

    </flux:card>

</div>
