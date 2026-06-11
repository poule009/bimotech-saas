<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Créer mon agence — BimoTech Immo</title>
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
    <a href="{{ url('/') }}" class="relative z-10">
        <x-bee-logo variant="white" size="md" />
    </a>
    <div class="relative z-10">
        <h2 class="font-display font-extrabold text-[clamp(22px,2.5vw,32px)] text-white tracking-tight leading-tight mb-5">
            Votre agence en ligne<br>en <em class="not-italic text-bimo-gold">moins de 10 min</em>
        </h2>
        <p class="font-body font-light text-sm text-white/50 leading-relaxed max-w-xs mb-7">Rejoignez les agences sénégalaises qui gèrent leur activité avec BimoTech.</p>
        <div class="flex flex-col gap-3">
            @foreach([['Conformité fiscale incluse','TVA 18%, NINEA, loi 81-18, TOM — automatiquement.'],['Quittances PDF légales','Générées et archivées automatiquement.'],['Gratuit jusqu\'à 5 biens','Aucune carte bancaire requise.']] as [$t,$d])
            <div class="flex items-start gap-3">
                <div class="w-5 h-5 rounded-[5px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-3 h-3 text-bimo-gold" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="2,6 5,9 10,3"/></svg>
                </div>
                <div>
                    <div class="font-body font-semibold text-sm text-white mb-0.5">{{ $t }}</div>
                    <div class="font-body text-xs text-white/40">{{ $d }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="relative z-10 font-body text-sm text-white/40">Déjà un compte ? <a href="{{ route('login') }}" class="text-bimo-gold hover:text-white transition-colors duration-150">Se connecter →</a></div>
</div>

{{-- Panneau droit formulaire --}}
<main class="flex items-center justify-center p-8 bg-white overflow-y-auto">
    <div class="w-full max-w-[420px]">
        <h1 class="font-display font-extrabold text-2xl text-bimo-text tracking-tight mb-2">Créer mon agence</h1>
        <p class="font-body text-sm text-bimo-text/50 mb-6">Déjà inscrit ? <a href="{{ route('login') }}" class="text-bimo-gold hover:text-bimo-text transition-colors duration-150 font-medium">Se connecter</a></p>

        @if($errors->any())
        <div class="border-l-[3px] border-bimo-red bg-bimo-red/[5%] border border-bimo-red/20 rounded-[8px] px-4 py-2.5 mb-5">
            @foreach($errors->all() as $error)<p class="font-body text-xs text-bimo-red leading-relaxed">{{ $error }}</p>@endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('register') }}" id="register-form" novalidate class="space-y-3">
            @csrf

            {{-- Section Agence --}}
            <div class="font-body font-medium text-[11px] uppercase tracking-[1.5px] text-bimo-text/30 pb-2 border-b border-bimo-navy/[8%] mt-5 mb-1">Votre agence</div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="agency_nom">Nom de l'agence <span class="text-bimo-red">*</span></label>
                <input type="text" id="agency_nom" name="agency_nom" value="{{ old('agency_nom') }}" placeholder="Agence Immobilière Diallo"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('agency_nom') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('agency_nom')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="agency_ninea">NINEA</label>
                    <input type="text" id="agency_ninea" name="agency_ninea" value="{{ old('agency_ninea') }}" placeholder="1234567A"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    @error('agency_ninea')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="agency_telephone">Téléphone</label>
                    <input type="tel" id="agency_telephone" name="agency_telephone" value="{{ old('agency_telephone') }}" placeholder="+221 77 000 00 00" autocomplete="tel"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                </div>
            </div>

            {{-- Section Admin --}}
            <div class="font-body font-medium text-[11px] uppercase tracking-[1.5px] text-bimo-text/30 pb-2 border-b border-bimo-navy/[8%] mt-6 mb-1">Votre compte administrateur</div>

            <div class="grid grid-cols-2 gap-3">
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="prenom">Prénom <span class="text-bimo-red">*</span></label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" placeholder="Amadou" autocomplete="given-name"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('prenom') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('prenom')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block font-body font-medium text-sm text-bimo-text" for="nom">Nom <span class="text-bimo-red">*</span></label>
                    <input type="text" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Diallo" autocomplete="family-name"
                           class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('nom') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    @error('nom')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="email">Adresse email <span class="text-bimo-red">*</span></label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="amadou@agence.sn" autocomplete="email"
                       class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                @error('email')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="password">Mot de passe <span class="text-bimo-red">*</span></label>
                <div class="relative">
                    <input type="password" id="password" name="password" placeholder="8 caractères minimum" autocomplete="new-password" oninput="checkStrength(this.value)"
                           class="w-full pr-11 px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150 @error('password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15 @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-bimo-text/30 hover:text-bimo-text/60 transition-colors cursor-pointer" onclick="togglePw('password','eye1')">
                        <svg id="eye1" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                <div id="pw-strength" class="flex gap-1 mt-1.5">
                    <div class="pw-bar flex-1 h-1 rounded-full bg-bimo-navy/10 transition-colors duration-300"></div>
                    <div class="pw-bar flex-1 h-1 rounded-full bg-bimo-navy/10 transition-colors duration-300"></div>
                    <div class="pw-bar flex-1 h-1 rounded-full bg-bimo-navy/10 transition-colors duration-300"></div>
                    <div class="pw-bar flex-1 h-1 rounded-full bg-bimo-navy/10 transition-colors duration-300"></div>
                </div>
                <div id="pw-label" class="font-body text-xs text-bimo-text/30 mt-0.5 transition-colors duration-300"></div>
            </div>

            <div class="space-y-1.5">
                <label class="block font-body font-medium text-sm text-bimo-text" for="password_confirmation">Confirmer le mot de passe <span class="text-bimo-red">*</span></label>
                <div class="relative">
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Répéter le mot de passe" autocomplete="new-password" oninput="checkMatch()"
                           class="w-full pr-11 px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-bimo-text/30 hover:text-bimo-text/60 transition-colors cursor-pointer" onclick="togglePw('password_confirmation','eye2')">
                        <svg id="eye2" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                <div id="match-hint" class="font-body text-xs text-bimo-text/30 transition-colors duration-300"></div>
            </div>

            <div class="flex items-start gap-2.5 my-4">
                <input type="checkbox" id="cgu" name="cgu" value="1" {{ old('cgu') ? 'checked' : '' }}
                       class="w-4 h-4 mt-0.5 rounded cursor-pointer accent-bimo-gold flex-shrink-0">
                <label for="cgu" class="font-body text-sm text-bimo-text/60 leading-relaxed cursor-pointer">
                    J'accepte les <a href="{{ route('mentions-legales') }}" target="_blank" class="text-bimo-gold hover:underline">conditions d'utilisation</a>
                    et la <a href="{{ route('confidentialite') }}" target="_blank" class="text-bimo-gold hover:underline">politique de confidentialité</a>.
                </label>
            </div>
            @error('cgu')<p class="font-body text-xs text-bimo-red -mt-2 mb-2">{{ $message }}</p>@enderror

            <button type="submit" id="submit-btn"
                    class="w-full inline-flex items-center justify-center gap-2 py-3.5 bg-[var(--ac)] text-white font-display font-bold text-sm rounded-[10px] hover:opacity-90 transition-opacity duration-150 cursor-pointer">
                <span id="btn-text">Créer mon agence gratuitement →</span>
                <svg id="btn-spinner" class="w-4 h-4 hidden animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/><path class="opacity-75" fill="white" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
            </button>
        </form>

        <p class="text-center font-body text-sm text-bimo-text/40 mt-4">Déjà un compte ? <a href="{{ route('login') }}" class="text-bimo-gold hover:text-bimo-text font-medium transition-colors duration-150">Se connecter</a></p>
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
function checkStrength(pw) {
    var bars = document.querySelectorAll('#pw-strength .pw-bar');
    var label = document.getElementById('pw-label');
    if (!pw) { bars.forEach(function(b){ b.style.background=''; }); label.textContent=''; return; }
    var score = 0;
    if (pw.length >= 8) score++;
    if (pw.length >= 12) score++;
    if (/[A-Z]/.test(pw) && /[a-z]/.test(pw)) score++;
    if (/[0-9]/.test(pw)) score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;
    score = Math.min(score, 4);
    var colors = ['#C81224','#C81224','#f97316','#C9A84C','#1B4F6B'];
    var labels = ['','Trop faible','Faible','Bon','Excellent'];
    bars.forEach(function(b, i){ b.style.background = i < score ? colors[score] : ''; });
    label.textContent = labels[score];
    label.style.color = score > 0 ? colors[score] : '';
    checkMatch();
}
function checkMatch() {
    var pw1 = document.getElementById('password').value;
    var pw2 = document.getElementById('password_confirmation').value;
    var hint = document.getElementById('match-hint');
    if (!pw2) { hint.textContent=''; return; }
    if (pw1 === pw2) { hint.textContent='✓ Les mots de passe correspondent'; hint.style.color='#1B4F6B'; }
    else { hint.textContent='Les mots de passe ne correspondent pas'; hint.style.color='#C81224'; }
}
document.getElementById('register-form').addEventListener('submit', function() {
    var btn = document.getElementById('submit-btn');
    btn.disabled = true; btn.style.opacity = '.7';
    document.getElementById('btn-text').textContent = 'Création en cours...';
    document.getElementById('btn-spinner').classList.remove('hidden');
});
</script>
</body>
</html>
