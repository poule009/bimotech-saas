<p class="font-body text-sm text-bimo-text/70 mb-4 leading-relaxed">
    Une fois votre compte supprimé, toutes les données seront définitivement perdues.
    Téléchargez vos données avant de procéder.
</p>

<form method="post" action="{{ route('profile.destroy') }}"
      data-confirm="Confirmez votre mot de passe pour supprimer définitivement votre compte. Cette action est irréversible."
      data-confirm-title="Supprimer votre compte ?"
      data-confirm-ok="Supprimer mon compte"
      data-confirm-color="#A60F1C"
      data-confirm-icon-bg="rgba(239,68,68,0.1)">
    @csrf @method('delete')

    {{-- Le mot de passe est demandé via la modale de confirmation globale --}}
    {{-- Pour la suppression de compte, on utilise un champ caché activé par la modale --}}
    <div id="delete-password-wrap" class="mb-4" style="display:none">
        <label class="block font-body font-medium text-sm text-bimo-text mb-1.5">Mot de passe de confirmation</label>
        <input type="password" id="delete_password" name="password" autocomplete="current-password"
               placeholder="Votre mot de passe actuel"
               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-red/30 font-body text-sm text-bimo-text
                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-red focus:ring-2 focus:ring-bimo-red/15 transition-all duration-150">
        @if($errors->userDeletion->get('password'))
        <p class="font-body text-xs text-bimo-red mt-1">{{ $errors->userDeletion->first('password') }}</p>
        @endif
    </div>

    <button type="submit"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-bimo-red/10 border border-bimo-red/20 text-bimo-red
                   font-display font-bold text-sm rounded-[10px] hover:bg-bimo-red/20 transition-all duration-150">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
        Supprimer mon compte
    </button>
</form>

@if($errors->userDeletion->isNotEmpty())
<div class="mt-3 flex items-start gap-2 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3">
    <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div class="font-body text-sm text-bimo-red">{{ $errors->userDeletion->first() }}</div>
</div>
@endif
