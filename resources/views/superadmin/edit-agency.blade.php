@extends('layouts.superadmin')

@section('title', 'Modifier — '.$agency->name)

@section('content')
<div class="max-w-[820px] mx-auto">

    {{-- En-tête --}}
    <div class="mb-6">
        <a href="{{ route('superadmin.agencies.show', $agency) }}" class="text-[13px] font-semibold text-teal hover:underline inline-flex items-center gap-1.5 mb-3">← {{ $agency->name }}</a>
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] text-ink mt-1">Modifier l'agence</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Coordonnées et paramètre de TVA de l'agence.</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            Merci de corriger les champs signalés ci-dessous.
        </div>
    @endif

    <form method="POST" action="{{ route('superadmin.agencies.update', $agency) }}">
        @csrf
        @method('PATCH')

        <div class="f-card mb-5">
            <div class="f-card-title">Coordonnées</div>
            <p class="f-card-sub">Le nom et l'email sont obligatoires. L'email doit rester unique parmi les agences.</p>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="f-label">Nom de l'agence</label>
                    <input type="text" name="name" value="{{ old('name', $agency->name) }}" class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $agency->email) }}" class="f-input @error('email') f-input-error @enderror">
                    @error('email')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Téléphone <span class="text-muted font-normal">(optionnel)</span></label>
                    <input type="text" name="telephone" value="{{ old('telephone', $agency->telephone) }}" placeholder="+221 33 800 00 00" class="f-input @error('telephone') f-input-error @enderror">
                    @error('telephone')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="f-label">Adresse <span class="text-muted font-normal">(optionnel)</span></label>
                    <input type="text" name="adresse" value="{{ old('adresse', $agency->adresse) }}" placeholder="Dakar, Almadies" class="f-input @error('adresse') f-input-error @enderror">
                    @error('adresse')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="f-card mb-5">
            <div class="f-card-title">Fiscalité</div>
            <p class="f-card-sub">Taux de TVA appliqué par défaut aux commissions de l'agence (en pourcentage).</p>

            <div class="max-w-[220px]">
                <label class="f-label">Taux de TVA (%)</label>
                <input type="number" name="taux_tva" step="0.01" min="0" max="100"
                       value="{{ old('taux_tva', $agency->taux_tva) }}" placeholder="18.00"
                       class="f-input @error('taux_tva') f-input-error @enderror">
                @error('taux_tva')<p class="mt-1.5 text-[12px] text-error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('superadmin.agencies.show', $agency) }}" class="px-5 py-3 rounded-[11px] border-[1.5px] border-line text-[14px] font-bold text-ink hover:border-teal">Annuler</a>
            <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold">Enregistrer</button>
        </div>
    </form>

</div>
@endsection
