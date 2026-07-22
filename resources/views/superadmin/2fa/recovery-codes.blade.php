@extends('layouts.superadmin')

@section('title', 'Codes de secours')

@section('content')
<div class="max-w-[520px] mx-auto">
    <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Sécurité du compte</div>
    <h1 class="font-display font-medium text-[30px] text-ink mt-1 mb-2">
        {{ ($regenerated ?? false) ? 'Nouveaux codes de secours' : 'Double authentification activée' }}
    </h1>
    <p class="text-[14px] text-muted leading-relaxed mb-6">
        Conservez ces codes de secours dans un endroit sûr. Chacun ne fonctionne
        <strong>qu'une seule fois</strong> et permet de vous connecter si vous perdez l'accès à votre
        application d'authentification. Ils ne seront plus affichés.
    </p>

    <div class="bg-white border border-line rounded-xl p-8">
        <div class="grid grid-cols-2 gap-2.5 mb-6">
            @foreach($codes as $code)
                <code class="text-center text-[15px] font-mono tracking-[0.1em] text-ink bg-paper-dim rounded-lg px-3 py-2.5">{{ $code }}</code>
            @endforeach
        </div>

        <div class="flex items-start gap-2.5 rounded-lg bg-gold/10 border border-gold/30 px-4 py-3 mb-6 text-[13px] text-gold-deep">
            <svg class="w-[17px] h-[17px] shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
            <span>Notez-les maintenant : impossible de les revoir plus tard (seulement en régénérer de nouveaux).</span>
        </div>

        <a href="{{ route('superadmin.dashboard') }}"
           class="block text-center w-full bg-gold text-white font-semibold text-[14px] rounded-lg px-4 py-3 hover:bg-gold-deep transition-colors">
            J'ai enregistré mes codes — continuer
        </a>
    </div>
</div>
@endsection
