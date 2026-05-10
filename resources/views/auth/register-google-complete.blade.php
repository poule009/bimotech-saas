<x-guest-layout>
    <div class="auth-card">

        <div style="text-align:center;margin-bottom:20px;">
            <div style="display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;background:#f0fdf4;border-radius:50%;margin-bottom:12px;">
                <svg width="22" height="22" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
            </div>
            <div class="auth-card-title" style="margin-bottom:4px;">Une dernière étape</div>
            <div class="auth-card-sub">
                Connecté en tant que <strong>{{ $googleName }}</strong><br>
                Donnez un nom à votre agence pour finir.
            </div>
        </div>

        @if ($errors->any())
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                @foreach ($errors->all() as $error)
                    <div style="font-size:13px;color:#dc2626;margin-bottom:2px;">{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('agency.register.google.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nom de l'agence <span style="color:#ef4444;">*</span></label>
                <input type="text" name="agency_name" value="{{ old('agency_name') }}"
                       placeholder="Ex : Immobilier Prestige Dakar"
                       class="form-input" required autofocus autocomplete="organization">
                @error('agency_name')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="cgu" value="1"
                           style="width:16px;height:16px;margin-top:2px;accent-color:#1a3c5e;cursor:pointer;flex-shrink:0;">
                    <span style="font-size:13px;color:#64748b;line-height:1.5;">
                        J'accepte les
                        <a href="#" style="color:#1a3c5e;font-weight:600;">conditions générales d'utilisation</a>
                    </span>
                </label>
                @error('cgu')
                    <div class="form-error" style="margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-auth">
                Créer mon agence gratuitement
            </button>

        </form>

    </div>

    <div style="text-align:center;margin-top:16px;">
        <a href="{{ route('agency.register') }}" class="auth-link" style="font-size:13px;color:#94a3b8;">
            ← Revenir à l'inscription
        </a>
    </div>

</x-guest-layout>
