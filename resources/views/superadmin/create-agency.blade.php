@extends('layouts.app')
@section('title', 'Nouvelle agence')
@section('breadcrumb', 'Nouvelle agence')

@section('content')
@push('styles')
<style>
        body { font-family: 'Inter', -apple-system, sans-serif; background: #f1f5f9; min-height: 100vh; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input {
            width: 100%; padding: 10px 14px; border: 1.5px solid #d1d5db;
            border-radius: 8px; font-size: 14px; color: #111827;
            background: #fff; transition: border-color .15s;
            outline: none;
        }
        .form-input:focus { border-color: #1d4ed8; box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
        .form-input.is-invalid { border-color: #ef4444; }
        .invalid-feedback { font-size: 12px; color: #ef4444; margin-top: 4px; }
        .section-title {
            font-size: 13px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .05em; color: #6b7280; margin-bottom: 16px;
            padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;
        }
        .btn-primary {
            background: #1d4ed8; color: #fff; padding: 11px 28px;
            border-radius: 8px; font-size: 14px; font-weight: 600;
            border: none; cursor: pointer; transition: background .15s;
        }
        .btn-primary:hover { background: #1e40af; }
        .btn-secondary {
            background: #f3f4f6; color: #374151; padding: 11px 20px;
            border-radius: 8px; font-size: 14px; font-weight: 500;
            border: 1px solid #d1d5db; cursor: pointer; text-decoration: none;
            display: inline-block; transition: background .15s;
        }
        .btn-secondary:hover { background: #e5e7eb; }
        .alert-error {
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
            border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px;
        }
        .password-hint {
            font-size: 11px; color: #9ca3af; margin-top: 4px;
        }
    </style>
@endpush
<div style="padding:0 0 48px;max-width:720px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:20px">
        <a href="{{ route('superadmin.dashboard') }}" style="color:#6b7280;text-decoration:none">Agences</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:600">Nouvelle agence</span>
    </div>

        {{-- Erreurs de validation --}}
        @if ($errors->any())
            <div class="alert-error">
                <p class="font-semibold mb-1">⚠️ Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('superadmin.agencies.store') }}">
            @csrf

            {{-- ── Section 1 : Informations de l'agence ── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <p class="section-title">🏢 Informations de l'agence</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                    <div class="form-group">
                        <label class="form-label" for="agency_name">
                            Nom de l'agence <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="agency_name"
                            name="agency_name"
                            class="form-input {{ $errors->has('agency_name') ? 'is-invalid' : '' }}"
                            value="{{ old('agency_name') }}"
                            placeholder="Ex : Agence Immobilière Dakar"
                            required
                        >
                        @error('agency_name')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="agency_email">
                            Email de l'agence <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="agency_email"
                            name="agency_email"
                            class="form-input {{ $errors->has('agency_email') ? 'is-invalid' : '' }}"
                            value="{{ old('agency_email') }}"
                            placeholder="contact@agence.sn"
                            required
                        >
                        @error('agency_email')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="agency_telephone">Téléphone</label>
                        <input
                            type="text"
                            id="agency_telephone"
                            name="agency_telephone"
                            class="form-input {{ $errors->has('agency_telephone') ? 'is-invalid' : '' }}"
                            value="{{ old('agency_telephone') }}"
                            placeholder="+221 77 000 00 00"
                        >
                        @error('agency_telephone')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="agency_adresse">Adresse</label>
                        <input
                            type="text"
                            id="agency_adresse"
                            name="agency_adresse"
                            class="form-input {{ $errors->has('agency_adresse') ? 'is-invalid' : '' }}"
                            value="{{ old('agency_adresse') }}"
                            placeholder="Rue 10, Dakar"
                        >
                        @error('agency_adresse')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Info : essai 30 jours automatique --}}
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
                    ℹ️ Un abonnement <strong>essai gratuit de 30 jours</strong> sera automatiquement créé pour cette agence.
                </div>
            </div>

            {{-- ── Section 2 : Compte administrateur ── --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <p class="section-title">👤 Compte administrateur de l'agence</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                    <div class="form-group">
                        <label class="form-label" for="admin_name">
                            Nom complet <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="admin_name"
                            name="admin_name"
                            class="form-input {{ $errors->has('admin_name') ? 'is-invalid' : '' }}"
                            value="{{ old('admin_name') }}"
                            placeholder="Prénom Nom"
                            required
                        >
                        @error('admin_name')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_email">
                            Email de connexion <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="email"
                            id="admin_email"
                            name="admin_email"
                            class="form-input {{ $errors->has('admin_email') ? 'is-invalid' : '' }}"
                            value="{{ old('admin_email') }}"
                            placeholder="admin@agence.sn"
                            required
                        >
                        @error('admin_email')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_password">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="admin_password"
                            name="admin_password"
                            class="form-input {{ $errors->has('admin_password') ? 'is-invalid' : '' }}"
                            placeholder="Minimum 8 caractères"
                            required
                        >
                        <p class="password-hint">Minimum 8 caractères. L'admin recevra ce mot de passe par email.</p>
                        @error('admin_password')
                            <p class="invalid-feedback">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="admin_password_confirmation">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="password"
                            id="admin_password_confirmation"
                            name="admin_password_confirmation"
                            class="form-input"
                            placeholder="Répétez le mot de passe"
                            required
                        >
                    </div>

                </div>

                {{-- Info : email envoyé automatiquement --}}
                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                    ✉️ Un email de bienvenue avec les identifiants sera automatiquement envoyé à l'administrateur.
                    <br><strong>Vous resterez connecté en tant que Super Admin.</strong>
                </div>
            </div>

            {{-- ── Boutons ── --}}
            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">
                    ✅ Créer l'agence
                </button>
                <a href="{{ route('superadmin.dashboard') }}" class="btn-secondary">
                    Annuler
                </a>
            </div>

        </form>

</div>
@endsection
