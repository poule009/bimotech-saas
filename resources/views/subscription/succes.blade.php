@extends('layouts.app')

@section('title', 'Paiement confirmé')
@section('page-title', 'Paiement confirmé')

@section('content')
<div class="max-w-[560px] mx-auto">
    <div class="bg-white border border-line rounded-xl p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-teal/10 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-teal" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
        </div>

        <h1 class="font-display font-medium text-[24px] text-ink mb-2">Merci, votre abonnement est actif&nbsp;!</h1>
        <p class="text-[14px] text-muted leading-relaxed mb-6 max-w-[420px] mx-auto">
            Le paiement de l'agence <strong class="text-ink">{{ $agency->name }}</strong> a bien été confirmé.
            Votre accès complet est immédiatement disponible.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center justify-center bg-gold text-white font-semibold text-[14px] rounded-lg px-5 py-3 hover:bg-gold-deep transition-colors">
                Aller au tableau de bord
            </a>
            <a href="{{ route('subscription.index') }}"
               class="inline-flex items-center justify-center border border-line text-ink font-semibold text-[14px] rounded-lg px-5 py-3 hover:bg-paper-dim transition-colors">
                Voir mon abonnement
            </a>
        </div>
    </div>

    <p class="text-[12.5px] text-muted text-center mt-4">
        Un reçu figure dans l'historique de paiements de votre abonnement.
    </p>
</div>
@endsection
