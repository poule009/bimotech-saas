@extends('layouts.app')

@php $estProprio = $role === 'proprietaire'; @endphp

@section('title', $estProprio ? 'Nouveau propriétaire' : 'Nouveau locataire')
@section('page-title', $estProprio ? 'Nouveau propriétaire' : 'Nouveau locataire')
@section('page-subtitle')
    <a href="{{ $estProprio ? route('admin.users.proprietaires') : route('admin.users.locataires') }}" class="text-teal font-semibold hover:underline">{{ $estProprio ? 'Propriétaires' : 'Locataires' }}</a>
    <span class="text-muted"> / Nouveau</span>
@endsection

@section('content')
@unless($estProprio)
<form method="POST" action="{{ route('admin.users.store') }}" x-data="tenantForm"
      data-tenant-type="{{ old('type_locataire', 'particulier') }}" class="max-w-[1000px]">
    @csrf
    <input type="hidden" name="role" value="locataire">
    <input type="hidden" name="type_locataire" x-bind:value="typeValue">
    <input type="hidden" name="est_entreprise" x-bind:value="estEntrepriseValue">

    <div class="mb-5 rounded-lg bg-teal/10 border border-teal/25 px-4 py-3 text-[13px] text-teal leading-relaxed">
        💡 En général, un locataire se crée directement depuis un contrat. Ce formulaire sert à le <strong>préenregistrer</strong> avant la signature.
    </div>

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
                <p class="f-card-sub">Particulier ou bureau/société — ajuste les cases fiscales (BRS, NINEA).</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-6">
                    <button type="button" x-on:click="setParticulier" x-bind:class="forkParticulierClass" class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1"><span class="text-[22px]">🧍</span><span class="font-bold text-[15px]">Particulier</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Une personne physique.</div>
                    </button>
                    <button type="button" x-on:click="setEntreprise" x-bind:class="forkEntrepriseClass" class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1"><span class="text-[22px]">🏢</span><span class="font-bold text-[15px]">Bureau / Société</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Entreprise, association…</div>
                    </button>
                </div>

                <div class="mb-[18px]">
                    <label for="name" class="f-label">
                        <span x-show="isParticulier">Prénom et nom</span>
                        <span x-show="isEntreprise" x-cloak>Nom du contact</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required placeholder="Ex. Moussa Fall" class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- Champs entreprise --}}
                <div x-show="isEntreprise" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2"><label for="nom_entreprise" class="f-label">Raison sociale</label><input id="nom_entreprise" type="text" name="nom_entreprise" value="{{ old('nom_entreprise') }}" placeholder="Ex. Cabinet Juris & Co" class="f-input"></div>
                    <div><label for="ninea_locataire" class="f-label">NINEA</label><input id="ninea_locataire" type="text" name="ninea_locataire" value="{{ old('ninea_locataire') }}" class="f-input"></div>
                    <div><label for="rccm_locataire" class="f-label">RCCM <span class="text-muted font-normal">(optionnel)</span></label><input id="rccm_locataire" type="text" name="rccm_locataire" value="{{ old('rccm_locataire') }}" class="f-input"></div>
                </div>
            </div>

            <div class="f-card">
                <h3 class="f-card-title">Coordonnées</h3>
                <p class="f-card-sub">Le téléphone est prioritaire — c'est le canal de relance principal.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="telephone" class="f-label">Téléphone (WhatsApp)</label>
                        <input id="telephone" type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+221 76 123 45 67" class="f-input">
                    </div>
                    <div>
                        <label for="email" class="f-label">Email <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nom@mail.com" class="f-input @error('email') f-input-error @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-[18px]">
                    <div><label for="profession" class="f-label">Profession <span class="text-muted font-normal">(optionnel)</span></label><input id="profession" type="text" name="profession" value="{{ old('profession') }}" class="f-input"></div>
                    <div><label for="cni" class="f-label">N° CNI <span class="text-muted font-normal">(optionnel)</span></label><input id="cni" type="text" name="cni" value="{{ old('cni') }}" class="f-input"></div>
                </div>
            </div>
        </div>

        <div class="lg:sticky lg:top-6 space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Pièce d'identité</h3>
                <p class="f-card-sub">CNI ou passeport.</p>
                <div class="border-[1.5px] border-dashed border-line rounded-[11px] p-6 text-center text-[13px] text-muted bg-paper">
                    <span class="text-[22px] block mb-2">📄</span>Import de fichier
                    <span class="block text-[11px] uppercase tracking-wide bg-paper-dim text-muted px-2 py-0.5 rounded font-bold mt-2 inline-block">Bientôt disponible</span>
                </div>
            </div>
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Créer le locataire</button>
                <a href="{{ route('admin.users.locataires') }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
                <p class="text-[12.5px] text-muted leading-relaxed mt-4 pt-4 border-t border-paper-dim">
                    Ce locataire sera proposé dans le champ « Locataire » à la création d'un contrat. Le <strong>garant</strong> se renseigne sur le contrat.
                </p>
            </div>
        </div>
    </div>
</form>
@else
<form method="POST" action="{{ route('admin.users.store') }}" x-data="ownerForm"
      data-owner-type="{{ old('est_personne_morale_is') === '1' ? 'entreprise' : 'particulier' }}"
      data-owner-tva="{{ old('assujetti_tva') === '1' ? '1' : '0' }}"
      class="max-w-[1100px]">
    @csrf
    <input type="hidden" name="role" value="proprietaire">
    <input type="hidden" name="est_personne_morale_is" x-bind:value="moraleValue">
    <input type="hidden" name="assujetti_tva" x-bind:value="tvaValue">

    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">
            <strong class="font-bold">Vérifiez le formulaire :</strong>
            <ul class="list-disc pl-5 mt-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.5fr_1fr] gap-6 items-start">

        {{-- Colonne principale --}}
        <div class="space-y-5">

            {{-- Type de propriétaire --}}
            <div class="f-card">
                <h3 class="f-card-title">Type de propriétaire</h3>
                <p class="f-card-sub">On adapte les champs juste après selon votre choix.</p>

                <div class="bg-paper border border-line rounded-[10px] px-4 py-3 text-[13px] text-muted leading-relaxed mb-4">
                    💡 <strong class="text-ink">Pourquoi cette question ?</strong> Un particulier n'a pas de NINEA et n'est pas taxé comme une entreprise. Ce choix règle les bonnes cases fiscales sur ses quittances.
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-6">
                    <button type="button" x-on:click="setParticulier" x-bind:class="forkParticulierClass"
                            class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1.5"><span class="text-[22px]">🧍</span><span class="font-bold text-[15px]">Particulier</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Une personne, propriétaire en son nom propre.</div>
                    </button>
                    <button type="button" x-on:click="setEntreprise" x-bind:class="forkEntrepriseClass"
                            class="text-left border-2 rounded-xl p-4 transition-all">
                        <div class="flex items-center gap-2.5 mb-1.5"><span class="text-[22px]">🏢</span><span class="font-bold text-[15px]">Entreprise</span></div>
                        <div class="text-[12.5px] text-muted leading-snug">Société, SCI ou groupe propriétaire du bien.</div>
                    </button>
                </div>

                {{-- Nom / raison sociale (champ unique, libellé adaptatif) --}}
                <div class="mb-[18px]">
                    <label for="name" class="f-label">
                        <span x-show="isParticulier">Prénom et nom</span>
                        <span x-show="isEntreprise" x-cloak>Raison sociale</span>
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                           placeholder="Ex. Cheikh Diop"
                           class="f-input @error('name') f-input-error @enderror">
                    @error('name')<p class="field-error">{{ $message }}</p>@enderror
                </div>

                {{-- CNI (particulier) --}}
                <div class="mb-[18px]" x-show="isParticulier">
                    <label for="cni" class="f-label">N° CNI ou passeport <span class="text-muted font-normal">(optionnel)</span></label>
                    <input id="cni" type="text" name="cni" value="{{ old('cni') }}" placeholder="1 234 5678 9012" class="f-input">
                </div>

                {{-- NINEA (entreprise) --}}
                <div class="mb-[18px]" x-show="isEntreprise" x-cloak>
                    <label for="ninea" class="f-label">NINEA</label>
                    <input id="ninea" type="text" name="ninea" value="{{ old('ninea') }}" placeholder="004578221 2" class="f-input">
                </div>
            </div>

            {{-- Coordonnées --}}
            <div class="f-card">
                <h3 class="f-card-title">Coordonnées</h3>
                <p class="f-card-sub">Utilisées pour les relances et l'envoi des documents.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="telephone" class="f-label">Téléphone</label>
                        <input id="telephone" type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+221 77 123 45 67" class="f-input">
                    </div>
                    <div>
                        <label for="email" class="f-label">Email <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nom@mail.com" class="f-input @error('email') f-input-error @enderror">
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-[18px]">
                    <div>
                        <label for="ville" class="f-label">Ville</label>
                        <input id="ville" type="text" name="ville" value="{{ old('ville', 'Dakar') }}" placeholder="Dakar" class="f-input">
                    </div>
                    <div>
                        <label for="adresse" class="f-label">Adresse <span class="text-muted font-normal">(optionnel)</span></label>
                        <input id="adresse" type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Quartier, rue…" class="f-input">
                    </div>
                </div>
            </div>

            {{-- Versement des loyers --}}
            <div class="f-card">
                <h3 class="f-card-title">Versement des loyers</h3>
                <p class="f-card-sub">Comment ce propriétaire est-il payé ?</p>
                <div class="mb-[18px]">
                    <label for="mode_paiement_prefere" class="f-label">Mode de versement préféré</label>
                    <select id="mode_paiement_prefere" name="mode_paiement_prefere" class="f-select">
                        @php $modes = ['wave'=>'Wave','orange_money'=>'Orange Money','virement'=>'Virement bancaire','especes'=>'Espèces','cheque'=>'Chèque']; @endphp
                        @foreach($modes as $val => $lbl)
                            <option value="{{ $val }}" @selected(old('mode_paiement_prefere', 'wave') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="numero_compte" class="f-label">Numéro / RIB associé</label>
                        <input id="numero_compte" type="text" name="numero_compte" value="{{ old('numero_compte') }}" placeholder="77 123 45 67" class="f-input">
                    </div>
                    <div>
                        <label for="banque" class="f-label">Banque <span class="text-muted font-normal">(si virement)</span></label>
                        <input id="banque" type="text" name="banque" value="{{ old('banque') }}" placeholder="Ex. CBAO" class="f-input">
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
                <div class="flex items-center justify-between gap-5 pt-4 mt-4 border-t border-paper-dim opacity-60">
                    <div>
                        <div class="text-[14.5px] font-bold flex items-center gap-2">Soumis au BRS <span class="text-[10px] uppercase tracking-wide bg-paper-dim text-muted px-1.5 py-0.5 rounded font-bold">Bientôt</span></div>
                        <div class="text-[12.5px] text-muted mt-0.5 leading-snug">Retenue à la source — sera géré dans le module fiscalité propriétaire.</div>
                    </div>
                    <div class="w-[42px] h-6 rounded-full bg-line shrink-0"></div>
                </div>
            </div>
        </div>

        {{-- Colonne latérale (sticky) --}}
        <div class="lg:sticky lg:top-6 space-y-5">
            <div class="f-card">
                <h3 class="f-card-title">Pièce d'identité</h3>
                <p class="f-card-sub">CNI, passeport ou registre de commerce.</p>
                <div class="border-[1.5px] border-dashed border-line rounded-[11px] p-6 text-center text-[13px] text-muted bg-paper">
                    <span class="text-[22px] block mb-2">📄</span>
                    Import de fichier
                    <span class="block text-[11px] uppercase tracking-wide bg-paper-dim text-muted px-2 py-0.5 rounded font-bold mt-2 inline-block">Bientôt disponible</span>
                </div>
            </div>
            <div class="f-card">
                <button type="submit" class="btn-primary mb-2.5">Créer le propriétaire</button>
                <a href="{{ route('admin.users.proprietaires') }}" class="block w-full text-center py-[13px] rounded border-[1.5px] border-line bg-white text-ink text-sm font-semibold hover:border-teal transition-colors">Annuler</a>
                <p class="text-[12.5px] text-muted leading-relaxed mt-4 pt-4 border-t border-paper-dim">
                    Une fois créé, ce propriétaire sera proposé automatiquement dans le champ « Propriétaire » à la création d'un bien.
                </p>
            </div>
        </div>
    </div>
</form>
@endunless
@endsection
