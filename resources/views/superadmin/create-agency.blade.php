@extends('layouts.app')
@section('title', 'Nouvelle agence')
@section('breadcrumb', 'Nouvelle agence')

@section('content')
<div style="padding:0 0 48px;max-width:720px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="{{ route('superadmin.dashboard') }}" style="color:#6b7280;text-decoration:none">Agences</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:600">Nouvelle agence</span>
    </div>

    {{-- Erreurs --}}
    @if($errors->any())
    <div class="flash-error" style="margin-bottom:20px">
        <strong>Veuillez corriger les erreurs suivantes :</strong>
        <ul style="margin-top:6px;padding-left:16px">
            @foreach($errors->all() as $error)
            <li style="font-size:12px">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('superadmin.agencies.store') }}">
        @csrf

        {{-- Informations de l'agence --}}
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
                        <label class="form-label" for="agency_name">Nom de l'agence <span class="req">*</span></label>
                        <input type="text" id="agency_name" name="agency_name"
                               class="form-input {{ $errors->has('agency_name') ? 'error' : '' }}"
                               value="{{ old('agency_name') }}"
                               placeholder="Ex : Agence Immobilière Dakar" required>
                        @error('agency_name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="agency_email">Email de l'agence <span class="req">*</span></label>
                        <input type="email" id="agency_email" name="agency_email"
                               class="form-input {{ $errors->has('agency_email') ? 'error' : '' }}"
                               value="{{ old('agency_email') }}"
                               placeholder="contact@agence.sn" required>
                        @error('agency_email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="agency_telephone">Téléphone <span class="opt">(optionnel)</span></label>
                        <input type="text" id="agency_telephone" name="agency_telephone"
                               class="form-input {{ $errors->has('agency_telephone') ? 'error' : '' }}"
                               value="{{ old('agency_telephone') }}"
                               placeholder="+221 77 000 00 00">
                        @error('agency_telephone')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="agency_adresse">Adresse <span class="opt">(optionnel)</span></label>
                        <input type="text" id="agency_adresse" name="agency_adresse"
                               class="form-input {{ $errors->has('agency_adresse') ? 'error' : '' }}"
                               value="{{ old('agency_adresse') }}"
                               placeholder="Rue 10, Dakar">
                        @error('agency_adresse')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div style="margin-top:12px;padding:10px 14px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;font-size:12px;color:#1d4ed8">
                    Un abonnement <strong>essai gratuit de 30 jours</strong> sera automatiquement créé.
                </div>
            </div>
        </div>

        {{-- Compte administrateur --}}
        <div class="card" style="margin-bottom:20px">
            <div class="card-hd">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="card-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="card-title">Compte administrateur de l'agence</div>
                </div>
            </div>
            <div class="card-body">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="admin_name">Nom complet <span class="req">*</span></label>
                        <input type="text" id="admin_name" name="admin_name"
                               class="form-input {{ $errors->has('admin_name') ? 'error' : '' }}"
                               value="{{ old('admin_name') }}"
                               placeholder="Prénom Nom" required>
                        @error('admin_name')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="admin_email">Email de connexion <span class="req">*</span></label>
                        <input type="email" id="admin_email" name="admin_email"
                               class="form-input {{ $errors->has('admin_email') ? 'error' : '' }}"
                               value="{{ old('admin_email') }}"
                               placeholder="admin@agence.sn" required>
                        @error('admin_email')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="admin_password">Mot de passe <span class="req">*</span></label>
                        <input type="password" id="admin_password" name="admin_password"
                               class="form-input {{ $errors->has('admin_password') ? 'error' : '' }}"
                               placeholder="Minimum 8 caractères" required>
                        <div style="font-size:11px;color:#9ca3af;margin-top:3px">L'admin recevra ce mot de passe par email.</div>
                        @error('admin_password')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="admin_password_confirmation">Confirmer le mot de passe <span class="req">*</span></label>
                        <input type="password" id="admin_password_confirmation" name="admin_password_confirmation"
                               class="form-input"
                               placeholder="Répétez le mot de passe" required>
                    </div>
                </div>
                <div style="margin-top:12px;padding:10px 14px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;font-size:12px;color:#16a34a">
                    Un email de bienvenue avec les identifiants sera envoyé automatiquement.
                    <strong>Vous resterez connecté en tant que Super Admin.</strong>
                </div>
            </div>
        </div>

        {{-- Boutons --}}
        <div class="submit-bar" style="background:transparent;border:none;padding:0;justify-content:flex-start;gap:12px">
            <button type="submit" class="btn-submit">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Créer l'agence
            </button>
            <a href="{{ route('superadmin.dashboard') }}" class="btn-cancel">Annuler</a>
        </div>

    </form>

</div>
@endsection
