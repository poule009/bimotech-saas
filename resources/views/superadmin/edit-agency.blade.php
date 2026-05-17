@extends('layouts.app')
@section('title', 'Modifier — '.$agency->name)
@section('breadcrumb', 'Modifier l\'agence')

@section('content')
<style>
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
</style>
<div style="padding:0 0 48px;max-width:720px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="{{ route('superadmin.dashboard') }}" style="color:#6b7280;text-decoration:none">Agences</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('superadmin.agencies.show', $agency) }}" style="color:#6b7280;text-decoration:none">{{ $agency->name }}</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:600">Modifier</span>
    </div>

    <form method="POST" action="{{ route('superadmin.agencies.update', $agency) }}">
        @csrf @method('PATCH')

        <div class="card" style="margin-bottom:16px">
            <div class="card-hd">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="card-icon gold">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="card-title">Informations de l'agence</div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Nom de l'agence <span class="req">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-input {{ $errors->has('name') ? 'error' : '' }}"
                               value="{{ old('name', $agency->name) }}" required>
                        @error('name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email <span class="req">*</span></label>
                        <input type="email" id="email" name="email"
                               class="form-input {{ $errors->has('email') ? 'error' : '' }}"
                               value="{{ old('email', $agency->email) }}" required>
                        @error('email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="telephone">Téléphone <span class="opt">(optionnel)</span></label>
                        <input type="text" id="telephone" name="telephone"
                               class="form-input {{ $errors->has('telephone') ? 'error' : '' }}"
                               value="{{ old('telephone', $agency->telephone) }}"
                               placeholder="+221 77 000 00 00">
                        @error('telephone')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="adresse">Adresse <span class="opt">(optionnel)</span></label>
                        <input type="text" id="adresse" name="adresse"
                               class="form-input {{ $errors->has('adresse') ? 'error' : '' }}"
                               value="{{ old('adresse', $agency->adresse) }}"
                               placeholder="Rue 10, Dakar">
                        @error('adresse')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="taux_tva">Taux TVA (%) <span class="opt">(optionnel)</span></label>
                        <input type="number" id="taux_tva" name="taux_tva" min="0" max="100" step="0.01"
                               class="form-input {{ $errors->has('taux_tva') ? 'error' : '' }}"
                               value="{{ old('taux_tva', $agency->taux_tva) }}"
                               placeholder="18">
                        @error('taux_tva')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div style="margin-top:14px;padding:10px 14px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;font-size:12px;color:#92400e">
                    Le slug, la couleur, le logo et la signature ne sont modifiables que par l'admin de l'agence depuis ses paramètres.
                </div>
            </div>
        </div>

        <div class="submit-bar" style="background:transparent;border:none;padding:0;justify-content:flex-start;gap:12px">
            <button type="submit" class="btn-submit">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Enregistrer les modifications
            </button>
            <a href="{{ route('superadmin.agencies.show', $agency) }}" class="btn-cancel">Annuler</a>
        </div>

    </form>

</div>
@endsection
