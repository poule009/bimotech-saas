<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Mot de passe oublié — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg antialiased min-h-screen grid md:grid-cols-2">

{{-- Panneau gauche brand --}}
<div class="hidden md:flex flex-col justify-between bg-bimo-navy p-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.03]"
         style="background-image:linear-gradient(rgba(255,255,255,1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,1) 1px,transparent 1px);background-size:48px 48px;pointer-events:none"></div>
    <a href="{{ url('/') }}" class="relative z-10 flex-shrink-0">
        <x-bee-logo variant="white" size="md" />
    </a>
    <div class="relative z-10">
        <h2 class="font-display font-extrabold text-[clamp(26px,3vw,36px)] text-white tracking-tight leading-tight mb-5">
            Gérez votre agence<br><em class="not-italic text-bimo-gold">comme un pro</em>
        </h2>
        <p class="font-body font-light text-sm text-white/50 leading-relaxed max-w-xs">Biens, contrats, paiements, quittances — tout centralisé. Conforme TVA 18%, NINEA et loi 81-18.</p>
    </div>
    <div class="relative z-10 flex gap-8">
        @foreach([['150+','Biens gérés'],['12','Agences actives'],['98%','Recouvrement']] as [$n,$l])
        <div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold">{{ $n }}</div>
            <div class="font-body text-xs text-white/40 mt-0.5">{{ $l }}</div>
        </div>
        @endforeach
    </div>
</div>

{{-- Panneau droit formulaire --}}
<main class="flex items-center justify-center p-8 bg-white overflow-y-auto">
    <div class="w-full max-w-sm">
        <h1 class="font-display font-extrabold text-2xl text-bimo-text tracking-tight mb-2">Mot de passe oublié</h1>
        <p class="font-body text-sm text-bimo-text/50 mb-8 leading-relaxed">Entrez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>

        @if($errors->any())
        <div class="border-l-[3px] border-bimo-red bg-bimo-red/[5%] border border-bimo-red/20 rounded-[8px] px-4 py-2.5 mb-5">
            @foreach($errors->all() as $error)<p class="font-body text-xs text-bimo-red leading-relaxed">{{ $error }}</p>@endforeach
        </div>
        @endif

        @if(session('status'))
        <div class="border-l-[3px] border-[#3B6D11] bg-[rgba(59,109,17,.06)] border border-[rgba(59,109,17,.2)] rounded-[8px] px-4 py-3 mb-5 font-body text-sm text-[#15803d]">
            ✓ {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" id="forgot-form" novalidate class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="votre@agence.sn" autocomplete="email" autofocus
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>
            <button type="submit" id="submit-btn"
                    class="w-full inline-flex items-center justify-center gap-2 py-3.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 cursor-pointer">
                <span id="btn-text">Envoyer le lien de réinitialisation</span>
                <svg id="btn-spinner" class="w-4 h-4 hidden animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </button>
        </form>

        <a href="{{ route('login') }}"
           class="flex items-center justify-center gap-1.5 font-body text-sm text-bimo-text/40 hover:text-bimo-text transition-colors duration-150 mt-5">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 5l-7 7 7 7"/></svg>
            Retour à la connexion
        </a>
    </div>
</main>

<script>
document.getElementById('forgot-form').addEventListener('submit', function() {
    var btn = document.getElementById('submit-btn');
    var txt = document.getElementById('btn-text');
    var spin = document.getElementById('btn-spinner');
    btn.disabled = true;
    btn.style.opacity = '.7';
    txt.textContent = 'Envoi en cours...';
    spin.classList.remove('hidden');
});
</script>
</body>
</html>
