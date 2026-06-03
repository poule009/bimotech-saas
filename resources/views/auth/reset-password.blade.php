<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Réinitialiser le mot de passe — Renlio</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-bimo-bg antialiased min-h-screen grid md:grid-cols-2">

{{-- Panneau gauche brand --}}
<div class="hidden md:flex flex-col justify-between bg-bimo-navy p-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.03]"
         style="background-image:linear-gradient(rgba(255,255,255,1) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,1) 1px,transparent 1px);background-size:48px 48px;pointer-events:none"></div>
    <a href="{{ url('/') }}" class="relative z-10">
        <img src="/images/logo.jpeg" alt="Renlio" class="h-10 w-auto">
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
        <h1 class="font-display font-extrabold text-2xl text-bimo-navy tracking-tight mb-2">Nouveau mot de passe</h1>
        <p class="font-body text-sm text-bimo-navy/50 mb-8 leading-relaxed">Choisissez un mot de passe sécurisé pour votre compte.</p>

        @if($errors->any())
        <div class="border-l-[3px] border-bimo-red bg-bimo-red/[5%] border border-bimo-red/20 rounded-[8px] px-4 py-2.5 mb-5">
            @foreach($errors->all() as $error)<p class="font-body text-xs text-bimo-red leading-relaxed">{{ $error }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('password.store') }}" id="reset-form" novalidate class="space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-navy" for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}" placeholder="votre@agence.sn" autocomplete="username"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-navy" for="password">Nouveau mot de passe</label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="new-password"
                           oninput="checkStrength(this.value)"
                           class="w-full pr-11 px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-bimo-navy/30 hover:text-bimo-navy/60 transition-colors cursor-pointer" onclick="togglePw('password','eye1')">
                        <svg id="eye1" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div class="h-1 bg-bimo-navy/10 rounded-full overflow-hidden"><div id="strength-fill" class="h-full rounded-full transition-all duration-300" style="width:0"></div></div>
                <div id="strength-label" class="font-body text-xs transition-colors duration-300"></div>
                @error('password')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-navy" for="password_confirmation">Confirmer le mot de passe</label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" autocomplete="new-password"
                           class="w-full pr-11 px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-navy placeholder:text-bimo-navy/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('password_confirmation') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-bimo-navy/30 hover:text-bimo-navy/60 transition-colors cursor-pointer" onclick="togglePw('password_confirmation','eye2')">
                        <svg id="eye2" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_confirmation')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <button type="submit" id="submit-btn"
                    class="w-full inline-flex items-center justify-center gap-2 py-3.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 cursor-pointer mt-2">
                <span id="btn-text">Réinitialiser le mot de passe</span>
                <svg id="btn-spinner" class="w-4 h-4 hidden animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </button>
        </form>
    </div>
</main>

<script>
function togglePw(inputId, iconId) {
    var input = document.getElementById(inputId);
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    document.getElementById(iconId).innerHTML = show
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
}
function checkStrength(val) {
    var fill = document.getElementById('strength-fill');
    var label = document.getElementById('strength-label');
    if (!val) { fill.style.width='0'; label.textContent=''; return; }
    var score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    var levels = [{w:'25%',bg:'#EF4444',txt:'Trop court'},{w:'50%',bg:'#f97316',txt:'Faible'},{w:'75%',bg:'#C9A84C',txt:'Moyen'},{w:'100%',bg:'#1B4F6B',txt:'Fort'}];
    var lvl = levels[Math.max(0, score-1)];
    fill.style.width = lvl.w; fill.style.background = lvl.bg;
    label.textContent = lvl.txt; label.style.color = lvl.bg;
}
document.getElementById('reset-form').addEventListener('submit', function() {
    var btn = document.getElementById('submit-btn');
    btn.disabled = true; btn.style.opacity = '.7';
    document.getElementById('btn-text').textContent = 'Réinitialisation...';
    document.getElementById('btn-spinner').classList.remove('hidden');
});
</script>
</body>
</html>
