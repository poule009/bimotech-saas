<x-app-layout>
    <x-slot name="header">Contrats</x-slot>

<style>
/* ── KPI ROW ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.kpi-mini { background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:18px 20px; position:relative; overflow:hidden; transition:transform .15s; cursor:pointer; }
.kpi-mini:hover { transform:translateY(-1px); box-shadow:0 4px 16px -4px rgba(0,0,0,0.07); }
.kpi-mini.active-filter { box-shadow:0 0 0 2px #c9a84c; }
.kpi-mini::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; border-radius:12px 12px 0 0; }
.kpi-mini.gold::before  { background:#c9a84c; }
.kpi-mini.green::before { background:#16a34a; }
.kpi-mini.red::before   { background:#dc2626; }
.kpi-mini.gray::before  { background:#9ca3af; }
.kpi-lbl { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#6b7280;margin-bottom:5px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.3px;line-height:1; }
.kpi-s   { font-size:11px;color:#9ca3af;margin-top:5px; }

/* ── FILTRES ── */
.filter-bar { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 18px;margin-bottom:18px;display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.filter-input  { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;transition:border-color .15s; }
.filter-input:focus { border-color:#c9a84c;background:#fff; }
.filter-select { padding:8px 13px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#0d1117;font-family:'DM Sans',sans-serif;background:#f9fafb;outline:none;cursor:pointer; }
.filter-btn   { padding:8px 16px;background:#0d1117;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;white-space:nowrap; }
.filter-reset { padding:8px 14px;background:none;color:#6b7280;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px;white-space:nowrap; }

/* ── TABLE CARD ── */
.table-card { background:#fff; border:1px solid #e5e7eb; border-radius:14px; overflow:hidden; }
.table-header { padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.table-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#0d1117; }
.table-count { font-size:12px; color:#6b7280; margin-top:2px; }
.dt { width:100%; border-collapse:collapse; }
.dt thead tr { background:#f9fafb; }
.dt th { padding:10px 18px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;border-bottom:1px solid #e5e7eb;white-space:nowrap; }
.dt td { padding:14px 18px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr { transition:background .1s; }
.dt tbody tr:hover { background:#f9fafb; }
.th-r { text-align:right !important; }
.td-r  { text-align:right; }
.td-c  { text-align:center; }

/* cellules */
.ref-bail { font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af;letter-spacing:.3px; }
.bien-ref  { font-size:13px;font-weight:600;color:#0d1117; }
.bien-sub  { font-size:11px;color:#6b7280;margin-top:1px; }
.loc-name  { font-size:13px;font-weight:500;color:#0d1117; }
.loc-email { font-size:11px;color:#6b7280;margin-top:1px; }
.loyer-val { font-family:'Syne',sans-serif;font-weight:600;color:#0d1117; }
.loyer-sub { font-size:10px;color:#9ca3af;margin-top:1px; }
.date-val  { font-size:13px;color:#374151; }
.date-sub  { font-size:11px;color:#9ca3af;margin-top:1px; }

/* badge statut */
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600;white-space:nowrap; }
.badge.g    { background:#dcfce7;color:#16a34a; }
.badge.r    { background:#fee2e2;color:#dc2626; }
.badge.gray { background:#f3f4f6;color:#6b7280; }
.badge.o    { background:#fef3c7;color:#d97706; }
.bdot { width:5px;height:5px;border-radius:50%;background:currentColor; }

/* type bail */
.bail-tag { display:inline-block;padding:2px 8px;background:#f3f4f6;color:#6b7280;border-radius:6px;font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.5px; }

/* alerte expiration */
.expiry-soon { color:#d97706;font-weight:600; }
.expiry-alert { color:#dc2626;font-weight:600; }

/* actions */
.act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s; }
.act-btn:hover { border-color:#c9a84c;color:#8a6e2f;background:#f5e9c9; }
.act-btn svg { width:13px;height:13px; }
.act-btn.danger:hover { border-color:#dc2626;color:#dc2626;background:#fee2e2; }
.act-btn.primary { background:#0d1117;border-color:#0d1117;color:#fff; }
.act-btn.primary:hover { opacity:.85; }

/* état vide */
.empty-state { padding:56px 20px;text-align:center; }
.empty-icon  { width:56px;height:56px;border-radius:14px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;margin:0 auto 16px; }
.empty-icon svg { width:24px;height:24px;color:#8a6e2f; }
.empty-title { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:6px; }
.empty-sub   { font-size:13px;color:#6b7280; }

/* pagination */
.pagination-wrap { padding:16px 22px;border-top:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.pagination-info { font-size:12px;color:#6b7280; }
.pagination-links { display:flex;gap:4px; }
.page-btn { display:inline-flex;align-items:center;justify-content:center;min-width:32px;height:32px;padding:0 10px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;font-weight:500;text-decoration:none;transition:all .15s; }
.page-btn:hover { background:#f9fafb; }
.page-btn.active { background:#0d1117;color:#fff;border-color:#0d1117; }
.page-btn.disabled { opacity:.4;pointer-events:none; }

/* barre de progression durée */
.progress-bar { height:4px;background:#f3f4f6;border-radius:99px;overflow:hidden;margin-top:6px;width:80px; }
.progress-fill { height:100%;border-radius:99px; }
</style>

<div style="padding:24px 32px 48px">

    {{-- PAGE HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Contrats de bail</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ $stats['total'] }} contrat(s) au total · {{ $stats['actifs'] }} actif(s)
            </p>
        </div>
        <div style="display:flex;gap:10px">
            <a href="{{ route('admin.contrats.create') }}" class="btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouveau contrat
            </a>
        </div>
    </div>

    {{-- KPI ROW — cliquables pour filtrer --}}
    <div class="kpi-row">
        <a href="{{ route('admin.contrats.index') }}" style="text-decoration:none" class="kpi-mini gold {{ !request('statut') ? 'active-filter' : '' }}">
            <div class="kpi-lbl">Total contrats</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
            <div class="kpi-s">Tous statuts confondus</div>
        </a>
        <a href="{{ route('admin.contrats.index', ['statut' => 'actif']) }}" style="text-decoration:none" class="kpi-mini green {{ request('statut') === 'actif' ? 'active-filter' : '' }}">
            <div class="kpi-lbl">Contrats actifs</div>
            <div class="kpi-val">{{ $stats['actifs'] }}</div>
            <div class="kpi-s">En cours d'exécution</div>
        </a>
        <a href="{{ route('admin.contrats.index', ['statut' => 'resilié']) }}" style="text-decoration:none" class="kpi-mini red {{ request('statut') === 'resilié' ? 'active-filter' : '' }}">
            <div class="kpi-lbl">Résiliés</div>
            <div class="kpi-val">{{ $stats['resilies'] }}</div>
            <div class="kpi-s">Bail interrompu</div>
        </a>
        <a href="{{ route('admin.contrats.index', ['statut' => 'expiré']) }}" style="text-decoration:none" class="kpi-mini gray {{ request('statut') === 'expiré' ? 'active-filter' : '' }}">
            <div class="kpi-lbl">Expirés</div>
            <div class="kpi-val">{{ $stats['expires'] }}</div>
            <div class="kpi-s">Durée échue</div>
        </a>
    </div>

    {{-- FILTRES --}}
    <form method="GET" action="{{ route('admin.contrats.index') }}">
        {{-- Conserver le filtre statut cliqué depuis les KPI --}}
        @if(request('statut') && !request('q') && !request('type_bail'))
            <input type="hidden" name="statut" value="{{ request('statut') }}">
        @endif
        <div class="filter-bar">
            {{-- Recherche --}}
            <div style="position:relative;flex:1;min-width:180px">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Réf. bail, locataire, bien…" class="filter-input" style="padding-left:34px;width:100%">
            </div>

            {{-- Statut --}}
            <select name="statut" class="filter-select">
                <option value="">Tous les statuts</option>
                <option value="actif"   {{ request('statut')==='actif'   ? 'selected':'' }}>Actifs</option>
                <option value="resilié" {{ request('statut')==='resilié' ? 'selected':'' }}>Résiliés</option>
                <option value="expiré"  {{ request('statut')==='expiré'  ? 'selected':'' }}>Expirés</option>
            </select>

            {{-- Type de bail --}}
            <select name="type_bail" class="filter-select">
                <option value="">Tous les types</option>
                @foreach(\App\Models\Contrat::TYPES_BAIL as $key => $label)
                    <option value="{{ $key }}" {{ request('type_bail')===$key ? 'selected':'' }}>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="filter-btn">Filtrer</button>

            @if(request()->hasAny(['q','statut','type_bail']))
                <a href="{{ route('admin.contrats.index') }}" class="filter-reset">
                    <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Effacer
                </a>
            @endif
        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-card">
        <div class="table-header">
            <div>
                <div class="table-title">Liste des contrats</div>
                <div class="table-count">{{ $contrats->total() }} contrat(s) · Page {{ $contrats->currentPage() }} / {{ $contrats->lastPage() }}</div>
            </div>
            <div style="font-size:12px;color:#6b7280;display:flex;align-items:center;gap:6px">
                <svg style="width:14px;height:14px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Cliquez sur un KPI pour filtrer rapidement
            </div>
        </div>

        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Réf. bail</th>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Type</th>
                        <th>Début</th>
                        <th>Fin / Durée</th>
                        <th class="th-r">Loyer mensuel</th>
                        <th class="th-r">Caution</th>
                        <th style="text-align:center">Statut</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contrats as $contrat)
                    @php
                        $sl = match($contrat->statut) {
                            'actif'   => ['g',    'Actif'],
                            'resilié' => ['r',    'Résilié'],
                            'expiré'  => ['gray', 'Expiré'],
                            default   => ['gray', ucfirst($contrat->statut)],
                        };

                        // Calcul durée écoulée pour barre de progression
                        $debut   = \Carbon\Carbon::parse($contrat->date_debut);
                        $fin     = $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin) : null;
                        $today   = now();

                        $progress = null;
                        $joursRestants = null;
                        $expiryClass = '';

                        if ($fin && $contrat->statut === 'actif') {
                            $totalJours   = $debut->diffInDays($fin);
                            $ecoules      = $debut->diffInDays($today);
                            $progress     = $totalJours > 0 ? min(100, round(($ecoules / $totalJours) * 100)) : 0;
                            $joursRestants = $today->diffInDays($fin, false);

                            if ($joursRestants <= 30 && $joursRestants > 0) {
                                $expiryClass = 'expiry-soon';
                            } elseif ($joursRestants <= 0) {
                                $expiryClass = 'expiry-alert';
                            }
                        }

                        $progressColor = match(true) {
                            $progress >= 90 => '#dc2626',
                            $progress >= 70 => '#d97706',
                            default         => '#16a34a',
                        };
                    @endphp
                    <tr>
                        {{-- Réf. bail --}}
                        <td>
                            <span class="ref-bail">{{ $contrat->reference_bail ?? '#'.$contrat->id }}</span>
                        </td>

                        {{-- Bien --}}
                        <td>
                            <div class="bien-ref">{{ $contrat->bien?->reference ?? '—' }}</div>
                            <div class="bien-sub">{{ $contrat->bien?->ville ?? '' }}</div>
                        </td>

                        {{-- Locataire --}}
                        <td>
                            <div class="loc-name">{{ $contrat->locataire?->name ?? '—' }}</div>
                            <div class="loc-email">{{ $contrat->locataire?->email ?? '' }}</div>
                        </td>

                        {{-- Type bail --}}
                        <td>
                            <span class="bail-tag">
                                {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                            </span>
                        </td>

                        {{-- Date début --}}
                        <td>
                            <div class="date-val">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</div>
                            <div class="date-sub">{{ \Carbon\Carbon::parse($contrat->date_debut)->diffForHumans() }}</div>
                        </td>

                        {{-- Date fin + barre progression --}}
                        <td>
                            @if($contrat->date_fin)
                                <div class="date-val {{ $expiryClass }}">
                                    {{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}
                                </div>
                                @if($joursRestants !== null && $contrat->statut === 'actif')
                                    <div style="font-size:10px;margin-top:2px;{{ $joursRestants <= 30 ? 'color:#d97706' : 'color:#9ca3af' }}">
                                        @if($joursRestants > 0)
                                            {{ $joursRestants }}j restants
                                        @else
                                            Expiré
                                        @endif
                                    </div>
                                    @if($progress !== null)
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width:{{ $progress }}%;background:{{ $progressColor }}"></div>
                                        </div>
                                    @endif
                                @endif
                            @else
                                <div class="date-val">—</div>
                                <div class="date-sub">Indéterminée</div>
                            @endif
                        </td>

                        {{-- Loyer --}}
                        <td class="td-r">
                            <div class="loyer-val">{{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F</div>
                            <div class="loyer-sub">/ mois</div>
                        </td>

                        {{-- Caution --}}
                        <td class="td-r">
                            <div style="font-size:13px;color:#374151">{{ number_format($contrat->caution, 0, ',', ' ') }} F</div>
                        </td>

                        {{-- Statut --}}
                        <td class="td-c">
                            <span class="badge {{ $sl[0] }}">
                                <span class="bdot"></span>
                                {{ $sl[1] }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="td-c">
                            <div style="display:flex;align-items:center;justify-content:center;gap:5px">
                                {{-- Voir --}}
                                <a href="{{ route('admin.contrats.show', $contrat) }}" class="act-btn" title="Voir le contrat">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>

                                {{-- Enregistrer un paiement --}}
                                @if($contrat->statut === 'actif')
                                    <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}"
                                       class="act-btn primary" title="Enregistrer un paiement">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                    </a>
                                @endif

                                {{-- Modifier --}}
                                @can('update', $contrat)
                                    <a href="{{ route('admin.contrats.edit', $contrat) }}" class="act-btn" title="Modifier">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                @endcan

                                {{-- Supprimer --}}
                                @can('delete', $contrat)
                                    <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                                          onsubmit="return confirm('Supprimer ce contrat ? Cette action est irréversible.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="act-btn danger" title="Supprimer">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                </div>
                                <div class="empty-title">Aucun contrat trouvé</div>
                                <div class="empty-sub">
                                    @if(request()->hasAny(['q','statut','type_bail']))
                                        Aucun résultat pour ces filtres.
                                        <a href="{{ route('admin.contrats.index') }}" style="color:#c9a84c;font-weight:500">Effacer les filtres</a>
                                    @else
                                        Créez votre premier contrat de bail.
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($contrats->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Affichage de {{ $contrats->firstItem() }} à {{ $contrats->lastItem() }} sur {{ $contrats->total() }} contrats
            </div>
            <div class="pagination-links">
                @if($contrats->onFirstPage())
                    <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></span>
                @else
                    <a href="{{ $contrats->previousPageUrl() }}" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg></a>
                @endif

                @foreach($contrats->getUrlRange(max(1,$contrats->currentPage()-2), min($contrats->lastPage(),$contrats->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page === $contrats->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($contrats->hasMorePages())
                    <a href="{{ $contrats->nextPageUrl() }}" class="page-btn"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></a>
                @else
                    <span class="page-btn disabled"><svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg></span>
                @endif
            </div>
        </div>
        @endif

    </div>{{-- end table-card --}}

</div>

</x-app-layout>