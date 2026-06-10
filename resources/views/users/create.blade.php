@extends('layouts.app')
@section('header', 'Nouveau ' . ($role === 'proprietaire' ? 'propriétaire' : 'locataire'))

@section('content')

{{-- Breadcrumb + titre --}}
<div class="mb-5">
    <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">
        Nouveau {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
    </h1>
    <p class="font-body text-sm text-bimo-text/50 mt-1">
        {{ $role === 'proprietaire'
            ? 'Renseignez les informations du propriétaire.'
            : 'Renseignez les informations du locataire.' }}
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6 items-start">

    {{-- ═══ SIDEBAR NAVIGATION ═══ --}}
    <div class="lg:sticky lg:top-6">
        <div class="bg-bimo-navy rounded-[14px] overflow-hidden">
            {{-- Badge rôle --}}
            <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10">
                <div class="w-10 h-10 rounded-[10px] bg-bimo-gold/15 border border-bimo-gold/25 flex items-center justify-center text-xl flex-shrink-0">
                    {{ $role === 'proprietaire' ? '🏢' : '👤' }}
                </div>
                <div>
                    <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/60">Création</div>
                    <div class="font-display font-bold text-sm text-white">
                        {{ $role === 'proprietaire' ? 'Propriétaire' : 'Locataire' }}
                    </div>
                </div>
            </div>

            {{-- Liens sections --}}
            <nav class="px-3 py-3 space-y-0.5">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-white/25 px-3 pt-1 pb-2">Sections</div>
                @php
                    $sections = [
                        ['sec-identite', 'Identité'],
                        ['sec-acces',    'Accès & Mot de passe'],
                    ];
                    if ($role === 'proprietaire') {
                        $sections[] = ['sec-paiement', 'Mode de paiement'];
                        $sections[] = ['sec-fiscal',   'Fiscal'];
                    } else {
                        $sections[] = ['sec-type',     'Type & Statut fiscal'];
                        $sections[] = ['sec-pro',      'Situation professionnelle'];
                        $sections[] = ['sec-urgence',  'Contact d\'urgence'];
                    }
                @endphp
                @foreach($sections as $i => [$id, $label])
                <button onclick="scrollToSection('{{ $id }}')" data-section="{{ $id }}"
                        class="nav-section-btn flex items-center gap-3 w-full px-3 py-2 rounded-[8px] transition-all duration-150 text-left
                               {{ $i === 0 ? 'bg-white/10 text-bimo-gold' : 'text-white/50 hover:text-white hover:bg-white/5' }}">
                    <span class="w-5 h-5 rounded-full border flex items-center justify-center text-[10px] font-bold flex-shrink-0
                                 {{ $i === 0 ? 'border-bimo-gold text-bimo-gold' : 'border-white/20 text-white/30' }}">
                        {{ $i + 1 }}
                    </span>
                    <span class="font-display font-semibold text-xs">{{ $label }}</span>
                </button>
                @endforeach
            </nav>

            <div class="px-4 py-4 border-t border-white/10">
                <p class="font-body text-[11px] text-white/30 leading-relaxed">
                    Les champs <span class="text-bimo-gold font-semibold">*</span> sont obligatoires. Les autres peuvent être complétés depuis la fiche.
                </p>
            </div>
        </div>
    </div>

    {{-- ═══ FORMULAIRE ═══ --}}
    <div>
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="role" value="{{ $role }}">

            {{-- ── SECTION 1 : IDENTITÉ ── --}}
            <div id="sec-identite" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-gold flex items-center justify-center font-display font-bold text-xs text-bimo-text flex-shrink-0">1</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">Identité personnelle</div>
                        <div class="font-body text-xs text-bimo-text/40">Informations de contact principales</div>
                    </div>
                </div>
                <div class="px-5 py-5 space-y-4">

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Nom complet <span class="text-bimo-red">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Ex: Moussa Diallo" autofocus
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('name') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('name')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" placeholder="+221 77 000 00 00"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Adresse <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Rue, quartier..."
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>

                    {{-- Genre pills --}}
                    <div class="space-y-2">
                        <label class="block font-body font-medium text-sm text-bimo-text">Genre</label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['M' => ['👨', 'Homme'], 'F' => ['👩', 'Femme']] as $val => [$emoji, $lbl])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="genre" value="{{ $val }}" {{ old('genre') === $val ? 'checked':'' }}
                                       class="sr-only peer">
                                <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-bimo-navy/10 rounded-[10px] font-body font-medium text-sm text-bimo-text/60
                                            peer-checked:border-bimo-gold peer-checked:bg-bimo-gold/10 peer-checked:text-bimo-gold
                                            hover:border-bimo-gold/40 transition-all duration-150 cursor-pointer">
                                    <span>{{ $emoji }}</span> {{ $lbl }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Date de naissance</label>
                            <input type="date" name="date_naissance" value="{{ old('date_naissance') }}"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Nationalité</label>
                            <input type="text" name="nationalite" value="{{ old('nationalite', 'Sénégalaise') }}"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Ville</label>
                            <select name="ville"
                                    class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text cursor-pointer
                                           focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                                @foreach(['Dakar','Thiès','Saint-Louis','Ziguinchor','Kaolack','Mbour','Rufisque','Touba','Diourbel','Tambacounda'] as $v)
                                <option value="{{ $v }}" {{ old('ville','Dakar') === $v ? 'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            CNI / Passeport <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="text" name="cni" value="{{ old('cni') }}" placeholder="1 234 567 890 12"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                    </div>

                </div>
            </div>

            {{-- ── SECTION 2 : ACCÈS ── --}}
            <div id="sec-acces" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-xs text-white flex-shrink-0">2</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">Accès à l'espace personnel</div>
                        <div class="font-body text-xs text-bimo-text/40">
                            @if($role === 'proprietaire') Optionnel — si renseigné, le propriétaire pourra se connecter
                            @else Identifiants de connexion au portail locataire
                            @endif
                        </div>
                    </div>
                </div>
                <div class="px-5 py-5 space-y-4">

                    <div class="space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            Email
                            @if($role === 'proprietaire')
                                <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                            @else
                                <span class="text-bimo-red">*</span>
                            @endif
                        </label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="email@exemple.com"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                      @error('email') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                      @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                        @error('email')<p class="mt-1 font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Mot de passe
                                @if($role === 'proprietaire')
                                    <span class="font-normal text-bimo-text/40 text-xs ml-1">(requis si email renseigné)</span>
                                @else
                                    <span class="text-bimo-red">*</span>
                                @endif
                            </label>
                            <input type="password" name="password" id="pwd" placeholder="Min. 8 caractères"
                                   oninput="checkPwd(this.value)"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:ring-2 transition-all duration-150
                                          @error('password') border-bimo-red focus:border-bimo-red focus:ring-bimo-red/15
                                          @else border-bimo-navy/20 focus:border-bimo-gold focus:ring-bimo-gold/15 @enderror">
                            <div class="h-1 rounded-full bg-bimo-navy/10 mt-2 overflow-hidden">
                                <div id="pwd-bar" class="h-full rounded-full transition-all duration-300" style="width:0%;background:#EF4444"></div>
                            </div>
                            <p id="pwd-hint" class="font-body text-[11px] text-bimo-text/40">Entrez un mot de passe</p>
                            @error('password')<p class="font-body text-xs text-bimo-red">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Confirmer le mot de passe
                                @if($role === 'proprietaire')
                                    <span class="font-normal text-bimo-text/40 text-xs ml-1">(requis si email renseigné)</span>
                                @else
                                    <span class="text-bimo-red">*</span>
                                @endif
                            </label>
                            <input type="password" name="password_confirmation" id="pwd2" placeholder="Répétez le mot de passe"
                                   oninput="checkConfirm()"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            <p id="pwd2-hint" class="font-body text-[11px] mt-1" style="color:transparent">—</p>
                        </div>
                    </div>

                    @if($role === 'proprietaire')
                    <div class="flex items-center gap-3 bg-bimo-gold/[8%] border border-bimo-gold/25 rounded-[10px] px-4 py-3">
                        <span class="text-base">💡</span>
                        <p class="font-body text-xs text-bimo-gold">Sans email, le propriétaire n'aura pas accès à son espace en ligne. Vous pourrez l'activer plus tard depuis sa fiche.</p>
                    </div>
                    @else
                    <div class="flex items-center gap-3 bg-bimo-navy/[5%] border border-bimo-navy/10 rounded-[10px] px-4 py-3">
                        <span class="text-base">✉️</span>
                        <p class="font-body text-xs text-bimo-text/70">Le locataire pourra se connecter avec son email sur <strong>{{ config('app.url') }}</strong></p>
                    </div>
                    @endif

                </div>
            </div>

            {{-- ══ SECTIONS PROPRIÉTAIRE ══ --}}
            @if($role === 'proprietaire')

            {{-- Section 3 : Mode de paiement --}}
            <div id="sec-paiement" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-xs text-white flex-shrink-0">3</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">Mode de paiement préféré</div>
                        <div class="font-body text-xs text-bimo-text/40">Comment reverser les loyers nets au propriétaire</div>
                    </div>
                </div>
                <div class="px-5 py-5 space-y-4">

                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            'virement'     => ['Virement',    '🏦'],
                            'wave'         => ['Wave',         '📱'],
                            'orange_money' => ['Orange Money', '🟠'],
                            'especes'      => ['Espèces',      '💵'],
                            'cheque'       => ['Chèque',       '📝'],
                            'mobile_money' => ['Mobile Money', '📲'],
                        ] as $val => [$lbl, $emoji])
                        <label class="relative cursor-pointer">
                            <input type="radio" name="mode_paiement_prefere" value="{{ $val }}"
                                   {{ old('mode_paiement_prefere','virement') === $val ? 'checked':'' }}
                                   class="sr-only peer">
                            <div class="flex flex-col items-center gap-1.5 p-3 border-2 border-bimo-navy/10 rounded-[10px] text-center
                                        peer-checked:border-bimo-gold peer-checked:bg-bimo-gold/10
                                        hover:border-bimo-gold/40 transition-all duration-150 cursor-pointer">
                                <span class="text-xl">{{ $emoji }}</span>
                                <span class="font-body font-medium text-[11px] text-bimo-text/60 peer-checked:text-bimo-gold">{{ $lbl }}</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Numéro Wave</label>
                            <input type="text" name="numero_wave" value="{{ old('numero_wave') }}" placeholder="+221 77 XXX XX XX"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Numéro Orange Money</label>
                            <input type="text" name="numero_om" value="{{ old('numero_om') }}" placeholder="+221 77 XXX XX XX"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Banque <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                            </label>
                            <input type="text" name="banque" value="{{ old('banque') }}" placeholder="CBAO, Ecobank, BIS..."
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">
                                Numéro de compte <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                            </label>
                            <input type="text" name="numero_compte" value="{{ old('numero_compte') }}" placeholder="RIB / IBAN"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4 : Fiscal propriétaire --}}
            <div id="sec-fiscal" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-xs text-white flex-shrink-0">4</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">Informations fiscales</div>
                        <div class="font-body text-xs text-bimo-text/40">NINEA et statut TVA du propriétaire</div>
                    </div>
                </div>
                <div class="px-5 py-5">
                    <div class="max-w-xs space-y-1.5">
                        <label class="block font-body font-medium text-sm text-bimo-text">
                            NINEA <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </label>
                        <input type="text" name="ninea" value="{{ old('ninea') }}" placeholder="Ex: 00123456789"
                               class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                      placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        <p class="font-body text-[11px] text-bimo-text/30">Numéro d'Identification National des Entreprises</p>
                    </div>
                </div>
            </div>

            @endif

            {{-- ══ SECTIONS LOCATAIRE ══ --}}
            @if($role === 'locataire')

            {{-- Section 3 : Type & Statut fiscal --}}
            <div id="sec-type" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-xs text-white flex-shrink-0">3</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">Type de locataire & Statut fiscal</div>
                        <div class="font-body text-xs text-bimo-text/40">Détermine si la Retenue à la Source (BRS) s'applique</div>
                    </div>
                </div>
                <div class="px-5 py-5 space-y-4">

                    <div class="space-y-2">
                        <label class="block font-body font-medium text-sm text-bimo-text">Type de locataire</label>
                        <div class="grid grid-cols-3 md:grid-cols-5 gap-2">
                            @foreach([
                                'particulier' => ['Particulier', '👤', false],
                                'entreprise'  => ['Entreprise',  '🏢', true],
                                'association' => ['Association', '🤝', true],
                                'ambassade'   => ['Ambassade',   '🏛️', false],
                                'ong'         => ['ONG / Org.', '🌍', false],
                            ] as $k => [$lbl, $ico, $brs])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type_locataire" value="{{ $k }}"
                                       {{ old('type_locataire','particulier') === $k ? 'checked':'' }}
                                       onchange="onTypeChange('{{ $k }}', {{ $brs ? 'true':'false' }})"
                                       class="sr-only peer">
                                <div class="flex flex-col items-center gap-1 p-3 border-2 border-bimo-navy/10 rounded-[10px] text-center cursor-pointer
                                            peer-checked:border-bimo-gold peer-checked:bg-bimo-gold/10
                                            hover:border-bimo-gold/40 transition-all duration-150">
                                    <span class="text-lg">{{ $ico }}</span>
                                    <span class="font-body font-medium text-[10px] text-bimo-text/60">{{ $lbl }}</span>
                                    @if($brs)<span class="font-body font-bold text-[9.5px] text-bimo-red">BRS 5%</span>@endif
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <input type="hidden" name="est_entreprise" id="est_entreprise"
                               value="{{ in_array(old('type_locataire','particulier'),['entreprise','association']) ? '1':'0' }}">
                    </div>

                    {{-- Bloc entreprise --}}
                    <div id="bloc-entreprise"
                         class="{{ in_array(old('type_locataire','particulier'),['entreprise','association']) ? '' : 'hidden' }}
                                bg-bimo-red/[4%] border border-bimo-red/20 rounded-[12px] p-4 space-y-3">
                        <div class="flex items-center gap-2 font-body font-semibold text-sm text-bimo-red">
                            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            BRS 5% — Retenue à la source automatique sur les paiements futurs (Art. 201 CGI SN)
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Raison sociale</label>
                            <input type="text" name="nom_entreprise" value="{{ old('nom_entreprise') }}" placeholder="Nom officiel de la société"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="block font-body font-medium text-sm text-bimo-text">NINEA</label>
                                <input type="text" name="ninea_locataire" value="{{ old('ninea_locataire') }}" placeholder="00123456789"
                                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block font-body font-medium text-sm text-bimo-text">RCCM</label>
                                <input type="text" name="rccm_locataire" value="{{ old('rccm_locataire') }}" placeholder="SN-DKR-2024-B-XXXXX"
                                       class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                              placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            </div>
                        </div>
                        <div class="max-w-[180px] space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Taux BRS personnalisé (%)</label>
                            <input type="number" name="taux_brs_override" value="{{ old('taux_brs_override') }}" placeholder="5" min="0" max="20" step="0.5"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                            <p class="font-body text-[11px] text-bimo-text/30">Vide = 5% légal (Art. 201 CGI SN)</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Section 4 : Situation professionnelle --}}
            <div id="sec-pro" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-xs text-white flex-shrink-0">4</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">Situation professionnelle</div>
                        <div class="font-body text-xs text-bimo-text/40">Permet de calculer le taux d'effort locatif</div>
                    </div>
                </div>
                <div class="px-5 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Profession</label>
                            <input type="text" name="profession" value="{{ old('profession') }}" placeholder="Ex: Ingénieur"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Employeur</label>
                            <input type="text" name="employeur" value="{{ old('employeur') }}" placeholder="Nom de l'employeur"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Revenu mensuel (F)</label>
                            <input type="number" name="revenu_mensuel" value="{{ old('revenu_mensuel') }}" placeholder="350000" min="0"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 5 : Contact urgence --}}
            <div id="sec-urgence" class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
                <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                    <div class="w-7 h-7 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-xs text-white flex-shrink-0">5</div>
                    <div>
                        <div class="font-display font-bold text-sm text-bimo-text">
                            Contact d'urgence <span class="font-normal text-bimo-text/40 text-xs ml-1">(optionnel)</span>
                        </div>
                        <div class="font-body text-xs text-bimo-text/40">Personne à contacter en cas d'urgence</div>
                    </div>
                </div>
                <div class="px-5 py-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Nom</label>
                            <input type="text" name="contact_urgence_nom" value="{{ old('contact_urgence_nom') }}" placeholder="Prénom NOM"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Téléphone</label>
                            <input type="text" name="contact_urgence_tel" value="{{ old('contact_urgence_tel') }}" placeholder="+221 7X XXX XX XX"
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block font-body font-medium text-sm text-bimo-text">Lien</label>
                            <input type="text" name="contact_urgence_lien" value="{{ old('contact_urgence_lien') }}" placeholder="Père, Conjoint..."
                                   class="w-full px-4 py-3 rounded-[10px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text
                                          placeholder:text-bimo-text/30 focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150">
                        </div>
                    </div>
                </div>
            </div>

            @endif

            {{-- Submit --}}
            <div class="sticky bottom-0 flex items-center justify-end gap-3 px-0 py-4
                        bg-bimo-bg/95 backdrop-blur-sm border-t border-bimo-navy/10">
                <a href="{{ $role === 'proprietaire' ? route('admin.users.proprietaires') : route('admin.users.locataires') }}"
                   class="px-5 py-2.5 border border-bimo-navy/15 rounded-[10px]
                          font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[var(--ac)] text-white
                               font-display font-bold text-sm rounded-[10px]
                               hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Créer le {{ $role === 'proprietaire' ? 'propriétaire' : 'locataire' }}
                </button>
            </div>

        </form>
    </div>

</div>

@push('scripts')
<script>
function scrollToSection(id) {
    document.getElementById(id)?.scrollIntoView({ behavior:'smooth', block:'start' });
    document.querySelectorAll('.nav-section-btn').forEach(b => {
        b.classList.remove('bg-white/10','text-bimo-gold');
        b.classList.add('text-white/50');
    });
    const btn = document.querySelector(`[data-section="${id}"]`);
    if (btn) { btn.classList.add('bg-white/10','text-bimo-gold'); btn.classList.remove('text-white/50'); }
}

function checkPwd(v) {
    const bar = document.getElementById('pwd-bar');
    const hint = document.getElementById('pwd-hint');
    let score = 0;
    if (v.length >= 8) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;
    const levels = [
        { pct:'25%', color:'#EF4444', label:'Trop faible' },
        { pct:'50%', color:'#f59e0b', label:'Faible' },
        { pct:'75%', color:'#3b82f6', label:'Correct' },
        { pct:'100%',color:'var(--ac)', label:'Fort ✓' },
    ];
    if (v.length === 0) { bar.style.width='0%'; hint.textContent='Entrez un mot de passe'; hint.style.color=''; return; }
    const l = levels[Math.max(0, score - 1)] ?? levels[0];
    bar.style.width = l.pct;
    bar.style.background = l.color;
    hint.textContent = l.label;
    hint.style.color = l.color;
    checkConfirm();
}

function checkConfirm() {
    const pwd = document.getElementById('pwd').value;
    const pwd2 = document.getElementById('pwd2').value;
    const hint = document.getElementById('pwd2-hint');
    if (!pwd2) { hint.style.color='transparent'; return; }
    if (pwd === pwd2) { hint.textContent='✓ Correspondent'; hint.style.color='var(--ac)'; }
    else { hint.textContent='✗ Ne correspondent pas'; hint.style.color='#EF4444'; }
}

function onTypeChange(type, isBrs) {
    document.getElementById('est_entreprise').value = isBrs ? '1' : '0';
    const bloc = document.getElementById('bloc-entreprise');
    if (bloc) { if (isBrs) bloc.classList.remove('hidden'); else bloc.classList.add('hidden'); }
}
</script>
@endpush

@endsection
