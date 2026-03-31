<x-app-layout>
    <x-slot name="header">Modifier le contrat</x-slot>

    {{-- Header navigation --}}
    <div style="display:flex;align-items:center;gap:12px;" class="section-gap">
        <a href="{{ route('admin.contrats.show', $contrat) }}"
           style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
           onmouseenter="this.style.background='var(--bg)'"
           onmouseleave="this.style.background='transparent'">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">
                Modifier le contrat — {{ $contrat->bien->reference }}
            </h1>
            <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                Locataire : {{ $contrat->locataire->name }} · Statut :
                <span style="color:{{ $contrat->statut === 'actif' ? '#16a34a' : '#dc2626' }};font-weight:600;">
                    {{ ucfirst($contrat->statut) }}
                </span>
            </p>
        </div>
    </div>

    <div style="max-width:720px;">
        <div class="card">
            <div class="card-body">

                {{-- Erreurs --}}
                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom:20px;">
                        @foreach($errors->all() as $error)
                            <div>❌ {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                {{-- Info bien (lecture seule) --}}
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:var(--radius-sm);padding:14px;margin-bottom:24px;">
                    <div style="font-size:11px;font-weight:700;color:#15803d;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">
                        📋 Bien concerné (non modifiable)
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
                        <div>
                            <div style="font-size:10px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">Référence</div>
                            <div style="font-weight:700;color:var(--text);">{{ $contrat->bien->reference }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">Type</div>
                            <div style="font-weight:600;color:var(--text);">{{ $contrat->bien->type }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px;color:#6b7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:2px;">Adresse</div>
                            <div style="font-weight:600;color:var(--text);">{{ $contrat->bien->adresse }}</div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.contrats.update', $contrat) }}">
                    @csrf
                    @method('PUT')

                    {{-- Section : Conditions financières --}}
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        💰 Conditions financières
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Loyer contractuel (FCFA) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="loyer_contractuel"
                                   value="{{ old('loyer_contractuel', $contrat->loyer_contractuel) }}"
                                   min="1" step="500" class="input">
                            @error('loyer_contractuel')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Caution (FCFA) <span style="color:#ef4444;">*</span></label>
                            <input type="number" name="caution"
                                   value="{{ old('caution', $contrat->caution) }}"
                                   min="0" step="500" class="input">
                            @error('caution')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Frais d'agence (FCFA)</label>
                            <input type="number" name="frais_agence"
                                   value="{{ old('frais_agence', $contrat->frais_agence ?? 0) }}"
                                   min="0" step="500" class="input">
                            @error('frais_agence')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Charges mensuelles (FCFA)</label>
                            <input type="number" name="charges_mensuelles"
                                   value="{{ old('charges_mensuelles', $contrat->charges_mensuelles ?? 0) }}"
                                   min="0" step="500" class="input">
                            @error('charges_mensuelles')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
                        <div>
                            <label class="form-label">Indexation annuelle (%)</label>
                            <input type="number" name="indexation_annuelle"
                                   value="{{ old('indexation_annuelle', $contrat->indexation_annuelle ?? 0) }}"
                                   min="0" max="20" step="0.5" class="input">
                            <div style="font-size:11px;color:var(--text-3);margin-top:3px;">Révision annuelle du loyer (0 = pas de révision)</div>
                            @error('indexation_annuelle')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Nombre de mois de caution</label>
                            <input type="number" name="nombre_mois_caution"
                                   value="{{ old('nombre_mois_caution', $contrat->nombre_mois_caution ?? 1) }}"
                                   min="1" max="6" class="input">
                            @error('nombre_mois_caution')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Section : Type de bail & Dates --}}
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        📄 Type de bail & Durée
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Type de bail <span style="color:#ef4444;">*</span></label>
                            <select name="type_bail" class="input">
                                @foreach($typesBail as $key => $label)
                                    <option value="{{ $key }}"
                                        {{ old('type_bail', $contrat->type_bail ?? 'habitation') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type_bail')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">
                                Date de fin
                                <span style="font-size:11px;font-weight:400;color:var(--text-3);">(vide = indéterminée)</span>
                            </label>
                            <input type="date" name="date_fin"
                                   value="{{ old('date_fin', $contrat->date_fin?->format('Y-m-d')) }}"
                                   class="input">
                            @error('date_fin')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Section : Garant --}}
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        🛡️ Garant (optionnel)
                    </div>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
                        <div>
                            <label class="form-label">Nom du garant</label>
                            <input type="text" name="garant_nom"
                                   value="{{ old('garant_nom', $contrat->garant_nom) }}"
                                   placeholder="Ex : Mamadou Diop"
                                   class="input">
                            @error('garant_nom')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="form-label">Téléphone du garant</label>
                            <input type="tel" name="garant_telephone"
                                   value="{{ old('garant_telephone', $contrat->garant_telephone) }}"
                                   placeholder="+221 77 000 00 00"
                                   class="input">
                            @error('garant_telephone')
                                <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom:24px;">
                        <label class="form-label">Adresse du garant</label>
                        <input type="text" name="garant_adresse"
                               value="{{ old('garant_adresse', $contrat->garant_adresse) }}"
                               placeholder="Ex : Sacré-Cœur 3, Dakar"
                               class="input">
                        @error('garant_adresse')
                            <div style="font-size:12px;color:#dc2626;margin-top:4px;">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Observations --}}
                    <div style="font-size:12px;font-weight:700;color:var(--text-2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px;padding-bottom:8px;border-bottom:1px solid var(--border);">
                        📝 Observations
                    </div>

                    <div style="margin-bottom:24px;">
                        <textarea name="observations" rows="3"
                                  placeholder="Conditions particulières, préavis, état des lieux..."
                                  class="input" style="resize:vertical;">{{ old('observations', $contrat->observations) }}</textarea>
                    </div>

                    {{-- Boutons --}}
                    <div style="display:flex;justify-content:flex-end;gap:10px;padding-top:20px;border-top:1px solid var(--border);">
                        <a href="{{ route('admin.contrats.show', $contrat) }}" class="btn btn-secondary">
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
