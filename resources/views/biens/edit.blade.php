<x-app-layout>
    <x-slot name="header">Modifier — {{ $bien->reference }}</x-slot>

    {{-- Header navigation --}}
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="{{ route('biens.show', $bien) }}"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                Modifier — {{ $bien->reference }}
            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                {{ $bien->type }} · {{ $bien->adresse }}, {{ $bien->ville }}
            </p>
        </div>
    </div>

    <div style="max-width:680px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('biens.update', $bien) }}">
                    @csrf @method('PUT')

                    {{-- Propriétaire (admin seulement) --}}
                    @if(auth()->user()->isAdmin())
                        <div style="margin-bottom:20px;">
                            <label class="form-label">Propriétaire</label>
                            <select name="proprietaire_id" class="input">
                                @foreach($proprietaires as $proprio)
                                    <option value="{{ $proprio->id }}"
                                            {{ $bien->proprietaire_id == $proprio->id ? 'selected' : '' }}>
                                        {{ $proprio->name }} — {{ $proprio->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Type + Statut --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Type de bien <span style="color:#ef4444;">*</span></label>
                            <select name="type" class="input">
                                @foreach(['Appartement', 'Villa', 'Studio', 'Bureau', 'Commerce', 'Terrain', 'Maison'] as $type)
                                    <option value="{{ $type }}" {{ old('type', $bien->type) === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Statut <span style="color:#ef4444;">*</span></label>
                            <select name="statut" class="input">
                                <option value="disponible" {{ old('statut', $bien->statut) === 'disponible' ? 'selected' : '' }}>Disponible</option>
                                <option value="loue"       {{ old('statut', $bien->statut) === 'loue'       ? 'selected' : '' }}>Loué</option>
                                <option value="en_travaux" {{ old('statut', $bien->statut) === 'en_travaux' ? 'selected' : '' }}>En travaux</option>
                            </select>
                        </div>
                    </div>

                    {{-- Adresse --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Adresse <span style="color:#ef4444;">*</span></label>
                        <input type="text" name="adresse"
                               value="{{ old('adresse', $bien->adresse) }}"
                               class="input">
                        @error('adresse')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Ville --}}
                    <div style="margin-bottom:20px;">
                        <label class="form-label">Ville <span style="color:#ef4444;">*</span></label>
                        <select name="ville" class="input">
                            @foreach(['Dakar', 'Thiès', 'Saint-Louis', 'Ziguinchor', 'Kaolack', 'Mbour', 'Rufisque', 'Touba'] as $ville)
                                <option value="{{ $ville }}" {{ old('ville', $bien->ville) === $ville ? 'selected' : '' }}>
                                    {{ $ville }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Surface + Pièces --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Surface (m²)</label>
                            <input type="number" name="surface_m2"
                                   value="{{ old('surface_m2', $bien->surface_m2) }}"
                                   min="1" class="input">
                        </div>
                        <div>
                            <label class="form-label">Nombre de pièces</label>
                            <input type="number" name="nombre_pieces"
                                   value="{{ old('nombre_pieces', $bien->nombre_pieces) }}"
                                   min="1" class="input">
                        </div>
                    </div>

                    {{-- Loyer + Commission --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Loyer mensuel (FCFA) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="loyer_mensuel"
                                   value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}"
                                   min="1" class="input">
                            @error('loyer_mensuel')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Taux commission (%) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="taux_commission"
                                   value="{{ old('taux_commission', $bien->taux_commission) }}"
                                   min="1" max="20" step="0.5" class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:4px;">Entre 1% et 20%</div>
                            @error('taux_commission')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div style="margin-bottom:24px;">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3"
                                  class="input" style="resize:vertical;">{{ old('description', $bien->description) }}</textarea>
                    </div>

                    {{-- Boutons --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="{{ route('biens.show', $bien) }}" class="btn btn-secondary">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Enregistrer les modifications
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

</x-app-layout>