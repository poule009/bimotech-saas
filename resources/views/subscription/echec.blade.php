@extends('layouts.app')

@section('title', 'Paiement non abouti')
@section('page-title', 'Paiement non abouti')

@section('content')
<div class="max-w-[560px] mx-auto">
    <div class="bg-white border border-line rounded-xl p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-error/10 flex items-center justify-center mx-auto mb-5">
            <svg class="w-8 h-8 text-error" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </div>

        <h1 class="font-display font-medium text-[24px] text-ink mb-2">Le paiement n'a pas abouti</h1>
        <p class="text-[14px] text-muted leading-relaxed mb-6 max-w-[440px] mx-auto">
            Votre paiement a été annulé ou interrompu — <strong class="text-ink">aucun montant n'a été débité</strong>.
            Vous pouvez réessayer, ou déclarer un paiement manuel (Wave, Orange Money, virement) avec justificatif.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('subscription.index') }}"
               class="inline-flex items-center justify-center bg-gold text-white font-semibold text-[14px] rounded-lg px-5 py-3 hover:bg-gold-deep transition-colors">
                Réessayer le paiement
            </a>
            <a href="{{ route('subscription.declarer') }}"
               class="inline-flex items-center justify-center border border-line text-ink font-semibold text-[14px] rounded-lg px-5 py-3 hover:bg-paper-dim transition-colors">
                Déclarer un paiement manuel
            </a>
        </div>
    </div>

    <p class="text-[12.5px] text-muted text-center mt-4">
        Un souci persistant&nbsp;? Contactez le support depuis votre espace.
    </p>
</div>
@endsection
