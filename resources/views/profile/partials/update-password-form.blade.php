<form method="post" action="{{ route('password.update') }}" class="space-y-4">
    @csrf @method('put')

    <div class="space-y-1.5">
        <label class="block font-body font-medium text-sm text-bimo-navy" for="update_password_current_password">Mot de passe actuel</label>
        <input type="password" id="update_password_current_password" name="current_password" autocomplete="current-password"
               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
        @if($errors->updatePassword->get('current_password'))
        <p class="font-body text-xs text-bimo-red mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
        @endif
    </div>

    <div class="space-y-1.5">
        <label class="block font-body font-medium text-sm text-bimo-navy" for="update_password_password">Nouveau mot de passe</label>
        <input type="password" id="update_password_password" name="password" autocomplete="new-password"
               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
        @if($errors->updatePassword->get('password'))
        <p class="font-body text-xs text-bimo-red mt-1">{{ $errors->updatePassword->first('password') }}</p>
        @endif
    </div>

    <div class="space-y-1.5">
        <label class="block font-body font-medium text-sm text-bimo-navy" for="update_password_password_confirmation">Confirmer le nouveau mot de passe</label>
        <input type="password" id="update_password_password_confirmation" name="password_confirmation" autocomplete="new-password"
               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
        @if($errors->updatePassword->get('password_confirmation'))
        <p class="font-body text-xs text-bimo-red mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
        @endif
    </div>

    <div class="flex items-center gap-3 pt-1">
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            Mettre à jour
        </button>
        @if (session('status') === 'password-updated')
        <p class="font-body text-sm text-bimo-gold">✓ Mot de passe mis à jour.</p>
        @endif
    </div>
</form>
