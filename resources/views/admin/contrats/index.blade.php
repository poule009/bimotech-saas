@extends('layouts.app')
@section('title', 'Contrats')
@section('breadcrumb', 'Contrats')

@section('content')
<style>
.kpi-row { display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px; }
.kpi { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px; }
.kpi.gold  { border-top:3px solid #c9a84c; }
.kpi.green { border-top:3px solid #16a34a; }
.kpi.amber { border-top:3px solid #d97706; }
.kpi.red   { border-top:3px solid #dc2626; }
.kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#0d1117; }

.table-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:10px 16px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb; }
.dt td { padding:13px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }
.badge { display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
.badge-actif    { background:#dcfce7;color:#16a34a; }
.badge-resilié  { background:#fee2e2;color:#dc2626; }
.badge-expiré   { background:#f3f4f6;color:#6b7280; }
.badge-bail     { background:#f5e9c9;color:#8a6e2f; }
.act-btn { display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:7px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;text-decoration:none;transition:all .15s;cursor:pointer; }
.act-btn:hover { border-color:#c9a84c;color:#8a6e2f; }
.act-btn.danger:hover { border-color:#fca5a5;color:#dc2626;background:#fef2f2; }
.act-btn.primary { background:#2a4a7f;border-color:#2a4a7f;color:#fff; }
.act-btn.primary:hover { background:#1e3a6f; }
.empty-state { padding:56px;text-align:center; }
.empty-icon { width:52px;height:52px;border-radius:14px;background:#f5e9c9;display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }
</style>

<div style="padding:0 0 48px">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Contrats de bail</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">{{ $stats['total'] }} contrat(s) au total</p>
        </div>
        <a href="{{ route('admin.contrats.create') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none"
           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
            <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau contrat
        </a>
    </div>

    {{-- KPIs --}}
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Total</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Actifs</div>
            <div class="kpi-val" style="color:#16a34a">{{ $stats['actifs'] }}</div>
        </div>
        <div class="kpi amber">
            <div class="kpi-lbl">Résiliés</div>
            <div class="kpi-val" style="color:#d97706">{{ $stats['resilies'] }}</div>
        </div>
        <div class="kpi red">
            <div class="kpi-lbl">Expirés</div>
            <div class="kpi-val" style="color:#dc2626">{{ $stats['expires'] }}</div>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
        <input type="text" name="q" value="{{ request('q') }}"
               placeholder="Référence, locataire, bien…"
               style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;width:220px;outline:none"
               onfocus="this.style.borderColor='#c9a84c'" onblur="this.style.borderColor='#e5e7eb'">
        <select name="statut" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer">
            <option value="">Tous les statuts</option>
            @foreach(\App\Models\Contrat::STATUTS as $val => $label)
                <option value="{{ $val }}" @selected(request('statut') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="type_bail" onchange="this.form.submit()"
                style="padding:8px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;cursor:pointer">
            <option value="">Tous les types</option>
            @foreach(\App\Models\Contrat::TYPES_BAIL as $val => $label)
                <option value="{{ $val }}" @selected(request('type_bail') === $val)>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit"
                style="padding:8px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;background:#fff;cursor:pointer;font-family:'DM Sans',sans-serif">
            Rechercher
        </button>
        @if(request()->hasAny(['q','statut','type_bail']))
            <a href="{{ route('admin.contrats.index') }}"
               style="padding:8px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#6b7280;text-decoration:none;background:#fff">
                Effacer
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="table-card">
        @if($contrats->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg style="width:22px;height:22px;color:#8a6e2f" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:6px">Aucun contrat trouvé</div>
                <div style="font-size:13px;color:#6b7280;margin-bottom:16px">
                    @if(request()->hasAny(['q','statut','type_bail']))
                        Aucun résultat pour ces filtres.
                    @else
                        Créez votre premier contrat de bail.
                    @endif
                </div>
                <a href="{{ route('admin.contrats.create') }}"
                   style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none">
                    + Nouveau contrat
                </a>
            </div>
        @else
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Bien</th>
                        <th>Locataire</th>
                        <th>Type bail</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th style="text-align:right">Loyer</th>
                        <th style="text-align:center">Statut</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contrats as $contrat)
                    <tr>
                        <td>
                            <span style="font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af">
                                {{ $contrat->reference_bail_affichee }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:13px;color:#0d1117">{{ $contrat->bien?->reference ?? '—' }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $contrat->bien?->ville }}</div>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#0d1117">{{ $contrat->locataire?->name ?? '—' }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $contrat->locataire?->email }}</div>
                        </td>
                        <td>
                            <span class="badge badge-bail">
                                {{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? $contrat->type_bail }}
                            </span>
                        </td>
                        <td style="font-size:12px">{{ $contrat->date_debut?->format('d/m/Y') }}</td>
                        <td style="font-size:12px">
                            {{ $contrat->date_fin?->format('d/m/Y') ?? 'Ouvert' }}
                            @if($contrat->date_fin && $contrat->statut === 'actif')
                                @php $jr = now()->diffInDays($contrat->date_fin, false); @endphp
                                @if($jr <= 30 && $jr >= 0)
                                    <div style="font-size:10px;color:#d97706;font-weight:600">⚠ {{ $jr }}j restants</div>
                                @endif
                            @endif
                        </td>
                        <td style="text-align:right;font-family:'Syne',sans-serif;font-weight:600;color:#0d1117">
                            {{ number_format($contrat->loyer_contractuel, 0, ',', ' ') }} F
                        </td>
                        <td style="text-align:center">
                            <span class="badge badge-{{ $contrat->statut }}">
                                <span style="width:5px;height:5px;border-radius:50%;background:currentColor"></span>
                                {{ \App\Models\Contrat::STATUTS[$contrat->statut] ?? $contrat->statut }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px">
                                <a href="{{ route('admin.contrats.show', $contrat) }}" class="act-btn" title="Voir">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                @if($contrat->statut === 'actif')
                                <a href="{{ route('admin.paiements.create', ['contrat_id' => $contrat->id]) }}" class="act-btn primary" title="Enregistrer un paiement">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </a>
                                <a href="{{ route('admin.contrats.edit', $contrat) }}" class="act-btn" title="Modifier">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                                      onsubmit="return confirm('Résilier ce contrat ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="act-btn danger" title="Résilier">
                                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($contrats->hasPages())
        <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-top:1px solid #f3f4f6">
            <div style="font-size:12px;color:#6b7280">
                {{ $contrats->firstItem() }}–{{ $contrats->lastItem() }} sur {{ $contrats->total() }}
            </div>
            <div style="display:flex;gap:4px">
                @if(!$contrats->onFirstPage())
                    <a href="{{ $contrats->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif
                @foreach($contrats->getUrlRange(max(1,$contrats->currentPage()-2), min($contrats->lastPage(),$contrats->currentPage()+2)) as $page => $url)
                    <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid {{ $page===$contrats->currentPage() ? '#0d1117':'#e5e7eb' }};border-radius:7px;font-size:12px;color:{{ $page===$contrats->currentPage() ? '#fff':'#374151' }};background:{{ $page===$contrats->currentPage() ? '#0d1117':'#fff' }};text-decoration:none">
                        {{ $page }}
                    </a>
                @endforeach
                @if($contrats->hasMorePages())
                    <a href="{{ $contrats->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>

</div>
@endsection