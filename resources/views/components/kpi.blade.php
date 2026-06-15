@props([
    'label',
    'value',
    'unit' => null,
    'sub' => null,
    'href' => null,
    'variant' => 'default',   // default | primary | danger
    'delta' => null,          // nombre (ou null pour masquer)
    'deltaSuffix' => '%',
    'deltaGood' => true,       // une hausse est-elle « bonne » ?
    'big' => false,            // chiffre roi : nombre agrandi
])
@php
    $variantClass = match ($variant) {
        'primary' => 'bg-bimo-gold/[8%] border-bimo-gold/25',
        'danger'  => 'bg-bimo-red/[5%] border-bimo-red/25 hover:border-bimo-red/40',
        default   => 'bg-white border-bimo-navy/10 hover:border-bimo-navy/25',
    };
    $valueColor = match ($variant) {
        'primary' => 'text-bimo-gold',
        'danger'  => 'text-bimo-red',
        default   => 'text-bimo-text',
    };
    $iconWrap = match ($variant) {
        'primary' => 'bg-bimo-gold/20 text-bimo-gold',
        'danger'  => 'bg-bimo-red/10 text-bimo-red',
        default   => 'bg-bimo-navy/5 text-bimo-text/40',
    };
    $unitColor  = $variant === 'primary' ? 'text-bimo-gold/60' : 'text-bimo-text/40';
    $labelColor = $variant === 'primary' ? 'text-bimo-gold/70' : 'text-bimo-text/50';
    $valueSize  = $big ? 'text-3xl lg:text-[2.6rem]' : 'text-2xl lg:text-3xl';

    $deltaUp = $delta !== null && $delta >= 0;
    $deltaPositive = ($deltaUp && $deltaGood) || (! $deltaUp && ! $deltaGood);
@endphp
<div {{ $attributes->merge(['class' => 'relative rounded-[14px] border p-5 lg:p-6 transition-all duration-150 h-full '.$variantClass]) }}>
    @if($href)
    <a href="{{ $href }}" class="absolute inset-0 rounded-[14px]" aria-label="{{ $label }}"></a>
    @endif

    <div class="flex items-start justify-between mb-4">
        <div class="w-10 h-10 rounded-[10px] flex items-center justify-center {{ $iconWrap }}">
            {{ $icon ?? '' }}
        </div>
        @if($delta !== null)
        <span class="inline-flex items-center gap-0.5 font-body font-semibold text-[11px] {{ $deltaPositive ? 'text-bimo-gold' : 'text-bimo-red' }}">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <polyline points="{{ $deltaUp ? '18 15 12 9 6 15' : '6 9 12 15 18 9' }}"/>
            </svg>
            {{ ($deltaUp ? '+' : '') . $delta . $deltaSuffix }}
        </span>
        @endif
    </div>

    <div class="font-display font-extrabold {{ $valueSize }} {{ $valueColor }} leading-none">
        {{ $value }}@if($unit)<span class="font-body font-normal text-sm {{ $unitColor }}"> {{ $unit }}</span>@endif
    </div>
    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest mt-2 {{ $labelColor }}">{{ $label }}</div>
    @if($sub)
    <div class="font-body text-[10px] text-bimo-text/40 mt-0.5">{{ $sub }}</div>
    @endif
</div>
