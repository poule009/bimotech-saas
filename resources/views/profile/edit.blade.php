@extends('layouts.app')
@section('header', 'Mon profil')

@section('content')
<div class="max-w-2xl space-y-5">

    {{-- Informations profil --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-navy">Informations du profil</div>
                <div class="font-body text-xs text-bimo-navy/40">Mettez à jour votre nom et votre adresse email.</div>
            </div>
        </div>
        <div class="px-5 py-5">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Mot de passe --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-navy/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-navy">
                    {{ $user->google_id ? 'Définir un mot de passe' : 'Mot de passe' }}
                </div>
                <div class="font-body text-xs text-bimo-navy/40">
                    @if($user->google_id)
                        Vous vous connectez via Google. Définissez un mot de passe pour pouvoir aussi vous connecter par email.
                    @else
                        Utilisez un mot de passe long et aléatoire pour rester sécurisé.
                    @endif
                </div>
            </div>
            @if($user->google_id)
            <div class="ml-auto flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white border border-bimo-navy/10">
                <svg class="w-3.5 h-3.5 flex-shrink-0" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                <span class="font-body text-[10px] text-bimo-navy/50">Google</span>
            </div>
            @endif
        </div>
        <div class="px-5 py-5">
            @if($user->google_id)
                {{-- Formulaire définir mot de passe (Google users) --}}
                <form method="post" action="{{ route('password.set') }}" class="space-y-4">
                    @csrf @method('put')

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy" for="set_password">Nouveau mot de passe</label>
                        <input type="password" id="set_password" name="password" autocomplete="new-password"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        @if($errors->setPassword->get('password'))
                        <p class="font-body text-xs text-bimo-red mt-1">{{ $errors->setPassword->first('password') }}</p>
                        @endif
                    </div>
                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-navy" for="set_password_confirmation">Confirmer le mot de passe</label>
                        <input type="password" id="set_password_confirmation" name="password_confirmation" autocomplete="new-password"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                      focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>
                    <div class="flex items-center gap-3 pt-1">
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            Définir le mot de passe
                        </button>
                        @if(session('status') === 'password-updated')
                        <p class="font-body text-sm text-bimo-gold">✓ Mot de passe défini. Vous pouvez maintenant vous connecter par email.</p>
                        @endif
                    </div>
                </form>
            @else
                @include('profile.partials.update-password-form')
            @endif
        </div>
    </div>

    {{-- Supprimer le compte --}}
    <div class="bg-white rounded-[14px] border border-bimo-red/20 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-red/10 bg-bimo-red/[3%]">
            <div class="w-8 h-8 rounded-[8px] bg-bimo-red/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-red" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-red">Supprimer le compte</div>
                <div class="font-body text-xs text-bimo-red/60">Une fois supprimé, toutes les données seront perdues définitivement.</div>
            </div>
        </div>
        <div class="px-5 py-5">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

</div>
@endsection
