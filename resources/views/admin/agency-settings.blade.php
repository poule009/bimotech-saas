@extends('layouts.app')

@php use Illuminate\Support\Facades\Storage; @endphp

@section('title', "Paramètres de l'agence")
@section('page-title', "Paramètres de l'agence")
@section('page-subtitle', 'Ces informations apparaissent sur vos contrats, quittances et le portail public.')

@section('content')
<div class="max-w-[900px]" x-data="settingsForm" data-tva="{{ $agency->assujetti_tva ? '1' : '0' }}">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Onglets --}}
    <div class="flex gap-1.5 border-b-2 border-line mb-6 overflow-x-auto">
        <button type="button" x-on:click="showIdentite" x-bind:class="identiteTabClass" class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Identité</button>
        <button type="button" x-on:click="showFiscalite" x-bind:class="fiscaliteTabClass" class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Fiscalité</button>
        <button type="button" x-on:click="showDocuments" x-bind:class="documentsTabClass" class="px-4 py-3 text-[14px] font-bold border-b-[3px] -mb-0.5 whitespace-nowrap transition-colors">Logo, signature &amp; cachet</button>
    </div>

    <form method="POST" action="{{ route('admin.agency.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <input type="hidden" name="assujetti_tva" x-bind:value="tvaValue">

        {{-- ─────────── Identité ─────────── --}}
        <div x-show="isIdentite">
            <div class="f-card">
                <h3 class="f-card-title">Informations de l'agence</h3>
                <p class="f-card-sub">Ces informations figurent en en-tête de tous vos documents générés.</p>
                <div class="mb-[18px]">
                    <label for="name" class="f-label">Nom de l'agence</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $agency->name) }}" required class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="mb-[18px]">
                    <label for="adresse" class="f-label">Adresse</label>
                    <input id="adresse" type="text" name="adresse" value="{{ old('adresse', $agency->adresse) }}" class="f-input">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-[18px]">
                    <div>
                        <label for="telephone" class="f-label">Téléphone</label>
                        <input id="telephone" type="text" name="telephone" value="{{ old('telephone', $agency->telephone) }}" class="f-input">
                    </div>
                    <div>
                        <label for="email" class="f-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email', $agency->email) }}" required class="f-input @error('email') f-input-error @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div>
                    <label for="ninea" class="f-label">NINEA</label>
                    <input id="ninea" type="text" name="ninea" value="{{ old('ninea', $agency->ninea) }}" class="f-input max-w-[280px]">
                </div>
            </div>
        </div>

        {{-- ─────────── Fiscalité ─────────── --}}
        <div x-show="isFiscalite" x-cloak>
            <div class="f-card">
                <h3 class="f-card-title">Fiscalité de l'agence</h3>
                <p class="f-card-sub">Détermine si la TVA s'applique automatiquement sur les frais et commissions facturés par votre agence. <strong>N'affecte pas</strong> la fiscalité des propriétaires (gérée séparément sur chaque fiche).</p>
                <div class="flex items-center justify-between gap-5">
                    <div>
                        <div class="text-[14.5px] font-bold">Assujetti à la TVA</div>
                        <div class="text-[12.5px] text-muted mt-0.5 leading-snug">Active le calcul de TVA sur vos documents (commissions, frais de gestion).</div>
                    </div>
                    <button type="button" x-on:click="toggleTva" x-bind:class="tvaSwitchClass" class="relative w-[42px] h-6 rounded-full shrink-0 transition-colors" aria-label="Assujetti à la TVA">
                        <span x-bind:class="tvaKnobClass" class="absolute top-[2.5px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all"></span>
                    </button>
                </div>
                <div x-show="tva" x-cloak class="flex items-center gap-3 mt-4 pt-4 border-t border-paper-dim">
                    <label for="taux_tva" class="f-label mb-0 whitespace-nowrap">Taux appliqué</label>
                    <input id="taux_tva" type="number" name="taux_tva" value="{{ old('taux_tva', $agency->taux_tva ? (float) $agency->taux_tva : 18) }}" min="0" max="100" step="0.01" class="f-input w-[100px] text-center font-bold">
                    <span class="font-bold text-muted">%</span>
                    <span class="text-[12px] text-muted ml-2">Modifiable — ne présumez pas d'un taux fixe pour tous les cas.</span>
                </div>
            </div>
        </div>

        {{-- ─────────── Logo, signature & cachet ─────────── --}}
        <div x-show="isDocuments" x-cloak>
            <div class="f-card">
                <h3 class="f-card-title">Logo, signature &amp; cachet</h3>
                <p class="f-card-sub">Utilisés automatiquement sur les contrats, quittances et le portail public — vous n'aurez pas à les rajouter à chaque document.</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    @php
                        $uploads = [
                            ['name' => 'logo',      'path' => $agency->logo_path,      'label' => "Logo de l'agence", 'desc' => 'En-tête des documents et portail public', 'del' => 'del-logo'],
                            ['name' => 'signature', 'path' => $agency->signature_path, 'label' => 'Signature',        'desc' => 'Insérée automatiquement sur les contrats', 'del' => 'del-signature'],
                            ['name' => 'cachet',    'path' => $agency->cachet_path,    'label' => 'Cachet',           'desc' => 'Tampon officiel, à côté de la signature', 'del' => 'del-cachet'],
                        ];
                    @endphp
                    @foreach($uploads as $u)
                        <div>
                            <div class="text-[13px] font-bold text-teal-deep mb-0.5">{{ $u['label'] }}</div>
                            <div class="text-[11.5px] text-muted mb-2.5 leading-snug min-h-[32px]">{{ $u['desc'] }}</div>
                            <label class="block border-[1.5px] {{ $u['path'] ? 'border-green/40 border-solid bg-white' : 'border-line border-dashed bg-paper' }} rounded-xl h-[120px] flex items-center justify-center cursor-pointer hover:border-teal transition-colors overflow-hidden">
                                @if($u['path'])
                                    <img src="{{ Storage::url($u['path']) }}" alt="{{ $u['label'] }}" class="max-h-[104px] max-w-full object-contain p-2">
                                @else
                                    <span class="text-[22px] text-[#B7AE9C]">⬆</span>
                                @endif
                                <input type="file" name="{{ $u['name'] }}" accept="image/png,image/jpeg,image/webp" class="hidden">
                            </label>
                            <div class="flex items-center justify-center gap-3 mt-2">
                                <span class="text-[11.5px] text-teal font-bold">{{ $u['path'] ? 'Remplacer' : 'Ajouter un fichier' }}</span>
                                @if($u['path'])
                                    <button type="submit" form="{{ $u['del'] }}" class="text-[11.5px] text-error font-bold">Retirer</button>
                                @endif
                            </div>
                            @error($u['name'])<p class="field-error text-center">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 rounded-[10px] bg-paper border border-line px-4 py-3.5 text-[12.5px] text-muted leading-relaxed">
                    💡 <strong class="text-ink">Aperçu sur les documents :</strong> tant que la signature ou le cachet ne sont pas ajoutés, la génération de PDF (contrats, quittances) affiche un <strong>espace vide à signer à la main</strong> — rien ne bloque votre usage en attendant.
                </div>
            </div>
        </div>

        <div class="flex justify-end mt-2">
            <button type="submit" class="bg-teal text-paper px-6 py-3 rounded-[10px] text-[14.5px] font-bold hover:bg-teal-deep transition-colors">Enregistrer les modifications</button>
        </div>
    </form>

    {{-- Formulaires de suppression (hors du formulaire principal — référencés via form=) --}}
    @if($agency->logo_path)<form id="del-logo" method="POST" action="{{ route('admin.agency.logo.delete') }}" class="hidden">@csrf @method('DELETE')</form>@endif
    @if($agency->signature_path)<form id="del-signature" method="POST" action="{{ route('admin.agency.signature.delete') }}" class="hidden">@csrf @method('DELETE')</form>@endif
    @if($agency->cachet_path)<form id="del-cachet" method="POST" action="{{ route('admin.agency.cachet.delete') }}" class="hidden">@csrf @method('DELETE')</form>@endif
</div>
@endsection
