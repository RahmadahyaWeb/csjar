<div class="space-y-6">

    <x-page-header title="Face Setup" description="Register your face for attendance" />

    <flux:card wire:ignore>
        <video id="video" autoplay muted playsinline class="w-full rounded"
            @if ($hasFace) style="display:none" @endif>
        </video>

        @if ($hasFace)
            <div class="text-green-600 font-medium">
                ✔ Face already registered
            </div>
        @else
            <div class="text-sm text-gray-500 mt-2">
                Make sure your face is clearly visible
            </div>
        @endif
    </flux:card>

    <flux:card>
        @if (!$hasFace)
            <flux:button onclick="enrollFace()" variant="primary">
                Register Face
            </flux:button>
        @else
            <flux:button wire:click="resetFace" variant="danger">
                Re-Register Face
            </flux:button>
        @endif
    </flux:card>

</div>
