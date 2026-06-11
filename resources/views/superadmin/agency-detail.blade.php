@extends('layouts.app')
@section('header', $agency->name)

@section('content')

<div class="space-y-4">

    {{-- Header --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <nav class="flex items-center gap-2 font-body text-sm text-bimo-text/40">
            <a href="{{ route('superadmin.dashboard') }}" class="hover:text-bimo-text transition-colors duration-150">Agences</a>
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span class="text-bimo-text font-semibold">{{ $agency->name }}</span>
        </nav>
        <div class="flex items-center gap-2 flex-wrap">
            @if($agency->actif)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-body font-semibold bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70"><span class="w-1.5 h-1.5 rounded-full bg-bimo-navy/50 mr-1.5"></span>Active</span>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-body font-semibold bg-bimo-red/10 border border-bimo-red/20 text-bimo-red"><span class="w-1.5 h-1.5 rounded-full bg-bimo-red mr-1.5"></span>Suspendue</span>
            @endif
            <a href="{{ route('superadmin.agencies.edit', $agency) }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-bimo-navy/15 rounded-[9px] font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                Modifier
            </a>
            <form method="POST" action="{{ route('superadmin.agencies.toggle', $agency) }}"
                  data-confirm="{{ $agency->actif ? 'Suspendre cette agence ?' : 'Activer cette agence ?' }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-[9px] font-body text-sm border cursor-pointer transition-all duration-150 {{ $agency->actif ? 'border-bimo-red/20 bg-bimo-red/[5%] text-bimo-red hover:bg-bimo-red/10' : 'border-bimo-navy/15 text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text' }}">
                    {{ $agency->actif ? 'Suspendre' : 'Activer' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Identité + Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-[280px_1fr] gap-4">

        {{-- Carte identité --}}
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
                <span class="font-display font-bold text-sm text-bimo-text">Identité</span>
            </div>
            <div class="px-5 py-5">
                <div class="flex items-center gap-3 mb-5">
                    @if($agency->logo_path)
                    <img src="{{ Storage::url($agency->logo_path) }}" alt="{{ $agency->name }}"
                         class="w-12 h-12 object-contain rounded-[10px] border border-bimo-navy/10">
                    @else
                    <div class="w-12 h-12 rounded-[10px] bg-bimo-bg2 border border-dashed border-bimo-navy/20 flex items-center justify-center font-display font-bold text-xl text-bimo-text/40">
                        {{ strtoupper(substr($agency->name,0,1)) }}
                    </div>
                    @endif
                    <div>
                        <div class="font-body font-semibold text-sm text-bimo-text">{{ $agency->name }}</div>
                        <div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $agency->slug }}</div>
                    </div>
                </div>
                <div class="space-y-0 divide-y divide-bimo-navy/[5%]">
                    @foreach(array_filter([
                        ['Email', $agency->email],
                        ['Téléphone', $agency->telephone],
                        ['Adresse', $agency->adresse],
                        ['TVA', $agency->taux_tva.'%'],
                        ['Inscrite le', $agency->created_at->format('d/m/Y')],
                    ], fn($r) => !empty($r[1])) as [$lbl,$val])
                    <div class="flex items-start gap-3 py-2.5">
                        <span class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/30 min-w-[80px] mt-0.5">{{ $lbl }}</span>
                        <span class="font-body text-sm text-bimo-text/70">{{ $val }}</span>
                    </div>
                    @endforeach
                    @if($agency->couleur_primaire)
                    <div class="flex items-center gap-3 py-2.5">
                        <span class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/30 min-w-[80px]">Couleur</span>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full border border-bimo-navy/10 inline-block flex-shrink-0" style="background:{{ $agency->couleur_primaire }}"></span>
                            <span class="font-body text-sm text-bimo-text/70">{{ $agency->couleur_primaire }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 content-start">
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Utilisateurs</div>
                <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['nb_users'] }}</div>
                <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">{{ $stats['nb_proprietaires'] }} proprio · {{ $stats['nb_locataires'] }} locataires</div>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Biens</div>
                <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['nb_biens'] }}</div>
                <div class="font-body text-[10.5px] text-bimo-gold mt-1.5">{{ $stats['nb_biens_loues'] }} loués</div>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Contrats actifs</div>
                <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $stats['nb_contrats'] }}</div>
            </div>
            <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">Loyers encaissés</div>
                <div class="font-display font-extrabold text-xl text-bimo-gold leading-none">{{ number_format($stats['total_loyers'],0,',','') }}</div>
                <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">FCFA</div>
            </div>
            <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 col-span-2 md:col-span-1">
                <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Commissions générées</div>
                <div class="font-display font-extrabold text-xl text-bimo-text leading-none">{{ number_format($stats['total_commissions'],0,',','') }}</div>
                <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">FCFA TTC</div>
            </div>
        </div>
    </div>

    {{-- Utilisateurs --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Utilisateurs ({{ $users->count() }})</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Nom</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Email</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Rôle</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Inscrit le</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($users as $user)
                    @php
                        $roleBadge = match($user->role) { 'admin'=>'bg-bimo-navy/15 border-bimo-navy/20 text-bimo-text/80', 'proprietaire'=>'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'locataire'=>'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/60', default=>'bg-bimo-navy/[5%] text-bimo-text/40' };
                        $roleLabel = match($user->role) { 'admin'=>'Admin', 'proprietaire'=>'Propriétaire', 'locataire'=>'Locataire', default=>$user->role };
                        $isDisabled = (bool)$user->deleted_at;
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100 {{ $isDisabled ? 'opacity-50' : '' }}">
                        <td class="px-5 py-3.5 font-body font-semibold text-sm text-bimo-text">{{ $user->name }}</td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $user->email }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium border {{ $roleBadge }}">{{ $roleLabel }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-center font-body text-xs text-bimo-text/40">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @if($isDisabled)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red"><span class="w-1.5 h-1.5 rounded-full bg-bimo-red mr-1"></span>Désactivé</span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text/70"><span class="w-1.5 h-1.5 rounded-full bg-bimo-navy/50 mr-1"></span>Actif</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex items-center justify-center gap-2 flex-wrap">
                                @if($user->role === 'admin' && !$isDisabled)
                                <form method="POST" action="{{ route('superadmin.impersonate', $user) }}"
                                      data-confirm="Se connecter en tant que {{ $user->name }} ({{ $agency->name }}) ?">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150 whitespace-nowrap cursor-pointer">
                                        Accès
                                    </button>
                                </form>
                                @endif
                                <form method="POST" action="{{ route('superadmin.agencies.users.reset-password', [$agency, $user->id]) }}"
                                      data-confirm="Réinitialiser le mot de passe de {{ $user->name }} ?">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-1 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150 whitespace-nowrap cursor-pointer">
                                        Réinit. mdp
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('superadmin.agencies.users.toggle', [$agency, $user->id]) }}"
                                      data-confirm="{{ $isDisabled ? 'Réactiver '.$user->name.' ?' : 'Désactiver '.$user->name.' ?' }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="inline-flex items-center px-3 py-1 rounded-[7px] font-body text-xs border transition-all duration-150 whitespace-nowrap cursor-pointer {{ $isDisabled ? 'border-bimo-navy/15 text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text' : 'border-bimo-red/20 bg-bimo-red/[5%] text-bimo-red hover:bg-bimo-red/10' }}">
                                        {{ $isDisabled ? 'Réactiver' : 'Désactiver' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun utilisateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Abonnement --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2 flex items-center justify-between gap-3">
            <span class="font-display font-bold text-sm text-bimo-text">Abonnement</span>
            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('superadmin.agencies.essai.reinitialiser', $agency) }}"
                      data-confirm="Réinitialiser l'essai de 30 jours pour {{ $agency->name }} ?">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-bimo-navy/15 rounded-[7px] font-body text-xs text-bimo-text/60 hover:border-bimo-navy/30 hover:text-bimo-text transition-all duration-150 cursor-pointer">
                        Réinitialiser essai
                    </button>
                </form>
            </div>
        </div>
        <div class="px-5 py-5">
            @if($subscription)
            @php
                $statutColors = match($subscription->statut) {
                    'actif'   => 'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold',
                    'essai'   => 'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70',
                    'expiré'  => 'bg-bimo-red/10 border-bimo-red/20 text-bimo-red',
                    default   => 'bg-bimo-navy/[5%] border-bimo-navy/10 text-bimo-text/40',
                };
                $planLabel = config('plans.labels.'.$subscription->plan_niveau, ucfirst($subscription->plan_niveau ?? 'N/A'));
            @endphp
            <div class="flex flex-wrap gap-4 items-start">
                <div>
                    <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Statut</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium border {{ $statutColors }}">{{ ucfirst($subscription->statut) }}</span>
                </div>
                <div>
                    <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Plan</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium bg-bimo-navy/10 border border-bimo-navy/15 text-bimo-text font-semibold">{{ $planLabel }}</span>
                </div>
                @if($subscription->date_fin_abonnement)
                <div>
                    <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Expire le</p>
                    <p class="font-body text-sm text-bimo-text/70">{{ $subscription->date_fin_abonnement->format('d/m/Y') }}</p>
                </div>
                @elseif($subscription->date_fin_essai)
                <div>
                    <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-1">Essai jusqu'au</p>
                    <p class="font-body text-sm text-bimo-text/70">{{ $subscription->date_fin_essai->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
            <div class="mt-5">
                <p class="font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 mb-3">Activer un abonnement</p>
                <form method="POST" action="{{ route('superadmin.agencies.abonnement.activer', $agency) }}" class="flex flex-wrap items-end gap-3">
                    @csrf
                    <div>
                        <label class="block font-body text-xs text-bimo-text/50 mb-1">Niveau</label>
                        <select name="plan_niveau" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                            <option value="starter">Starter</option>
                            <option value="pro" selected>Pro</option>
                            <option value="agence">Agence</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-body text-xs text-bimo-text/50 mb-1">Cycle</label>
                        <select name="plan" class="px-3 py-2 rounded-[8px] bg-white border border-bimo-navy/20 font-body text-sm text-bimo-text focus:outline-none focus:border-bimo-gold focus:ring-2 focus:ring-bimo-gold/15 transition-all duration-150 appearance-none cursor-pointer">
                            <option value="mensuel">Mensuel</option>
                            <option value="annuel">Annuel</option>
                        </select>
                    </div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-[8px] bg-bimo-navy text-white font-display font-bold text-xs hover:bg-bimo-navy-dk transition-colors duration-150 cursor-pointer">
                        Activer
                    </button>
                </form>
            </div>
            @else
            <p class="font-body text-sm text-bimo-text/40">Aucun abonnement.</p>
            @endif
        </div>
    </div>

    {{-- Features à la carte --}}
    @php
        $hierarchy     = config('plans.hierarchy', ['starter', 'pro', 'agence']);
        $planNiveau    = $subscription?->plan_niveau ?? 'starter';
        $niveauEffect  = config('plans.niveau_effectif')[$planNiveau] ?? 'starter';
        $posActuelle   = array_search($niveauEffect, $hierarchy);

        $featureLabels = [
            'immeubles'           => 'Immeubles',
            'rapports_pdf'        => 'Rapports PDF',
            'export_csv'          => 'Export CSV',
            'releve_bailleur_pdf' => 'Relevé bailleur PDF',
            'recherche_globale'   => 'Recherche globale',
            'import_excel'        => 'Import Excel',
            'contrat_formel_pdf'  => 'Contrat PDF',
            'comptabilite'        => 'Comptabilité',
            'tresorerie'          => 'Trésorerie',
            'fiscalite'           => 'Fiscalité',
            'bilans_fiscaux'      => 'Bilans fiscaux',
            'logs_activite'       => 'Logs d\'activité',
        ];
    @endphp
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Features personnalisées</span>
            <p class="font-body text-xs text-bimo-text/40 mt-0.5">Activer ou désactiver des features individuellement, indépendamment du plan.</p>
        </div>
        <div class="divide-y divide-bimo-navy/[5%]">
            @foreach(config('plans.features') as $feature => $planRequis)
            @php
                $override      = $overrides->get($feature);
                $enabledByPlan = $posActuelle >= array_search($planRequis, $hierarchy);
                $enabledFinal  = $override ? $override->enabled : $enabledByPlan;
                $planRequisLabel = config('plans.labels.'.$planRequis, ucfirst($planRequis));
                $label         = $featureLabels[$feature] ?? $feature;
            @endphp
            <div class="px-5 py-3.5 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    {{-- Icône état --}}
                    @if($enabledFinal)
                    <div class="w-6 h-6 rounded-full bg-bimo-gold/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    @else
                    <div class="w-6 h-6 rounded-full bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                        <svg class="w-3.5 h-3.5 text-bimo-text/25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                    @endif
                    <div class="min-w-0">
                        <p class="font-body font-medium text-sm text-bimo-text">{{ $label }}</p>
                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                            <span class="font-body text-[10px] uppercase tracking-widest text-bimo-text/30">Requis : {{ $planRequisLabel }}</span>
                            @if($override)
                                @if($override->enabled && !$enabledByPlan)
                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[9.5px] font-body font-medium bg-bimo-gold/10 border border-bimo-gold/20 text-bimo-gold uppercase tracking-widest">Sur devis</span>
                                @elseif(!$override->enabled && $enabledByPlan)
                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[9.5px] font-body font-medium bg-bimo-red/10 border border-bimo-red/20 text-bimo-red uppercase tracking-widest">Bloqué</span>
                                @else
                                <span class="inline-flex items-center px-1.5 py-0 rounded text-[9.5px] font-body font-medium bg-bimo-navy/[5%] border border-bimo-navy/10 text-bimo-text/40 uppercase tracking-widest">Override</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($override)
                    {{-- Retirer l'override --}}
                    <form method="POST" action="{{ route('superadmin.agencies.features.remove', [$agency, $feature]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" title="Retirer l'override (revenir au plan)"
                                class="inline-flex items-center px-2.5 py-1 border border-bimo-navy/15 rounded-[6px] font-body text-[11px] text-bimo-text/40 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150 cursor-pointer">
                            ← Plan
                        </button>
                    </form>
                    @endif
                    {{-- Toggle --}}
                    <form method="POST" action="{{ route('superadmin.agencies.features.toggle', [$agency, $feature]) }}">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-3 py-1.5 rounded-[7px] font-body text-xs font-medium border transition-all duration-150 cursor-pointer
                                    {{ $enabledFinal
                                        ? 'border-bimo-red/20 bg-bimo-red/[5%] text-bimo-red hover:bg-bimo-red/10'
                                        : 'border-bimo-gold/25 bg-bimo-gold/[5%] text-bimo-gold hover:bg-bimo-gold/10' }}">
                            {{ $enabledFinal ? 'Désactiver' : 'Activer' }}
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Biens --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">Biens ({{ $biens->count() }})</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Référence</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Type</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Adresse</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Propriétaire</th>
                        <th class="px-5 py-3 text-right font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Loyer</th>
                        <th class="px-5 py-3 text-center font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @forelse($biens as $bien)
                    @php
                        $bstatut = match($bien->statut) { 'loue'=>['Loué','bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold'], 'disponible'=>['Disponible','bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70'], default=>[ucfirst($bien->statut),'bg-bimo-navy/[5%] text-bimo-text/40'] };
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 font-body text-[11px] text-bimo-text/40 uppercase tracking-widest" style="font-family:monospace">{{ $bien->reference }}</td>
                        <td class="px-5 py-3.5 font-body text-sm text-bimo-text/70">{{ $bien->type }}</td>
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $bien->adresse }}, {{ $bien->ville }}</td>
                        <td class="px-5 py-3.5 text-center font-body text-sm text-bimo-text/60">{{ $bien->proprietaire?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-display font-semibold text-sm text-bimo-text/70">{{ number_format($bien->loyer_mensuel,0,',','') }} FCFA</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-medium border {{ $bstatut[1] }}">{{ $bstatut[0] }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center font-body text-sm text-bimo-text/30">Aucun bien.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
