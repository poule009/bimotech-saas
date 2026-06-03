@extends('layouts.app')
@section('header', 'Inviter un collaborateur')

@section('content')
<div class="max-w-lg mx-auto">

    <div class="mb-5">
        <h1 class="font-display font-extrabold text-xl text-bimo-navy tracking-tight">Inviter un collaborateur</h1>
        <p class="font-body text-sm text-bimo-navy/50 mt-1">
            Le collaborateur aura accès à tous les biens, contrats et paiements de votre agence.
        </p>
    </div>

    <form method="POST" action="{{ route('admin.equipe.store') }}" class="space-y-5">
        @csrf

        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5 space-y-4">

            <div class="space-y-1.5">
                <label for="name" class="block font-body font-medium text-sm text-bimo-navy">
                    Nom complet <span class="text-bimo-red">*</span>
                </label>
                <input id="name" name="name" type="text"
                       value="{{ old('name') }}"
                       placeholder="Prénom et Nom"
                       autocomplete="off"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                              placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150
                              @error('name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                              @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('name')
                <p class="font-body text-xs text-bimo-red">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block font-body font-medium text-sm text-bimo-navy">
                    Email <span class="text-bimo-red">*</span>
                </label>
                <input id="email" name="email" type="email"
                       value="{{ old('email') }}"
                       placeholder="collaborateur@agence.sn"
                       autocomplete="off"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                              placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150
                              @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                              @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('email')
                <p class="font-body text-xs text-bimo-red">{{ $message }}</p>
                @enderror
            </div>

            <div class="space-y-1.5">
                <label for="telephone" class="block font-body font-medium text-sm text-bimo-navy">
                    Téléphone <span class="font-light text-bimo-navy/40 ml-1">(optionnel)</span>
                </label>
                <input id="telephone" name="telephone" type="tel"
                       value="{{ old('telephone') }}"
                       placeholder="+221 77 000 00 00"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                              placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                              transition-all duration-150">
            </div>

            <div class="grid grid-cols-2 gap-3" x-data="{ show: false }">
                <div class="space-y-1.5">
                    <label for="password" class="block font-body font-medium text-sm text-bimo-navy">
                        Mot de passe <span class="text-bimo-red">*</span>
                    </label>
                    <div class="relative">
                        <input id="password" name="password"
                               :type="show ? 'text' : 'password'"
                               placeholder="Min. 8 caractères"
                               autocomplete="new-password"
                               class="w-full px-4 pr-10 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy
                                      placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-bimo-navy/30 hover:text-bimo-navy/60 transition-colors duration-150">
                            <svg x-show="!show" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg x-show="show" style="display:none" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                    <p class="font-body text-xs text-bimo-red">{{ $message }}</p>
                    @enderror
                </div>
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="block font-body font-medium text-sm text-bimo-navy">
                        Confirmer <span class="text-bimo-red">*</span>
                    </label>
                    <input id="password_confirmation" name="password_confirmation"
                           :type="show ? 'text' : 'password'"
                           placeholder="Répétez"
                           autocomplete="new-password"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-navy
                                  placeholder:text-bimo-navy/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15
                                  transition-all duration-150">
                </div>
            </div>

            <div class="bg-bimo-bg2 rounded-[10px] px-4 py-3">
                <p class="font-body text-xs text-bimo-navy/60 leading-relaxed">
                    <svg class="w-3.5 h-3.5 inline mr-1 text-bimo-navy/40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Ce collaborateur aura accès à tous les biens, contrats et paiements de votre agence.
                    Il ne pourra pas modifier les paramètres de l'agence ni gérer l'équipe.
                </p>
            </div>
        </div>

        <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-bimo-navy/10 py-3 flex justify-end gap-3">
            <a href="{{ route('admin.equipe.index') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15
                      font-body text-sm text-bimo-navy/60 hover:text-bimo-navy hover:border-bimo-navy/30
                      transition-all duration-150">
                Annuler
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px] bg-[var(--ac)] text-white
                           font-display font-bold text-sm hover:opacity-90 transition-opacity duration-150">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                Inviter
            </button>
        </div>
    </form>

</div>
@endsection
