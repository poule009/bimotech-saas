@extends('layouts.app')

@php $estProprio = $user->isProprietaire(); @endphp

@section('title', 'Modifier ' . $user->name)
@section('page-title', $estProprio ? 'Modifier le propriétaire' : 'Modifier le locataire')
@section('page-subtitle')
    <a href="{{ route('admin.users.show', $user) }}" class="text-teal font-semibold hover:underline">{{ $user->name }}</a>
    <span class="text-muted"> / Modifier</span>
@endsection

@section('content')
@unless($estProprio)
@php $profil = $user->locataire; @endphp
<form method="POST" action="{{ route('admin.users.update', $user) }}" x-data="tenantForm" enctype="multipart/form-data"
      data-tenant-type="{{ old('type_locataire', $profil?->est_entreprise ? 'entreprise' : 'particulier') }}"
      data-piece-default="{{ $profil?->piece_identite_path ? 'Remplacer le fichier' : 'Choisir un fichier' }}" class="max-w-[1000px]">
    @csrf
    @method('PATCH')
    <input type="hidden" name="type_locataire" x-bind:value="typeValue">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Type de locataire</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-6">
                    <button type="button" x-on:click="setParticulier" x-bind:class="forkParticulierClass" class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1"><x-icon name="user" size="20" /><span class="font-bold text-[15px]">Particulier</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Une personne physique.</div>
                    </button>
                    <button type="button" x-on:click="setEntreprise" x-bind:class="forkEntrepriseClass" class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1"><x-icon name="building" size="20" /><span class="font-bold text-[15px]">Bureau / Société</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Entreprise, association…</div>
                    </button>
                </div>
                <div class="mb-[18px]">
                    <label for="name" class="f-label">
                        <span x-show="isParticulier">Prénom et nom</span>
                        <span x-show="isEntreprise" x-cloak>Nom du contact</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div x-show="isEntreprise" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2"><label for="nom_entreprise" class="f-label">Raison sociale</label><input id="nom_entreprise" type="text" name="nom_entreprise" value="{{ old('nom_entreprise', $profil?->nom_entreprise) }}" class="f-input"></div>
                    <div><label for="ninea_locataire" class="f-label">NINEA</label><input id="ninea_locataire" type="text" name="ninea_locataire" value="{{ old('ninea_locataire', $profil?->ninea_locataire) }}" class="f-input"></div>
                    <div><label for="rccm_locataire" class="f-label">RCCM <span class="text-muted font-normal">(optionnel)</span></label><input id="rccm_locataire" type="text" name="rccm_locataire" value="{{ old('rccm_locataire', $profil?->rccm_locataire) }}" class="f-input"></div>
                </div>
            </div>

            <div class="f-card">
                <h3 class="f-card-title">Coordonnées</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label for="telephone" class="f-label">Téléphone (WhatsApp)</label><input id="telephone" type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="f-input"></div>
                    <div>
                        <label for="email" class="f-label">Email <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="f-input @error('email') f-input-error @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-[18px]">
                    <div><label for="profession" class="f-label">Profession <span class="text-muted font-normal">(optionnel)</span></label><input id="profession" type="text" name="profession" value="{{ old('profession', $profil?->profession) }}" class="f-input"></div>
                    <div><label for="revenu_mensuel" class="f-label">Revenu mensuel <span class="text-muted font-normal">(optionnel)</span></label><input id="revenu_mensuel" type="number" name="revenu_mensuel" value="{{ old('revenu_mensuel', $profil?->revenu_mensuel ? (int) $profil->revenu_mensuel : '') }}" min="0" class="f-input"></div>
                </div>
                <div class="mt-[18px]"><label for="cni" class="f-label">N° CNI <span class="text-muted font-normal">(optionnel)</span></label><input id="cni" type="text" name="cni" value="{{ old('cni', $profil?->cni) }}" class="f-input"></div>
            </div>
        </div>

        <div class="lg:sticky lg:top-6 space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Pièce d'identité</h3>
                <p class="f-card-sub">CNI ou passeport.</p>
                @if($profil?->piece_identite_path)
                    <div class="flex items-center gap-3 mb-3 p-3 border border-line rounded-[10px] bg-paper">
                        <x-icon name="file-text" size="18" class="text-teal shrink-0" />
                        <span class="text-[13px] font-semibold flex-1 truncate">{{ basename($profil->piece_identite_path) }}</span>
                        <a href="{{ Storage::disk('public')->url($profil->piece_identite_path) }}" target="_blank" rel="noopener" class="text-[13px] font-bold text-teal hover:underline shrink-0">Ouvrir</a>
                    </div>
                @endif
                <label for="piece_identite" class="block border-[1.5px] border-dashed border-line rounded-[11px] p-5 text-center bg-paper cursor-pointer hover:border-teal transition-colors">
                    <x-icon name="file-text" size="22" class="block mx-auto mb-2 text-muted" />
                    <span class="block text-[13px] font-semibold text-teal" x-text="pieceLabel">{{ $profil?->piece_identite_path ? 'Remplacer le fichier' : 'Choisir un fichier' }}</span>
                    <span class="block text-[11.5px] text-muted mt-1">JPG, PNG, WEBP ou PDF — 5 Mo max</span>
                </label>
                <input id="piece_identite" type="file" name="piece_identite"
                       accept="image/jpeg,image/png,image/webp,application/pdf"
                       x-on:change="pickPiece" class="hidden">
                @error('piece_identite')<p class="field-error mt-2">{{ $message }}</p>@enderror
            </div>
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Enregistrer les modifications</button>
                <a href="{{ route('admin.users.show', $user) }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
                <p class="text-[12.5px] text-muted leading-relaxed mt-4 pt-4 border-t border-paper-dim">Le garant et le loyer se gèrent sur le contrat.</p>
            </div>
        </div>
    </div>
</form>
@else
@php
    $profil = $user->proprietaire;
    $isEnt  = old('est_personne_morale_is') !== null ? old('est_personne_morale_is') === '1' : (bool) $profil?->est_personne_morale_is;
    $isTva  = old('assujetti_tva') !== null ? old('assujetti_tva') === '1' : (bool) $profil?->assujetti_tva;
    $isBrsDispense = old('brs_dispense') !== null ? old('brs_dispense') === '1' : (bool) $profil?->brs_dispense;
@endphp
<form method="POST" action="{{ route('admin.users.update', $user) }}" x-data="ownerForm" enctype="multipart/form-data"
      data-owner-type="{{ $isEnt ? 'entreprise' : 'particulier' }}"
      data-owner-tva="{{ $isTva ? '1' : '0' }}"
      data-owner-brs-dispense="{{ $isBrsDispense ? '1' : '0' }}"
      data-piece-default="{{ $profil?->piece_identite_path ? 'Remplacer le fichier' : 'Choisir un fichier' }}"
      class="max-w-[1100px]">
    @csrf
    @method('PATCH')
    <input type="hidden" name="est_personne_morale_is" x-bind:value="moraleValue">
    <input type="hidden" name="assujetti_tva" x-bind:value="tvaValue">
    <input type="hidden" name="brs_dispense" x-bind:value="brsDispenseValue">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">
        <div class="space-y-5">

            {{-- Type --}}
            <div class="f-card">
                <h3 class="f-card-title">Type de propriétaire</h3>
                <p class="f-card-sub">Modifier le type ajuste les cases fiscales des futures quittances.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-6">
                    <button type="button" x-on:click="setParticulier" x-bind:class="forkParticulierClass"
                            class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1.5"><x-icon name="user" size="20" /><span class="font-bold text-[15px]">Particulier</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Une personne, propriétaire en son nom propre.</div>
                    </button>
                    <button type="button" x-on:click="setEntreprise" x-bind:class="forkEntrepriseClass"
                            class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1.5"><x-icon name="building" size="20" /><span class="font-bold text-[15px]">Entreprise</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Société, SCI ou groupe propriétaire du bien.</div>
                    </button>
                </div>

                <div class="mb-[18px]">
                    <label for="name" class="f-label">
                        <span x-show="isParticulier">Prénom et nom</span>
                        <span x-show="isEntreprise" x-cloak>Raison sociale</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                <div class="mb-[18px]" x-show="isParticulier">
                    <label for="cni" class="f-label">N° CNI ou passeport <span class="text-muted font-normal">(optionnel)</span></label>
                    <input id="cni" type="text" name="cni" value="{{ old('cni', $profil?->cni) }}" class="f-input">
                </div>

                <div class="mb-[18px]" x-show="isEntreprise" x-cloak>
                    <label for="ninea" class="f-label">NINEA</label>
                    <input id="ninea" type="text" name="ninea" value="{{ old('ninea', $profil?->ninea) }}" class="f-input">
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="f-card">
                <h3 class="f-card-title">Coordonnées</h3>
                <p class="f-card-sub">Utilisées pour les relances et l'envoi des documents.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="telephone" class="f-label">Téléphone</label>
                        <input id="telephone" type="text" name="telephone" value="{{ old('telephone', $user->telephone) }}" class="f-input">
                    </div>
                    <div>
                        <label for="email" class="f-label">Email <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="f-input @error('email') f-input-error @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-[18px]">
                    <div>
                        <label for="ville" class="f-label">Ville</label>
                        <input id="ville" type="text" name="ville" value="{{ old('ville', $profil?->ville) }}" class="f-input">
                    </div>
                    <div>
                        <label for="adresse" class="f-label">Adresse <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse', $user->adresse) }}" class="f-input">
                    </div>
                </div>
            </div>

            {{-- Versement --}}
            <div class="f-card">
                <h3 class="f-card-title">Versement des loyers</h3>
                <div class="mb-[18px]">
                    <label for="mode_paiement_prefere" class="f-label">Mode de versement préféré</label>
                    <select id="mode_paiement_prefere" name="mode_paiement_prefere" class="f-select">
                        @php $modes = ['wave'=>'Wave','orange_money'=>'Orange Money','virement'=>'Virement bancaire','especes'=>'Espèces','cheque'=>'Chèque']; @endphp
                        @foreach($modes as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('mode_paiement_prefere', $profil?->mode_paiement_prefere) === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="numero_compte" class="f-label">Numéro / RIB associé</label>
                        <input id="numero_compte" type="text" name="numero_compte" value="{{ old('numero_compte', $profil?->numero_compte) }}" class="f-input">
                    </div>
                    <div>
                        <label for="banque" class="f-label">Banque <span class="text-muted font-normal">(si virement)</span></label>
                        <input id="banque" type="text" name="banque" value="{{ old('banque', $profil?->banque) }}" class="f-input">
                    </div>
                </div>
            </div>

            {{-- Fiscalité --}}
            <div class="f-card">
                <h3 class="f-card-title">Fiscalité</h3>
                <div class="flex items-center justify-between gap-5 pt-1">
                    <div>
                        <div class="text-[14.5px] font-bold">Assujetti à la TVA</div>
                        <div class="text-[12.5px] text-muted mt-0.5 leading-snug">Active le calcul automatique de la TVA sur les quittances de ce propriétaire.</div>
                    </div>
                    <button type="button" x-on:click="toggleTva" x-bind:class="tvaSwitchClass"
                            class="relative w-[42px] h-6 rounded-full shrink-0 transition-colors" aria-label="Assujetti à la TVA">
                        <span x-bind:class="tvaKnobClass" class="absolute top-[2.5px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between gap-5 pt-4 mt-4 border-t border-paper-dim">
                    <div>
                        <div class="text-[14.5px] font-bold">Dispensé de retenue à la source (BRS)</div>
                        <div class="text-[12.5px] text-muted mt-0.5 leading-snug">Par défaut, la BRS de 5% est retenue pour un bailleur personne physique (loyer ≥ 150 000 F). N'activez que si ce propriétaire justifie d'une dispense DGID.</div>
                    </div>
                    <button type="button" x-on:click="toggleBrsDispense" x-bind:class="brsSwitchClass"
                            class="relative w-[42px] h-6 rounded-full shrink-0 transition-colors" aria-label="Dispensé de BRS">
                        <span x-bind:class="brsKnobClass" class="absolute top-[2.5px] w-[19px] h-[19px] rounded-full bg-white shadow transition-all"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="lg:sticky lg:top-6 space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Pièce d'identité</h3>
                <p class="f-card-sub">CNI, passeport ou registre de commerce.</p>
                @if($profil?->piece_identite_path)
                    <div class="flex items-center gap-3 mb-3 p-3 border border-line rounded-[10px] bg-paper">
                        <x-icon name="file-text" size="18" class="text-teal shrink-0" />
                        <span class="text-[13px] font-semibold flex-1 truncate">{{ basename($profil->piece_identite_path) }}</span>
                        <a href="{{ Storage::disk('public')->url($profil->piece_identite_path) }}" target="_blank" rel="noopener" class="text-[13px] font-bold text-teal hover:underline shrink-0">Ouvrir</a>
                    </div>
                @endif
                <label for="piece_identite" class="block border-[1.5px] border-dashed border-line rounded-[11px] p-5 text-center bg-paper cursor-pointer hover:border-teal transition-colors">
                    <x-icon name="file-text" size="22" class="block mx-auto mb-2 text-muted" />
                    <span class="block text-[13px] font-semibold text-teal" x-text="pieceLabel">{{ $profil?->piece_identite_path ? 'Remplacer le fichier' : 'Choisir un fichier' }}</span>
                    <span class="block text-[11.5px] text-muted mt-1">JPG, PNG, WEBP ou PDF — 5 Mo max</span>
                </label>
                <input id="piece_identite" type="file" name="piece_identite"
                       accept="image/jpeg,image/png,image/webp,application/pdf"
                       x-on:change="pickPiece" class="hidden">
                @error('piece_identite')<p class="field-error mt-2">{{ $message }}</p>@enderror
            </div>
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Enregistrer les modifications</button>
                <a href="{{ route('admin.users.show', $user) }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
            </div>
        </div>
    </div>
</form>
@endunless
@endsection
