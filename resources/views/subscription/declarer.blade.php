@extends('layouts.app')

@php
    $fmt = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $niveaux = [
        'starter' => 'Starter — 25 000 F/mois',
        'pro'     => 'Pro — 50 000 F/mois',
        'agence'  => 'Agence — 90 000 F/mois',
    ];
    $sel = old('plan_niveau', $planPreselect ?? 'starter');
@endphp

@section('title', 'Déclarer un paiement')
@section('page-title', 'Déclarer un paiement')

@section('content')
<div class="max-w-[680px]">

    <div class="mb-6">
        <a href="{{ route('subscription.index') }}" class="text-[13px] font-bold text-teal hover:underline flex items-center gap-1.5">← Abonnement</a>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <div class="flex items-center gap-2 font-semibold"><x-icon name="alert-triangle" size="16" /> Vérifiez le formulaire :</div>
            <ul class="mt-1.5 ml-6 list-disc">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="f-card">
        <div class="f-card-title">Déclarer un paiement</div>
        <p class="f-card-sub">Le paiement est traité manuellement — votre accès sera réactivé dès confirmation, généralement sous 24h ouvrées.</p>

        <div class="bg-paper border border-line rounded-xl px-4 py-3.5 mb-5 text-[13px] leading-relaxed flex gap-2">
            <x-icon name="wallet" size="16" class="text-teal shrink-0 mt-0.5" />
            <span>Envoyez le montant de votre plan à <strong class="text-teal-deep">Wave : 77 820 11 05</strong> ou <strong class="text-teal-deep">Orange Money : 78 820 11 05</strong> (BIMO-tech), puis déclarez-le ci-dessous avec votre reçu.</span>
        </div>

        <form method="POST" action="{{ route('subscription.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-[18px]">
                <label class="f-label">Plan choisi</label>
                <select name="plan_niveau" class="f-select">
                    @foreach($niveaux as $k => $label)
                        <option value="{{ $k }}" @selected($sel === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="f-label">Montant envoyé (FCFA)</label>
                    <input type="text" inputmode="numeric" name="montant" value="{{ old('montant') }}" placeholder="Ex. 25000" class="f-input @error('montant') f-input-error @enderror">
                </div>
                <div>
                    <label class="f-label">Méthode</label>
                    <select name="methode" class="f-select">
                        <option value="wave" @selected(old('methode')==='wave')>Wave</option>
                        <option value="orange_money" @selected(old('methode')==='orange_money')>Orange Money</option>
                        <option value="virement" @selected(old('methode')==='virement')>Virement bancaire</option>
                    </select>
                </div>
            </div>

            <div class="mb-[18px] mt-[18px]">
                <label class="f-label">Référence de la transaction</label>
                <input type="text" name="reference" value="{{ old('reference') }}" placeholder="Numéro affiché dans votre app Wave / Orange Money" class="f-input @error('reference') f-input-error @enderror">
            </div>

            {{-- Justificatif OBLIGATOIRE — traitement visuel distinctif (bordure rouge), comme les dépenses compta --}}
            <div class="mb-5">
                <label class="f-label">Reçu <span class="text-error">*obligatoire</span></label>
                <label class="block border-[1.5px] border-dashed border-error rounded-[10px] px-4 py-6 text-center cursor-pointer bg-error/5 hover:bg-error/10 transition-colors"
                       x-data="fileField" data-placeholder="Ajouter une capture ou photo du reçu">
                    <input type="file" name="justificatif" accept=".jpg,.jpeg,.png,.pdf" class="hidden" x-on:change="pick">
                    <div class="flex flex-col items-center text-error">
                        <x-icon name="paperclip" size="22" class="mb-1.5" />
                        <span class="font-bold text-[13px]" x-text="label"></span>
                        <span class="text-[11px] opacity-80 mt-0.5">Requis pour valider votre paiement rapidement (jpg, png, pdf — 5 Mo max)</span>
                    </div>
                </label>
                @error('justificatif')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('subscription.index') }}" class="px-5 py-3 rounded-[10px] border-[1.5px] border-line text-[13.5px] font-bold text-ink hover:border-teal">Annuler</a>
                <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[10px] text-[14px] font-bold">Envoyer la déclaration</button>
            </div>
        </form>
    </div>

</div>
@endsection
