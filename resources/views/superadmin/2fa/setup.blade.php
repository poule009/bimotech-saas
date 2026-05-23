@extends('layouts.app')
@section('title', 'Configurer le 2FA')

@section('content')
<style>
.tfa-wrap { max-width:520px;margin:0 auto;padding:8px 0; }
.tfa-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.tfa-card-hd { padding:18px 22px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:12px; }
.tfa-card-icon { width:36px;height:36px;border-radius:10px;background:#ede9fe;color:#7c3aed;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.tfa-card-icon svg { width:18px;height:18px; }
.tfa-card-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.tfa-card-body { padding:22px; }
.tfa-step { display:flex;align-items:flex-start;gap:12px;margin-bottom:18px; }
.tfa-step-num { width:22px;height:22px;border-radius:50%;background:#6366f1;color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px; }
.tfa-step-text { font-size:13px;color:#374151;line-height:1.5; }
.tfa-qr { display:flex;justify-content:center;margin:20px 0;padding:16px;background:#f9fafb;border-radius:10px;border:1px solid #e5e7eb; }
.tfa-qr svg { width:180px;height:180px; }
.tfa-secret { font-family:'Courier New',monospace;font-size:15px;font-weight:700;letter-spacing:3px;color:#374151;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 14px;text-align:center;word-break:break-all; }
.tfa-input-wrap { margin-top:22px; }
.tfa-input { width:100%;padding:12px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:22px;font-weight:700;font-family:'Courier New',monospace;text-align:center;letter-spacing:6px;color:#0d1117;outline:none;transition:border .15s; }
.tfa-input:focus { border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.12); }
.tfa-input.error { border-color:#dc2626; }
.tfa-actions { display:flex;gap:10px;margin-top:16px; }
.btn-purple { background:#6366f1;color:#fff;font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:600;padding:10px 22px;border-radius:8px;border:none;cursor:pointer;flex:1;display:flex;align-items:center;justify-content:center;gap:6px;transition:opacity .15s; }
.btn-purple:hover { opacity:.88; }
</style>

<div class="tfa-wrap">

    @if ($errors->any())
    <div class="flash-error" style="margin-bottom:16px">
        <span>{{ $errors->first() }}</span>
        <button class="flash-close" onclick="this.closest('.flash-error').remove()">✕</button>
    </div>
    @endif

    <div class="tfa-card">
        <div class="tfa-card-hd">
            <div class="tfa-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 018 0v4"/></svg>
            </div>
            <div>
                <div class="tfa-card-title">Configurer l'authentification à deux facteurs</div>
                <div style="font-size:12px;color:#6b7280;margin-top:2px">Protégez votre compte superadmin avec Google Authenticator ou Authy</div>
            </div>
        </div>
        <div class="tfa-card-body">

            <div class="tfa-step">
                <div class="tfa-step-num">1</div>
                <div class="tfa-step-text">Installez <strong>Google Authenticator</strong>, <strong>Authy</strong> ou toute app TOTP compatible sur votre téléphone.</div>
            </div>

            <div class="tfa-step">
                <div class="tfa-step-num">2</div>
                <div class="tfa-step-text">Scannez ce QR code avec l'application, ou saisissez la clé manuellement.</div>
            </div>

            <div class="tfa-qr">
                {!! $qrCode !!}
            </div>

            <div style="margin-bottom:6px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;text-align:center">Clé de configuration manuelle</div>
            <div class="tfa-secret">{{ $secret }}</div>

            <div class="tfa-step" style="margin-top:20px;margin-bottom:0">
                <div class="tfa-step-num">3</div>
                <div class="tfa-step-text">Saisissez le code à 6 chiffres affiché dans l'application pour confirmer la configuration.</div>
            </div>

            <form method="POST" action="{{ route('superadmin.2fa.confirm') }}" class="tfa-input-wrap">
                @csrf
                <input
                    type="text"
                    name="code"
                    class="tfa-input {{ $errors->has('code') ? 'error' : '' }}"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    maxlength="6"
                    placeholder="000000"
                    autofocus
                >
                @error('code')
                <div class="form-error" style="text-align:center;margin-top:6px">{{ $message }}</div>
                @enderror
                <div class="tfa-actions">
                    <button type="submit" class="btn-purple">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        Confirmer et activer le 2FA
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection
