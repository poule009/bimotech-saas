<form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

<form method="post" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf @method('patch')

    <div class="space-y-1.5">
        <label class="block font-body font-medium text-sm text-bimo-navy" for="name">Nom complet</label>
        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                      focus:outline-none focus:ring-2 transition-all duration-150
                      @error('name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
        @error('name')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="space-y-1.5">
        <label class="block font-body font-medium text-sm text-bimo-navy" for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                      focus:outline-none focus:ring-2 transition-all duration-150
                      @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
        @error('email')<p class="font-body text-xs text-bimo-red mt-1">{{ $message }}</p>@enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <div class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[10px] px-4 py-3 mt-2">
            <p class="font-body text-sm text-bimo-gold flex-1">Votre adresse email n'est pas vérifiée.</p>
            <button form="send-verification" class="font-body font-semibold text-xs text-bimo-gold underline hover:text-bimo-navy transition-colors duration-150">
                Renvoyer l'email
            </button>
        </div>
        @if (session('status') === 'verification-link-sent')
        <p class="font-body text-xs text-bimo-gold mt-1">✓ Un nouveau lien de vérification a été envoyé.</p>
        @endif
        @endif
    </div>

    <div class="flex items-center gap-3 pt-1">
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Enregistrer
        </button>
        @if (session('status') === 'profile-updated')
        <p class="font-body text-sm text-bimo-gold">✓ Profil mis à jour.</p>
        @endif
    </div>
</form>
