@extends('layouts.app')

@php
    $planLabel = $requiredPlan
        ? app(\App\Services\PlanService::class)->label($requiredPlan)
        : 'supérieur';
@endphp

@section('title', 'Offre supérieure requise')
@section('page-title', 'Fonctionnalité non incluse')

@section('content')
<div class="max-w-[560px] mx-auto">
    <div class="bg-white border border-line rounded-2xl p-8 md:p-10 text-center">
        <div class="w-16 h-16 rounded-full bg-gold/15 text-gold flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        </div>

        <h2 class="font-display font-semibold text-[22px] mb-2">Réservé au plan {{ $planLabel }}</h2>
        <p class="text-[14px] text-muted leading-relaxed mb-7 max-w-[400px] mx-auto">
            Cette fonctionnalité fait partie de l'offre <strong class="text-ink">{{ $planLabel }}</strong>.
            Passez à cette offre pour l'activer sur votre agence.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            @if(Route::has('subscription.index'))
                <a href="{{ route('subscription.index') }}" class="btn-primary">Voir les offres</a>
            @endif
            <a href="{{ route('admin.dashboard') }}" class="px-5 py-3 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[14px] font-bold hover:border-teal transition-colors">Retour au tableau de bord</a>
        </div>
    </div>
</div>
@endsection
