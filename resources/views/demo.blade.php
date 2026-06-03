<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="index, follow">
<link rel="canonical" href="https://immo.bimotechsn.com/demo">
<title>Demander une démo — Renlio</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" media="print" onload="this.media='all'">
<noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap"></noscript>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-body bg-[#0d1117] text-[#e6edf3]">

@include('partials.public-nav', ['active' => 'contact'])

{{-- Hero --}}
<div class="pt-[120px] pb-16 px-[5%] text-center">
    <div class="font-body font-semibold text-[11px] uppercase tracking-[2px] text-bimo-gold mb-4">Démo gratuite</div>
    <h1 class="font-display font-extrabold text-[clamp(28px,5vw,50px)] tracking-tight leading-tight mb-4">
        Voyez Renlio en action<br>pour votre <em class="not-italic text-bimo-gold">agence</em>
    </h1>
    <p class="font-body font-light text-[15px] text-[#8b949e] max-w-[460px] mx-auto leading-relaxed">30 minutes de démonstration personnalisée. On configure ensemble votre espace avec vos vrais biens.</p>
</div>

{{-- Main --}}
<div class="max-w-[920px] mx-auto px-[5%] pb-24 grid grid-cols-1 md:grid-cols-[1.1fr_1fr] gap-12 items-start">

    {{-- Promesses + Témoignage --}}
    <div>
        <div class="flex flex-col gap-4">
            @foreach([
                ['30 minutes chrono','Une démo ciblée sur vos besoins réels. Pas de présentation générique, on va droit au but.','<circle cx="12" cy="12" r="10"/><polyline points="12,6 12,12 16,14"/>'],
                ['Démo sur votre vrai contexte','On part de votre portefeuille, vos loyers, votre secteur. La démo ressemble à votre agence.','<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>'],
                ['Par WhatsApp ou sur place','Démo en ligne ou déplacement à Dakar selon votre préférence. Aucune contrainte.','<path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81"/><path d="M1 1l22 22"/>'],
                ['Zéro engagement','La démo est gratuite. Aucune carte bancaire, aucun engagement de souscription.','<polyline points="20,6 9,17 4,12"/>'],
            ] as [$title, $desc, $icon])
            <div class="flex gap-3.5 bg-[#161b22] border border-[rgba(255,255,255,.08)] rounded-[14px] p-6">
                <div class="w-11 h-11 bg-bimo-gold rounded-[10px] flex items-center justify-center flex-shrink-0">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0d1117" stroke-width="2">{!! $icon !!}</svg>
                </div>
                <div>
                    <div class="font-body font-semibold text-sm text-[#e6edf3] mb-1">{{ $title }}</div>
                    <div class="font-body text-[13px] text-[#8b949e] leading-relaxed">{{ $desc }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <hr class="my-6 border-none border-t border-[rgba(255,255,255,.08)]">

        <div class="bg-[#161b22] border border-[rgba(255,255,255,.1)] rounded-[14px] p-6">
            <p class="font-body font-light text-sm text-[#8b949e] leading-[1.8] italic mb-4">"Avant Renlio, je gérais tout sur Excel. Maintenant mes quittances sont générées automatiquement et mes propriétaires voient leurs revenus en temps réel. La démo m'a convaincu en 20 minutes."</p>
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-[rgba(201,168,76,.15)] rounded-full flex items-center justify-center font-body font-semibold text-[13px] text-bimo-gold">AD</div>
                <div>
                    <div class="font-body font-medium text-[13px] text-[#e6edf3]">Amadou D.</div>
                    <div class="font-body text-xs text-[#484f58]">Directeur, Agence Diallo Immobilier — Dakar</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Formulaire --}}
    <div class="bg-[#161b22] border border-[rgba(255,255,255,.08)] rounded-[16px] p-10 md:sticky md:top-[84px]">
        <div class="font-display font-bold text-lg text-[#e6edf3] mb-1">Réserver ma démo</div>
        <p class="font-body text-[13px] text-[#8b949e] mb-7 leading-relaxed">Réponse sous 2h en semaine · Démo planifiée sous 48h</p>

        @if(session('success'))
        <div class="border-l-[3px] border-[#3B6D11] bg-[rgba(59,109,17,.1)] border border-[rgba(59,109,17,.2)] rounded-[8px] px-4 py-3 font-body text-sm text-[#86d066] mb-5">✓ Demande reçue ! Nous vous contactons dans les 2h pour planifier la démo.</div>
        @endif

        @if($errors->any())
        <div class="border-l-[3px] border-[#E24B4A] bg-[rgba(226,75,74,.08)] border border-[rgba(226,75,74,.2)] rounded-[8px] px-4 py-2.5 mb-5">
            @foreach($errors->all() as $error)<p class="font-body text-xs text-[#f0a0a0] leading-relaxed">{{ $error }}</p>@endforeach
        </div>
        @endif

        @if(!session('success'))
        <form method="POST" action="{{ route('demo.send') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom') }}" placeholder="Amadou"
                           class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] placeholder-[#484f58] outline-none focus:border-bimo-gold focus:bg-[#1c2128] transition-colors duration-200 appearance-none">
                    @error('prenom')<p class="font-body text-xs text-[#f0a0a0] mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" placeholder="Diallo"
                           class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] placeholder-[#484f58] outline-none focus:border-bimo-gold focus:bg-[#1c2128] transition-colors duration-200 appearance-none">
                    @error('nom')<p class="font-body text-xs text-[#f0a0a0] mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Nom de votre agence *</label>
                <input type="text" name="agence" value="{{ old('agence') }}" placeholder="Agence Immobilière Diallo"
                       class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] placeholder-[#484f58] outline-none focus:border-bimo-gold focus:bg-[#1c2128] transition-colors duration-200 appearance-none">
                @error('agence')<p class="font-body text-xs text-[#f0a0a0] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Téléphone (WhatsApp) *</label>
                <input type="tel" name="telephone" value="{{ old('telephone') }}" placeholder="+221 77 000 00 00"
                       class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] placeholder-[#484f58] outline-none focus:border-bimo-gold focus:bg-[#1c2128] transition-colors duration-200 appearance-none">
                @error('telephone')<p class="font-body text-xs text-[#f0a0a0] mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="amadou@agence.sn"
                       class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] placeholder-[#484f58] outline-none focus:border-bimo-gold focus:bg-[#1c2128] transition-colors duration-200 appearance-none">
                @error('email')<p class="font-body text-xs text-[#f0a0a0] mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-2.5">
                <div>
                    <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Nombre de biens</label>
                    <select name="nb_biens" class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] outline-none focus:border-bimo-gold cursor-pointer appearance-none transition-colors duration-200">
                        <option value="" {{ old('nb_biens') ? '' : 'selected' }} disabled>Choisir</option>
                        @foreach(['1-10'=>'1 à 10 biens','10-30'=>'10 à 30 biens','30-100'=>'30 à 100 biens','100+'=>'Plus de 100'] as $v => $l)
                        <option value="{{ $v }}" {{ old('nb_biens') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-body font-medium text-xs text-[#8b949e] mb-1.5">Ville</label>
                    <select name="ville" class="w-full bg-[#0d1117] border border-[rgba(255,255,255,.1)] rounded-[10px] px-3.5 py-2.5 font-body text-sm text-[#e6edf3] outline-none focus:border-bimo-gold cursor-pointer appearance-none transition-colors duration-200">
                        <option value="" {{ old('ville') ? '' : 'selected' }} disabled>Choisir</option>
                        @foreach(['dakar'=>'Dakar','thies'=>'Thiès','saint-louis'=>'Saint-Louis','ziguinchor'=>'Ziguinchor','autre'=>'Autre'] as $v => $l)
                        <option value="{{ $v }}" {{ old('ville') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button type="submit"
                    class="w-full bg-bimo-gold text-[#0d1117] font-body font-bold text-sm py-3.5 rounded-[10px] border-none cursor-pointer hover:opacity-90 transition-opacity duration-200">
                Réserver ma démo gratuite →
            </button>
        </form>
        @endif

        <div class="flex items-center gap-3 my-5">
            <div class="flex-1 h-px bg-[rgba(255,255,255,.08)]"></div>
            <span class="font-body text-xs text-[#484f58]">ou directement sur</span>
            <div class="flex-1 h-px bg-[rgba(255,255,255,.08)]"></div>
        </div>

        <a href="https://wa.me/221XXXXXXXXX?text=Bonjour Renlio, je souhaite réserver une démo pour mon agence." target="_blank"
           class="flex items-center justify-center gap-2.5 bg-[rgba(37,211,102,.1)] border border-[rgba(37,211,102,.2)] rounded-[10px] p-3 no-underline text-[#25d366] font-body font-semibold text-[13.5px] hover:bg-[rgba(37,211,102,.15)] transition-colors duration-200">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#25d366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Contacter sur WhatsApp
        </a>
    </div>

</div>

{{-- Footer --}}
<footer class="px-[5%] py-8 border-t border-[rgba(255,255,255,.08)] flex flex-col md:flex-row items-center justify-between gap-4 flex-wrap">
    <div class="font-display font-extrabold text-[15px] text-bimo-gold">Renlio</div>
    <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
        @foreach([[url('/'),'Accueil'],[route('contact'),'Contact'],[route('mentions-legales'),'Mentions légales']] as [$href,$lbl])
        <a href="{{ $href }}" class="font-body text-xs text-[#8b949e] no-underline hover:text-[#e6edf3] transition-colors duration-200">{{ $lbl }}</a>
        @endforeach
    </div>
    <div class="font-body text-xs text-[#484f58]">© {{ date('Y') }} Renlio · Dakar, Sénégal</div>
</footer>

</body>
</html>
