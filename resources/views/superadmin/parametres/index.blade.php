@extends('layouts.superadmin')

@section('title', 'Paramètres système')

@php
    use App\Support\JournalCritique;

    // Pastille de sévérité (dérivée du code d'action, cf. JournalCritique).
    $pastille = JournalCritique::SEVERITE_PASTILLE;

    // Statut d'intégration → classes de badge.
    $chip = [
        'green' => 'bg-green/10 text-green',
        'gold'  => 'bg-gold/10 text-gold-deep',
        'gray'  => 'bg-ink/[0.06] text-muted',
    ];

    $tempsRelatif = function (?\Carbon\Carbon $d) {
        if (! $d) return '—';
        if ($d->isToday())     return "Aujourd'hui, ".$d->format('H:i');
        if ($d->isYesterday()) return 'Hier, '.$d->format('H:i');
        return $d->locale('fr')->isoFormat('D MMM Y, HH:mm');
    };
@endphp

@section('content')
<div class="max-w-[1000px] mx-auto" x-data="parametresTabs" data-initial="{{ $tab }}">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif

    {{-- ─────────── En-tête ─────────── --}}
    <div class="mb-1">
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] md:text-[32px] text-ink mt-1">Paramètres système</h1>
    </div>
    <p class="text-[14.5px] text-muted mb-6">Configuration globale, sécurité des comptes, et journal des actions sensibles.</p>

    {{-- ─────────── Sous-navigation ─────────── --}}
    <div class="flex gap-1 mb-6 border-b border-line">
        <button type="button" x-on:click="showGeneral" x-bind:class="generalClass"
                class="px-1 py-2.5 mr-6 text-[13.5px] font-semibold border-b-2 -mb-px transition-colors">Général</button>
        <button type="button" x-on:click="showSecurite" x-bind:class="securiteClass"
                class="px-1 py-2.5 mr-6 text-[13.5px] font-semibold border-b-2 -mb-px transition-colors">Sécurité</button>
        <button type="button" x-on:click="showJournal" x-bind:class="journalClass"
                class="px-1 py-2.5 text-[13.5px] font-semibold border-b-2 -mb-px transition-colors">Journal d'activité critique</button>
    </div>

    {{-- ═══════════ Onglet : Général ═══════════ --}}
    <div x-show="isGeneral" x-cloak>

        {{-- Informations générales --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden mb-5">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Informations générales</div>
            <form method="POST" action="{{ route('superadmin.parametres.general') }}" class="p-5">
                @csrf
                @method('PATCH')

                <div class="mb-4">
                    <label class="block text-[11px] font-semibold uppercase tracking-[0.04em] text-muted mb-1.5">Nom de la plateforme</label>
                    <input type="text" name="plateforme_nom" value="{{ old('plateforme_nom', $settings->get('plateforme_nom')) }}"
                           placeholder="{{ $settings->plateformeNom() }}"
                           class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-[13.5px] text-ink">
                    @error('plateforme_nom')<p class="text-[12px] text-error mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label class="block text-[11px] font-semibold uppercase tracking-[0.04em] text-muted mb-1.5">Email support</label>
                    <input type="text" name="support_email" value="{{ old('support_email', $settings->supportEmail()) }}"
                           class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-[13.5px] text-ink">
                    @error('support_email')<p class="text-[12px] text-error mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label class="block text-[11px] font-semibold uppercase tracking-[0.04em] text-muted mb-1.5">Téléphone support</label>
                    <input type="text" name="support_telephone" value="{{ old('support_telephone', $settings->supportTelephone()) }}"
                           placeholder="+221 XX XXX XX XX"
                           class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-[13.5px] text-ink">
                    @error('support_telephone')<p class="text-[12px] text-error mt-1">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="text-[13px] font-semibold px-4 py-2.5 rounded-lg bg-teal-deep text-paper hover:bg-teal transition-colors">Enregistrer</button>
            </form>
        </section>

        {{-- Intégrations & paiements (statut seul, jamais les clés) --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden mb-5">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Intégrations &amp; paiements</div>
            <div class="p-5">
                <div class="flex gap-2.5 bg-paper-dim rounded-lg px-4 py-3.5 text-[12.5px] text-muted leading-relaxed">
                    <span class="text-gold shrink-0">🔒</span>
                    <span>Les clés API (PayTech, Wave, Orange Money, service d'email) ne sont <strong class="text-ink">pas gérées ici</strong>, pour des raisons de sécurité — elles vivent dans les variables d'environnement du serveur (Laravel Forge). Cet écran n'indique que leur état de configuration.</span>
                </div>

                @foreach($integrations as $i)
                    <div class="flex items-center justify-between gap-4 py-3.5 {{ $loop->last ? '' : 'border-b border-paper-dim' }} {{ $loop->first ? 'mt-1' : '' }}">
                        <div class="min-w-0">
                            <div class="text-[13.5px] font-semibold text-ink">{{ $i['nom'] }}</div>
                            <div class="text-[12px] text-muted mt-0.5">{{ $i['desc'] }}</div>
                        </div>
                        <span class="shrink-0 text-[11.5px] font-semibold px-2.5 py-1 rounded-full inline-flex items-center gap-1.5 {{ $chip[$i['variant']] }}">
                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>{{ $i['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Mode maintenance --}}
        <section class="bg-white border border-line rounded-xl overflow-hidden mb-5">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Mode maintenance</div>
            <form method="POST" action="{{ route('superadmin.parametres.maintenance') }}" class="p-5"
                  x-data="maintenancePanel" data-active="{{ $settings->maintenanceActive() ? '1' : '0' }}"
                  x-on:submit="onSubmit"
                  data-confirm="Activer le mode maintenance ? Toutes les agences verront une page d'attente jusqu'à sa désactivation.">
                @csrf
                @method('PATCH')

                <div class="flex items-center justify-between gap-4 bg-paper-dim rounded-lg px-4 py-4">
                    <div class="min-w-0">
                        <div class="text-[14px] font-semibold text-ink">Bloquer l'accès aux agences</div>
                        <div class="text-[12px] text-muted mt-0.5">Affiche une page d'attente à toutes les agences pendant une intervention technique. Le Super Admin reste accessible.</div>
                    </div>
                    <button type="button" x-on:click="toggle" x-bind:class="switchClass"
                            class="relative w-[34px] h-[19px] rounded-full shrink-0 transition-colors">
                        <span x-bind:class="knobClass" class="absolute top-[2px] w-[15px] h-[15px] rounded-full bg-white transition-all"></span>
                    </button>
                    <input type="hidden" name="maintenance_active" x-bind:value="activeValue">
                </div>

                <div class="mt-4">
                    <label class="block text-[11px] font-semibold uppercase tracking-[0.04em] text-muted mb-1.5">Message affiché aux agences</label>
                    <textarea name="maintenance_message" rows="3" maxlength="500"
                              class="w-full bg-paper border border-line rounded-lg px-3.5 py-2.5 text-[13.5px] text-ink leading-relaxed resize-none">{{ old('maintenance_message', $settings->maintenanceMessage()) }}</textarea>
                    @error('maintenance_message')<p class="text-[12px] text-error mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between gap-4 mt-4">
                    <div class="text-[11.5px] font-semibold flex items-center gap-2" x-bind:class="statusClass">
                        <span class="w-1.5 h-1.5 rounded-full" x-bind:class="dotClass"></span>
                        <span x-text="statusText"></span>
                    </div>
                    <button type="submit" class="text-[13px] font-semibold px-4 py-2.5 rounded-lg bg-teal-deep text-paper hover:bg-teal transition-colors">Enregistrer</button>
                </div>
            </form>
        </section>
    </div>

    {{-- ═══════════ Onglet : Sécurité ═══════════ --}}
    <div x-show="isSecurite" x-cloak>
        <section class="bg-white border border-line rounded-xl overflow-hidden mb-5"
                 x-data="securitePanel"
                 data-deux-fa="{{ $settings->deuxFacteursObligatoire() ? '1' : '0' }}"
                 data-session="{{ $settings->sessionExpiration() ? '1' : '0' }}"
                 data-mdp="{{ $settings->motDePasseRenforce() ? '1' : '0' }}">
            <div class="px-5 py-4 border-b border-paper-dim font-display font-medium text-[16.5px] text-ink">Sécurité des comptes admin</div>
            <form method="POST" action="{{ route('superadmin.parametres.securite') }}" class="p-5">
                @csrf
                @method('PATCH')

                {{-- 2FA --}}
                <div class="flex items-center justify-between gap-4 py-3.5 border-b border-paper-dim">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-semibold text-ink">Authentification à deux facteurs (2FA)</div>
                        <div class="text-[12px] text-muted mt-0.5">Obligatoire pour tous les comptes ayant accès au Super Admin.</div>
                    </div>
                    <button type="button" x-on:click="toggleDeuxFa" x-bind:class="deuxFaSwitch"
                            class="relative w-[34px] h-[19px] rounded-full shrink-0 transition-colors">
                        <span x-bind:class="deuxFaKnob" class="absolute top-[2px] w-[15px] h-[15px] rounded-full bg-white transition-all"></span>
                    </button>
                    <input type="hidden" name="securite_2fa_obligatoire" x-bind:value="deuxFaValue">
                </div>

                {{-- Expiration de session --}}
                <div class="flex items-center justify-between gap-4 py-3.5 border-b border-paper-dim">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-semibold text-ink">Expiration automatique de session</div>
                        <div class="text-[12px] text-muted mt-0.5">Déconnexion après une durée d'inactivité.</div>
                    </div>
                    <div class="flex items-center gap-3 shrink-0">
                        <div x-show="showSessionMinutes" x-cloak class="flex items-center gap-1.5">
                            <input type="number" name="securite_session_minutes" min="5" max="240"
                                   value="{{ old('securite_session_minutes', $settings->sessionMinutes()) }}"
                                   class="w-[64px] bg-paper border border-line rounded-lg px-2 py-1.5 text-[13px] text-ink tabular-nums text-center">
                            <span class="text-[12px] text-muted">min</span>
                        </div>
                        <button type="button" x-on:click="toggleSession" x-bind:class="sessionSwitch"
                                class="relative w-[34px] h-[19px] rounded-full shrink-0 transition-colors">
                            <span x-bind:class="sessionKnob" class="absolute top-[2px] w-[15px] h-[15px] rounded-full bg-white transition-all"></span>
                        </button>
                    </div>
                    <input type="hidden" name="securite_session_expiration" x-bind:value="sessionValue">
                </div>

                {{-- Politique de mot de passe --}}
                <div class="flex items-center justify-between gap-4 py-3.5">
                    <div class="min-w-0">
                        <div class="text-[13.5px] font-semibold text-ink">Politique de mot de passe renforcée</div>
                        <div class="text-[12px] text-muted mt-0.5">Minimum 12 caractères, majuscule, chiffre, caractère spécial.</div>
                    </div>
                    <button type="button" x-on:click="toggleMdp" x-bind:class="mdpSwitch"
                            class="relative w-[34px] h-[19px] rounded-full shrink-0 transition-colors">
                        <span x-bind:class="mdpKnob" class="absolute top-[2px] w-[15px] h-[15px] rounded-full bg-white transition-all"></span>
                    </button>
                    <input type="hidden" name="securite_mdp_renforce" x-bind:value="mdpValue">
                </div>

                @error('securite_session_minutes')<p class="text-[12px] text-error mt-1">{{ $message }}</p>@enderror

                <div class="mt-5 pt-4 border-t border-paper-dim flex items-center justify-between gap-4">
                    <p class="text-[11.5px] text-muted/90 leading-relaxed max-w-[540px]">
                        Ces réglages sont enregistrés et font foi. Leur application automatique (forcer la configuration 2FA, déconnexion sur inactivité, validation du mot de passe) est déployée progressivement.
                    </p>
                    <button type="submit" class="shrink-0 text-[13px] font-semibold px-4 py-2.5 rounded-lg bg-teal-deep text-paper hover:bg-teal transition-colors">Enregistrer</button>
                </div>
            </form>
        </section>
    </div>

    {{-- ═══════════ Onglet : Journal d'activité critique ═══════════ --}}
    <div x-show="isJournal" x-cloak>

        {{-- Filtres (soumission serveur, auto-submit Alpine) --}}
        <form method="GET" action="{{ route('superadmin.parametres.index') }}" x-data="agencyFilters"
              class="flex items-center gap-3 mb-4 flex-wrap">
            <input type="hidden" name="tab" value="journal">

            <select name="severite" x-on:change="apply"
                    class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
                <option value="toutes" @selected($severiteFiltre === 'toutes')>Sévérité : Toutes</option>
                @foreach($severites as $val => $label)
                    <option value="{{ $val }}" @selected($severiteFiltre === $val)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="admin" x-on:change="apply"
                    class="bg-white border border-line rounded-lg px-3 py-2.5 text-[13px] font-medium text-ink cursor-pointer">
                <option value="tous" @selected($adminFiltre === 'tous')>Administrateur : Tous</option>
                @foreach($admins as $a)
                    <option value="{{ $a->id }}" @selected($adminFiltre === (string) $a->id)>{{ $a->name }}</option>
                @endforeach
            </select>

            <div class="flex-1"></div>
            <span class="text-[12.5px] text-muted font-medium whitespace-nowrap">{{ $journal->total() }} événement(s)</span>
            <noscript><button type="submit" class="text-[13px] font-semibold text-teal">Filtrer</button></noscript>
        </form>

        <section class="bg-white border border-line rounded-xl overflow-hidden">
            @if($journal->isEmpty())
                <div class="px-5 py-14 text-center text-[13.5px] text-muted">Aucun événement critique pour ces critères.</div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left text-[11px] uppercase tracking-wide text-muted font-semibold bg-paper-dim">
                            <th class="px-4 py-3">Événement</th>
                            <th class="px-4 py-3">Administrateur</th>
                            <th class="px-4 py-3 whitespace-nowrap">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journal as $log)
                            @php $sev = JournalCritique::severite($log); @endphp
                            <tr class="text-[13.8px] hover:bg-paper/60 transition-colors {{ $loop->last ? '' : 'border-b border-paper-dim' }}">
                                <td class="px-4 py-3.5">
                                    <div class="flex items-start gap-2.5">
                                        <span class="mt-[6px] w-[7px] h-[7px] rounded-full shrink-0 {{ $pastille[$sev] }}"></span>
                                        <div class="min-w-0">
                                            <div class="text-ink">{{ $log->description }}</div>
                                            @if($log->agency)
                                                <div class="text-[12px] text-muted mt-0.5">{{ $log->agency->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 font-semibold text-ink whitespace-nowrap">{{ $log->user?->name ?? '—' }}</td>
                                <td class="px-4 py-3.5 text-muted whitespace-nowrap text-[12.5px]">{{ $tempsRelatif($log->created_at) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </section>

        @if($journal->hasPages())
            <div class="mt-6">{{ $journal->links() }}</div>
        @endif

        <p class="text-[11.5px] text-muted/80 mt-3">
            Vue filtrée du journal global : seuls les événements sensibles (suspensions, révocations, changements de plan, règles fiscales, paramètres système) apparaissent ici.
        </p>
    </div>
</div>
@endsection
