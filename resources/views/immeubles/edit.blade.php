@extends('layouts.app')
@section('title', 'Modifier — ' . $immeuble->nom)
@section('breadcrumb', 'Immeubles › ' . $immeuble->nom . ' › Modifier')

@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 320px; gap:24px; align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.recap-card { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:24px; }
.recap-hd { padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07); }
.recap-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff; }
.recap-body { padding:16px 18px; }
.rp-row { display:flex;justify-content:space-between;align-items:center;padding:7px 0;border-bottom:1px solid rgba(255,255,255,.06); }
.rp-row:last-child { border-bottom:none; }
.rp-lbl { font-size:12px;color:rgba(255,255,255,.45); }
.rp-val { font-family:'Syne',sans-serif;font-size:12px;font-weight:600;color:#fff; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:18px">
        <a href="{{ route('admin.immeubles.index') }}" style="color:#6b7280;text-decoration:none">Immeubles</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('admin.immeubles.show', $immeuble) }}" style="color:#6b7280;text-decoration:none">{{ $immeuble->nom }}</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="margin-bottom:20px">
        <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Modifier l'immeuble</h1>
        <p style="font-size:13px;color:#6b7280;margin-top:3px">{{ $immeuble->nom }}</p>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <strong>Veuillez corriger les erreurs :</strong>
        <ul style="margin-top:4px;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.immeubles.update', $immeuble) }}">
        @csrf @method('PATCH')
        <div class="form-grid">

            {{-- ═══ COLONNE GAUCHE ═══ --}}
            <div>

                {{-- PROPRIÉTAIRE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="card-title">Propriétaire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Propriétaire <span class="req">*</span></label>
                            <select name="proprietaire_id" class="form-select {{ $errors->has('proprietaire_id') ? 'error':'' }}">
                                <option value="">— Sélectionner —</option>
                                @foreach($proprietaires as $p)
                                    <option value="{{ $p->id }}" {{ old('proprietaire_id', $immeuble->proprietaire_id) == $p->id ? 'selected':'' }}>
                                        {{ $p->name }} — {{ $p->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proprietaire_id')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- INFORMATIONS --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="18" rx="2"/><line x1="2" y1="9" x2="22" y2="9"/><line x1="12" y1="3" x2="12" y2="21"/></svg>
                        </div>
                        <div class="card-title">Informations générales</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Nom de l'immeuble <span class="req">*</span></label>
                            <input type="text" name="nom"
                                   class="form-input {{ $errors->has('nom') ? 'error':'' }}"
                                   value="{{ old('nom', $immeuble->nom) }}">
                            @error('nom')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Nombre de niveaux <span class="opt">(optionnel)</span></label>
                            <input type="number" name="nombre_niveaux"
                                   class="form-input"
                                   value="{{ old('nombre_niveaux', $immeuble->nombre_niveaux) }}"
                                   min="1" max="99">
                        </div>
                    </div>
                </div>

                {{-- LOCALISATION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="card-title">Localisation</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Adresse <span class="req">*</span></label>
                            <input type="text" name="adresse"
                                   class="form-input {{ $errors->has('adresse') ? 'error':'' }}"
                                   value="{{ old('adresse', $immeuble->adresse) }}">
                            @error('adresse')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ville <span class="req">*</span></label>
                            <input type="text" name="ville"
                                   class="form-input {{ $errors->has('ville') ? 'error':'' }}"
                                   value="{{ old('ville', $immeuble->ville) }}">
                            @error('ville')<div class="form-error">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Description <span class="opt">(optionnel)</span></div>
                    </div>
                    <div class="card-body">
                        <textarea name="description" class="form-textarea">{{ old('description', $immeuble->description) }}</textarea>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('admin.immeubles.show', $immeuble) }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer
                        </button>
                    </div>
                </div>

            </div>{{-- fin colonne gauche --}}

            {{-- ═══ COLONNE DROITE --}}
            <div>
                <div class="recap-card">
                    <div class="recap-hd">
                        <div class="recap-title">Récapitulatif</div>
                    </div>
                    <div class="recap-body">
                        <div class="rp-row">
                            <span class="rp-lbl">Créé le</span>
                            <span class="rp-val">{{ $immeuble->created_at?->format('d/m/Y') }}</span>
                        </div>
                        <div class="rp-row">
                            <span class="rp-lbl">Unités</span>
                            <span class="rp-val">{{ $immeuble->biens()->count() }}</span>
                        </div>
                        <div class="rp-row">
                            <span class="rp-lbl">Unités sous contrat</span>
                            <span class="rp-val" style="color:#4ade80">
                                {{ $immeuble->biens()->whereHas('contratActif')->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- fin form-grid --}}
    </form>
</div>
@endsection
