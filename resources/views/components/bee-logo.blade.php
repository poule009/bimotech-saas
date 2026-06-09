@props(['variant' => 'white', 'size' => 'md'])
@php
$iconSizes = ['sm' => 'w-6 h-6', 'md' => 'w-8 h-8', 'lg' => 'w-10 h-10'];
$textSizes = ['sm' => 'text-sm', 'md' => 'text-base', 'lg' => 'text-xl'];
$iconClass  = ($iconSizes[$size] ?? 'w-8 h-8')  . ' ' . ($variant === 'navy' ? 'text-bimo-navy' : 'text-white');
$textClass  = ($textSizes[$size] ?? 'text-base') . ' ' . ($variant === 'navy' ? 'text-bimo-navy' : 'text-white');
@endphp
<div class="flex items-center gap-2.5">
    <svg class="{{ $iconClass }} flex-shrink-0" viewBox="0 0 120 110" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M52 50 C56 28 84 18 90 34 C96 50 78 62 54 56 Z"/>
        <path d="M52 56 C58 36 86 28 90 46 C94 64 74 72 54 64 Z"/>
        <path d="M50 62 C56 46 78 44 80 58 C82 72 66 76 52 70 Z"/>
        <ellipse cx="38" cy="60" rx="14" ry="18" transform="rotate(-8 38 60)"/>
        <circle cx="24" cy="57" r="12"/>
        <circle cx="21" cy="54" r="4" fill="white"/>
        <line x1="18" y1="47" x2="8" y2="32" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
        <line x1="22" y1="46" x2="14" y2="28" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
        <line x1="28" y1="72" x2="16" y2="84" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
        <line x1="34" y1="76" x2="24" y2="90" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
        <line x1="40" y1="78" x2="32" y2="92" stroke="currentColor" stroke-width="3.5" stroke-linecap="round"/>
    </svg>
    <span class="font-display font-extrabold leading-none {{ $textClass }}">bee</span>
</div>
