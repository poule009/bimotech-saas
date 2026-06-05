@extends('layouts.app')
@section('header', 'Paramètres agence')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-5 items-start max-w-5xl">

    {{-- FORMULAIRE --}}
    <form method="POST" action="{{ route('admin.agency.settings.update') }}" enctype="multipart/form-data" id="settings-form">
    @csrf @method('PATCH')

        {{-- IDENTITÉ VISUELLE --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden mb-4">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Identité visuelle</span>
            </div>
            <div class="px-5 py-5 space-y-6">

                {{-- Logo principal --}}
                <div class="space-y-2">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Logo <span class="font-normal text-bimo-text/40 text-xs ml-1">PNG, JPG · max 2 Mo</span>
                    </label>
                    <div id="logo-zone" onclick="document.getElementById('logo-input').click()"
                         class="flex items-center gap-4 p-4 bg-bimo-bg border-2 border-dashed border-bimo-navy/15 rounded-[10px] cursor-pointer
                                hover:border-bimo-gold/50 hover:bg-bimo-gold/[3%] transition-all duration-150">
                        <div id="logo-preview" class="w-16 h-16 rounded-[10px] border border-bimo-navy/10 bg-bimo-bg2 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($agency->logo_path && Storage::disk('public')->exists($agency->logo_path))
                                <img src="{{ Storage::url($agency->logo_path) }}" alt="{{ $agency->name }}" id="logo-preview-img" class="w-full h-full object-contain">
                            @else
                                <div id="logo-preview-placeholder" class="font-display font-extrabold text-xl text-bimo-gold">
                                    {{ mb_strtoupper(mb_substr($agency->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <div class="font-body font-medium text-sm text-bimo-text">Cliquer pour changer le logo</div>
                            <div class="font-body text-xs text-bimo-text/40 mt-0.5">Recommandé : 200×200 px · fond transparent</div>
                        </div>
                        <input type="file" name="logo" id="logo-input" accept="image/png,image/jpeg,image/webp" style="display:none" onchange="previewLogo(this)">
                    </div>
                    @error('logo')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                    @if($agency->logo_path)
                    <button type="button" onclick="if(confirm('Supprimer le logo ?')) document.getElementById('form-delete-logo').submit()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-red/10 border border-bimo-red/20 rounded-[7px] font-body text-xs text-bimo-red hover:bg-bimo-red/20 transition-all duration-150">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        Supprimer le logo
                    </button>
                    @endif
                </div>

                {{-- Logo fond sombre --}}
                <div class="space-y-2">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Logo fond sombre <span class="font-normal text-bimo-text/40 text-xs ml-1">Version claire/blanche pour les PDF à en-tête noir</span>
                    </label>
                    <div class="flex items-start gap-2 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[8px] px-3 py-2.5 mb-2">
                        <svg class="w-3.5 h-3.5 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <p class="font-body text-[11px] text-bimo-text/50 leading-relaxed">Utilisé dans les en-têtes sombres des quittances, baux et relevés. Logo blanc sur fond transparent (PNG) recommandé.</p>
                    </div>
                    <div id="logo-dark-zone" onclick="document.getElementById('logo-dark-input').click()"
                         class="flex items-center gap-4 p-4 bg-bimo-navy border-2 border-dashed border-white/10 rounded-[10px] cursor-pointer
                                hover:border-bimo-gold/40 transition-all duration-150">
                        <div id="logo-dark-preview" class="w-16 h-16 rounded-[10px] border border-white/10 bg-bimo-navy-dk flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($agency->logo_dark_path && Storage::disk('public')->exists($agency->logo_dark_path))
                                <img src="{{ Storage::url($agency->logo_dark_path) }}" alt="Logo fond sombre" id="logo-dark-preview-img" class="w-full h-full object-contain">
                            @else
                                <svg class="w-6 h-6 text-white/20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            @endif
                        </div>
                        <div>
                            <div class="font-body font-medium text-sm text-white/80">Ajouter le logo fond sombre</div>
                            <div class="font-body text-xs text-white/30 mt-0.5">Logo blanc sur fond transparent · PNG recommandé</div>
                        </div>
                        <input type="file" name="logo_dark" id="logo-dark-input" accept="image/png,image/jpeg,image/webp" style="display:none" onchange="previewLogoDark(this)">
                    </div>
                    @error('logo_dark')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                    @if($agency->logo_dark_path)
                    <button type="button" onclick="if(confirm('Supprimer le logo fond sombre ?')) document.getElementById('form-delete-logo-dark').submit()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-red/10 border border-bimo-red/20 rounded-[7px] font-body text-xs text-bimo-red hover:bg-bimo-red/20 transition-all duration-150">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        Supprimer
                    </button>
                    @endif
                </div>

                {{-- Signature --}}
                <div class="space-y-2">
                    <label class="block font-body font-medium text-sm text-bimo-text">
                        Signature / Tampon <span class="font-normal text-bimo-text/40 text-xs ml-1">PNG fond transparent · max 1 Mo</span>
                    </label>
                    <div id="sig-zone" onclick="document.getElementById('sig-input').click()"
                         class="flex items-center gap-4 p-4 bg-bimo-bg border-2 border-dashed border-bimo-navy/15 rounded-[10px] cursor-pointer
                                hover:border-bimo-gold/50 hover:bg-bimo-gold/[3%] transition-all duration-150">
                        <div id="sig-preview" class="w-16 h-16 rounded-[10px] border border-bimo-navy/10 bg-bimo-bg2 flex items-center justify-center overflow-hidden flex-shrink-0">
                            @if($agency->signature_path && Storage::disk('public')->exists($agency->signature_path))
                                <img src="{{ Storage::url($agency->signature_path) }}" alt="Signature" id="sig-preview-img" class="max-h-14 object-contain">
                            @else
                                <span class="font-body text-[10px] text-bimo-text/30 text-center px-1">Aucune signature</span>
                            @endif
                        </div>
                        <div>
                            <div class="font-body font-medium text-sm text-bimo-text">Ajouter la signature</div>
                            <div class="font-body text-xs text-bimo-text/40 mt-0.5">Signature ou tampon officiel · fond transparent</div>
                        </div>
                        <input type="file" name="signature" id="sig-input" accept="image/png,image/jpeg,image/webp" style="display:none" onchange="previewSignature(this)">
                    </div>
                    @error('signature')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                    @if($agency->signature_path)
                    <button type="button" onclick="if(confirm('Supprimer la signature ?')) document.getElementById('form-delete-signature').submit()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-bimo-red/10 border border-bimo-red/20 rounded-[7px] font-body text-xs text-bimo-red hover:bg-bimo-red/20 transition-all duration-150">
                        <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                        Supprimer la signature
                    </button>
                    @endif
                </div>

                {{-- Couleur --}}
                <div class="space-y-2">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="couleur_primaire">Couleur principale</label>
                    <div class="flex items-center gap-3 mb-3">
                        <input type="color" id="color-native"
                               value="{{ old('couleur_primaire', $agency->couleur_primaire ?? '#c9a84c') }}"
                               oninput="syncColor(this.value)"
                               class="w-10 h-10 rounded-[8px] border border-bimo-navy/20 cursor-pointer p-1 bg-white">
                        <input type="text" name="couleur_primaire" id="couleur_primaire"
                               value="{{ old('couleur_primaire', $agency->couleur_primaire ?? '#c9a84c') }}"
                               placeholder="#c9a84c" maxlength="7"
                               oninput="syncColorFromHex(this.value)"
                               class="flex-1 px-4 py-2.5 rounded-[9px] bg-white border border-bimo-navy/20 font-mono text-sm text-bimo-text
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150
                                      @error('couleur_primaire') border-bimo-red @enderror">
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach(['#c9a84c','#3b82f6','#22c55e','#ef4444','#8b5cf6','#0d1117','#06b6d4','#f59e0b','#1a3c5e','#ec4899'] as $color)
                        <div onclick="syncColor('{{ $color }}')" title="{{ $color }}"
                             class="w-7 h-7 rounded-[6px] cursor-pointer transition-all duration-150 hover:scale-110 border-2 border-transparent
                                    {{ ($agency->couleur_primaire ?? '#c9a84c') === $color ? 'border-white shadow-md scale-110' : '' }} color-swatch"
                             style="background:{{ $color }}"></div>
                        @endforeach
                    </div>
                    @error('couleur_primaire')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                    <p class="font-body text-[11px] text-bimo-text/40">Utilisée pour la sidebar, les badges et les accents.</p>
                </div>

            </div>
        </div>

        {{-- INFORMATIONS AGENCE --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden mb-4">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations de l'agence</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="name">Nom de l'agence <span class="text-bimo-red">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $agency->name) }}" placeholder="Ex : Immobilière Dakar"
                           oninput="updatePreview()"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('name')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="email">Email <span class="text-bimo-red">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $agency->email) }}" placeholder="contact@agence.sn"
                               oninput="updatePreview()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('email')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="telephone">Téléphone</label>
                        <input type="text" name="telephone" id="telephone" value="{{ old('telephone', $agency->telephone) }}" placeholder="+221 77 XXX XX XX"
                               oninput="updatePreview()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="whatsapp">
                        WhatsApp portail <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                    </label>
                    <input type="text" name="whatsapp" id="whatsapp" value="{{ old('whatsapp', $agency->whatsapp) }}" placeholder="+221771234567"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                  @error('whatsapp') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                  @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('whatsapp')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                    <p class="font-body text-[11px] text-bimo-text/40">Numéro utilisé pour le bouton WhatsApp sur le portail public. Format : +221771234567 sans espaces.</p>
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="adresse">
                        Adresse <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                    </label>
                    <input type="text" name="adresse" id="adresse" value="{{ old('adresse', $agency->adresse) }}" placeholder="Ex : 12 Avenue Cheikh Anta Diop, Dakar"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                  placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>
        </div>

        {{-- INFORMATIONS LÉGALES --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Informations légales</span>
            </div>
            <div class="px-5 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="ninea">
                            NINEA <span class="font-normal text-bimo-text/40 text-xs ml-1">Numéro fiscal</span>
                        </label>
                        <input type="text" name="ninea" id="ninea" value="{{ old('ninea', $agency->ninea) }}" placeholder="00123456789" maxlength="30"
                               oninput="updatePreview()"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('ninea') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('ninea')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
                        @if(!$agency->ninea)
                        <p class="font-body text-[11px] text-amber-600">⚠ Requis pour les quittances conformes</p>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="rccm">
                            RCCM <span class="font-normal text-bimo-text/40 text-xs ml-1">Registre de commerce</span>
                        </label>
                        <input type="text" name="rccm" id="rccm" value="{{ old('rccm', $agency->rccm ?? '') }}" placeholder="SN-DKR-2024-XXX" maxlength="50"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                </div>
                <p class="font-body text-[11px] text-bimo-text/40">Ces informations apparaissent sur toutes les quittances PDF.</p>

                {{-- Modèle de bail --}}
                <div class="pt-4 border-t border-bimo-navy/[5%]">
                    <div class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-3">Modèle de contrat de bail</div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text" for="modele_contrat">
                            Clauses générales de l'agence
                            <span class="font-normal text-bimo-text/40 text-xs ml-1">Insérées automatiquement dans tous vos baux PDF</span>
                        </label>
                        <textarea name="modele_contrat" id="modele_contrat" rows="8" maxlength="10000"
                                  placeholder="Ex : Le locataire s'interdit tout dépôt d'ordures dans les parties communes...&#10;Le bailleur se réserve le droit de visite avec préavis de 48h..."
                                  class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                         placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                         transition-all duration-150 resize-y">{{ old('modele_contrat', $agency->modele_contrat) }}</textarea>
                        <p class="font-body text-[11px] text-bimo-text/40 leading-relaxed">
                            Écrivez vos clauses habituelles ici une seule fois. Pour les conditions spécifiques à un bail, utilisez le champ "Clauses particulières" sur la fiche du contrat.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Submit --}}
            <div class="sticky bottom-0 px-5 py-4 bg-white/95 backdrop-blur-sm border-t border-bimo-navy/[5%]">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px]
                               hover:opacity-90 transition-opacity duration-150 shadow-gold-sm">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Sauvegarder les paramètres
                </button>
            </div>
        </div>

    </form>

    {{-- COLONNE DROITE --}}
    <div class="lg:sticky lg:top-6 space-y-4">

        {{-- Aperçu sidebar --}}
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3.5 border-b border-white/[7%]">
                <span class="font-body font-medium text-[9px] uppercase tracking-widest text-white/30">Aperçu sidebar</span>
                <div class="w-1.5 h-1.5 rounded-full bg-bimo-gold" style="animation:pulse-dot 2s ease-in-out infinite" title="Aperçu en direct"></div>
            </div>

            {{-- Mini sidebar --}}
            <div class="p-3">
                <div class="bg-bimo-navy-dk rounded-[10px] overflow-hidden">
                    <div class="flex items-center gap-2 px-3 py-3 border-b border-white/[7%]">
                        <div id="preview-logo-box" class="w-7 h-7 rounded-[6px] flex items-center justify-center font-display font-extrabold text-[10px] text-white flex-shrink-0"
                             style="background: {{ $agency->couleur_primaire ?? '#c9a84c' }}">
                            @if($agency->logo_path && Storage::disk('public')->exists($agency->logo_path))
                                <img src="{{ Storage::url($agency->logo_path) }}" class="w-full h-full object-contain rounded" id="preview-logo-img" alt="">
                            @else
                                <span id="preview-logo-initials">{{ mb_strtoupper(mb_substr($agency->name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div id="preview-name" class="font-display font-bold text-[11px] text-white truncate">{{ $agency->name }}</div>
                            <div class="font-body text-[8px] text-white/25 uppercase tracking-wider">Admin</div>
                        </div>
                    </div>
                    <div class="px-2 py-2 space-y-0.5">
                        <div id="preview-active-item" class="flex items-center gap-2 px-2 py-2 rounded-[6px]"
                             style="background: rgba(201,168,76,0.13)">
                            <div id="preview-active-dot" class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background:#c9a84c;box-shadow:0 0 0 3px rgba(201,168,76,.2)"></div>
                            <div class="h-1.5 flex-1 rounded-sm" style="background:#c9a84c;opacity:.5"></div>
                        </div>
                        @foreach([0,1,2,3] as $i)
                        <div class="flex items-center gap-2 px-2 py-2 rounded-[6px]">
                            <div class="w-1.5 h-1.5 rounded-full bg-white/20 flex-shrink-0"></div>
                            <div class="h-1.5 rounded-sm bg-white/10 {{ $i % 2 ? 'w-2/3' : 'flex-1' }}"></div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Infos recap --}}
            <div class="mx-3 mb-3 bg-white/[3%] rounded-[8px] divide-y divide-white/[4%]">
                @foreach([
                    ['info-name',  'Nom',      $agency->name, false],
                    ['info-email', 'Email',    $agency->email ?? '—', false],
                    ['info-tel',   'Téléphone',$agency->telephone ?? 'Non renseigné', !$agency->telephone],
                    ['info-ninea', 'NINEA',    $agency->ninea ?? 'Non renseigné', !$agency->ninea],
                ] as [$id, $lbl, $val, $missing])
                <div class="flex items-center justify-between px-3.5 py-2.5">
                    <span class="font-body text-[11px] text-white/30">{{ $lbl }}</span>
                    <span id="{{ $id }}" class="font-body text-[11px] font-medium max-w-[140px] truncate text-right {{ $missing ? 'text-bimo-red italic' : 'text-white/75' }}">{{ $val }}</span>
                </div>
                @endforeach
                <div class="flex items-center justify-between px-3.5 py-2.5">
                    <span class="font-body text-[11px] text-white/30">Couleur</span>
                    <div class="flex items-center gap-2">
                        <div id="info-color-dot" class="w-3 h-3 rounded-[3px]" style="background:{{ $agency->couleur_primaire ?? '#c9a84c' }}"></div>
                        <span id="info-color" class="font-body text-[11px] text-white/75">{{ $agency->couleur_primaire ?? '#c9a84c' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Checklist --}}
        @php
            $checks = [
                ['label' => 'Nom de l\'agence', 'done' => !empty($agency->name)],
                ['label' => 'Email de contact', 'done' => !empty($agency->email)],
                ['label' => 'Téléphone',        'done' => !empty($agency->telephone)],
                ['label' => 'Adresse physique', 'done' => !empty($agency->adresse)],
                ['label' => 'NINEA fiscal',     'done' => !empty($agency->ninea)],
                ['label' => 'Logo uploadé',     'done' => !empty($agency->logo_path)],
                ['label' => 'Couleur définie',  'done' => !empty($agency->couleur_primaire)],
                ['label' => 'Modèle de bail',   'done' => !empty($agency->modele_contrat)],
            ];
            $nbDone = collect($checks)->where('done', true)->count();
            $pct    = round($nbDone / count($checks) * 100);
        @endphp
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/5 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-bimo-text/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-bimo-text">Configuration</span>
            </div>
            <div class="px-5 py-5">
                <div class="flex items-center justify-between mb-3">
                    <span class="font-body font-medium text-sm text-bimo-text">{{ $nbDone }}/{{ count($checks) }} complété{{ $nbDone > 1 ? 's':'' }}</span>
                    <span class="font-display font-bold text-sm {{ $pct === 100 ? 'text-bimo-gold' : 'text-amber-500' }}">{{ $pct }}%</span>
                </div>
                <div class="h-1.5 bg-bimo-navy/10 rounded-full mb-4 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300 {{ $pct === 100 ? 'bg-bimo-gold' : 'bg-bimo-gold' }}" style="width:{{ $pct }}%"></div>
                </div>
                <div class="space-y-2">
                    @foreach($checks as $check)
                    <div class="flex items-center gap-3 px-3 py-2 rounded-[8px] {{ $check['done'] ? 'bg-bimo-gold/[6%]' : 'bg-amber-50 border border-amber-100' }}">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0
                                    {{ $check['done'] ? 'bg-bimo-gold/25' : 'bg-amber-100' }}">
                            @if($check['done'])
                            <svg class="w-2.5 h-2.5 text-bimo-gold" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                            @else
                            <span class="font-body font-bold text-[10px] text-amber-600">!</span>
                            @endif
                        </div>
                        <span class="font-body text-xs {{ $check['done'] ? 'text-bimo-gold' : 'text-amber-700' }}">{{ $check['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Forms suppression (hors form principal) --}}
@if($agency->logo_path)
<form id="form-delete-logo" method="POST" action="{{ route('admin.agency.logo.delete') }}" style="display:none">@csrf @method('DELETE')</form>
@endif
@if($agency->logo_dark_path)
<form id="form-delete-logo-dark" method="POST" action="{{ route('admin.agency.logo-dark.delete') }}" style="display:none">@csrf @method('DELETE')</form>
@endif
@if($agency->signature_path)
<form id="form-delete-signature" method="POST" action="{{ route('admin.agency.signature.delete') }}" style="display:none">@csrf @method('DELETE')</form>
@endif

@push('scripts')
<script>
function syncColor(hex) {
    if (!hex.startsWith('#')) hex = '#' + hex;
    document.getElementById('couleur_primaire').value = hex;
    document.getElementById('color-native').value = hex;
    const r = parseInt(hex.slice(1,3),16), g = parseInt(hex.slice(3,5),16), b = parseInt(hex.slice(5,7),16);
    const box = document.getElementById('preview-logo-box');
    const activeItem = document.getElementById('preview-active-item');
    const activeDot  = document.getElementById('preview-active-dot');
    box.style.background = hex;
    activeItem.style.background = `rgba(${r},${g},${b},.13)`;
    activeDot.style.background = hex;
    activeDot.style.boxShadow = `0 0 0 3px rgba(${r},${g},${b},.2)`;
    activeDot.nextElementSibling.style.background = hex;
    document.getElementById('info-color-dot').style.background = hex;
    document.getElementById('info-color').textContent = hex;
    document.querySelectorAll('.color-swatch').forEach(s => {
        const sw = rgbToHex(s.style.background);
        const isActive = sw === hex || s.style.background === hex;
        s.style.borderColor = isActive ? '#fff' : 'transparent';
        s.style.transform = isActive ? 'scale(1.1)' : '';
    });
}
function syncColorFromHex(val) { if (/^#[0-9A-Fa-f]{6}$/.test(val)) syncColor(val); }
function rgbToHex(rgb) { const m = rgb.match(/\d+/g); if (!m) return ''; return '#' + m.slice(0,3).map(x => parseInt(x).toString(16).padStart(2,'0')).join(''); }
function updatePreview() {
    const name = document.getElementById('name').value || '—';
    const email = document.getElementById('email').value || '—';
    const tel = document.getElementById('telephone').value;
    const ninea = document.getElementById('ninea').value;
    document.getElementById('preview-name').textContent = name;
    document.getElementById('info-name').textContent = name;
    document.getElementById('info-email').textContent = email;
    const telEl = document.getElementById('info-tel');
    telEl.textContent = tel || 'Non renseigné';
    telEl.className = telEl.className.replace(/text-bimo-red|text-white\/75/g, '');
    telEl.classList.add(tel ? 'text-white/75' : 'text-bimo-red');
    const nineaEl = document.getElementById('info-ninea');
    nineaEl.textContent = ninea || 'Non renseigné';
    nineaEl.className = nineaEl.className.replace(/text-bimo-red|text-white\/75/g, '');
    nineaEl.classList.add(ninea ? 'text-white/75' : 'text-bimo-red');
    const initEl = document.getElementById('preview-logo-initials');
    if (initEl) initEl.textContent = name.substring(0,2).toUpperCase();
}
function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const box = document.getElementById('preview-logo-box');
        const zone = document.querySelector('#logo-preview');
        let sidebarImg = document.getElementById('preview-logo-img');
        if (!sidebarImg) {
            const initials = document.getElementById('preview-logo-initials');
            if (initials) initials.remove();
            sidebarImg = document.createElement('img');
            sidebarImg.id = 'preview-logo-img';
            sidebarImg.className = 'w-full h-full object-contain rounded';
            box.appendChild(sidebarImg);
        }
        sidebarImg.src = e.target.result;
        let zoneImg = document.getElementById('logo-preview-img');
        if (!zoneImg) {
            const ph = document.getElementById('logo-preview-placeholder');
            if (ph) ph.remove();
            zoneImg = document.createElement('img');
            zoneImg.id = 'logo-preview-img';
            zoneImg.className = 'w-full h-full object-contain';
            zone.appendChild(zoneImg);
        }
        zoneImg.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
function previewLogoDark(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const zone = document.getElementById('logo-dark-preview');
        let img = document.getElementById('logo-dark-preview-img');
        if (!img) {
            zone.innerHTML = '';
            img = document.createElement('img');
            img.id = 'logo-dark-preview-img';
            img.className = 'w-full h-full object-contain';
            zone.appendChild(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
function previewSignature(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => {
        const zone = document.getElementById('sig-preview');
        let img = document.getElementById('sig-preview-img');
        if (!img) {
            zone.innerHTML = '';
            img = document.createElement('img');
            img.id = 'sig-preview-img';
            img.className = 'max-h-14 object-contain';
            zone.appendChild(img);
        }
        img.src = e.target.result;
    };
    reader.readAsDataURL(input.files[0]);
}
updatePreview();
syncColor(document.getElementById('couleur_primaire').value);
</script>
@endpush

@endsection
