@props([
    'sidebar' => false,
])

@if ($sidebar)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 px-3']) }}>
        <img src="{{ asset('PorosTrack BW.png') }}" alt="Poros Logo" class="h-8 w-auto object-contain" />

        <span class="text-lg font-semibold tracking-tight text-foreground">
            Poros
        </span>
    </div>
@else
    <div {{ $attributes->merge(['class' => 'flex items-center gap-3 px-3']) }}>
        <img src="{{ asset('PorosTrack BW.png') }}" alt="Poros Logo" class="h-8 w-auto object-contain" />

        <span class="text-lg font-semibold tracking-tight text-foreground">
            Poros
        </span>
    </div>
@endif
