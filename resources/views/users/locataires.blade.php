<x-app-layout>
    <x-slot name="header">Locataires</x-slot>

<style>
:root { --gold:#c9a84c; --gold-light:#f5e9c9; --gold-dark:#8a6e2f; --dark:#0d1117; --green:#16a34a; --red:#dc2626; --blue:#1d4ed8; }

.page { padding:24px 32px 48px; }

/* KPI */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:24px; }
.kpi { background:#fff; border:1px solid #e5e7eb; border-radius:14px; padding:18px 20px; position:relative; overflow:hidden; }
.kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:14px 14px 0 0; }
.kpi.gold::before  { background:var(--gold); }
.kpi.green::before { background:var(--green); }
.kpi.blue::before  { background:var(--blue); }
.kpi.red::before   { background:var(--red); }
.kpi-lbl { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#6b7280; margin-bottom:6px; }
.kpi-val { font-family:'Syne',sans-serif; font-size:28px; font-weight:800; color:#0d1117; }
.kpi-sub { font-size:11px; color:#9ca3af; margin-top:4px; }

/* Card */
.card { background:#fff; border:1px solid #e5e7eb; border-radius:16px; overflow:hidden; }
.card-hd { padding:16px 22px; border-bottom:1px solid #e5e7eb; display:flex; align-items:center; justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif; font-size:14px; font-weight:700; color:#0d1117; }

/* Table */
.dt { width:100%; border-collapse:collapse; }
.dt th { padding:10px 16px; font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.8px; color:#9ca3af; background:#f9fafb; border-bottom:1px solid #e5e7eb; text-align:left; white-space:nowrap; }
.dt td { padding:13px 16px; border-bottom:1px solid #f3f4f6; vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#fafafa; }

/* Avatar */
.av { width:38px; height:38px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-family:'Syne',sans-serif; font-size:14px; font-weight:800; color:#fff; flex-shrink:0; }
.av-blue { background:linear-gradient(135deg,#3b82f6,#1d4ed8); }
.av-purple { background:linear-gradient(135deg,#8b5cf6,#6d28d9); }
.av-teal { background:linear-gradient(135deg,#14b8a6,#0d9488); }

/* Badges */
.badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:99px; font-size:10px; font-weight:700; }
.badge-green { background:#dcfce7; color:var(--green); }
.badge-gray  { background:#f3f4f6; color:#6b7280; }
.badge-red   { background:#fee2e2; color:var(--red); }
.badge-blue  { background:#dbeafe; color:var(--blue); }

/* Contrat mini */
.contrat-mini { background:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:7px 10px; }
.contrat-mini-ref { font-size:11px; font-weight:700; color:#0d1117; }
.contrat-mini-addr { font-size:10px; color:#9ca3af; margin-top:2px; }
.contrat-mini-loyer { font-size:11px; font-weight:700; color:var(--green); margin-top:3px; }

/* Boutons */
.btn-add { display:inline-flex; align-items:center; gap:7px; padding:9px 18px; background:var(--dark); color:#fff; border:none; border-radius:10px; font-size:13px; font-weight:600; font-family:'DM Sans',sans-serif; text-decoration:none; cursor:pointer; transition:opacity .15s; }
.btn-add:hover { opacity:.88; }
.act { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border:1px solid #e5e7eb; border-radius:8px; text-decoration:none; color:#6b7280; transition:all .15s; }
.act:hover { border-color:var(--gold); color:var(--gold-dark); background:var(--gold-light); }
.act.wa:hover { border-color:#25D366; color:#25D366; background:#f0fdf4; }
.act svg { width:13px; height:13px; }

/* Filtre tabs */
.filter-tabs { display:flex; gap:4px; }
.tab { padding:6px 14px; border-radius:8px; font-size:12px; font-weight:500; cursor:pointer; border:1px solid #e5e7eb; background:#fff; color:#6b7280; transition:all .15s; }
.tab.active { background:var(--dark); color:#fff; border-color:var(--dark); }

/* Search */
.search-input { padding:8px 12px 8px 36px; border:1px solid #e5e7eb; border-radius:9px; font-size:13px; font-family:'DM Sans',sans-serif; color:#0d1117; outline:none; transition:border-color .15s; background:#f9fafb url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 10px center / 15px; }
.search-input:focus { border-color:var(--gold); background-color:#fff; }

/* Couleurs avatar selon index */
.av-colors { }

/* Vide */
.empty { padding:64px; text-align:center; }
</style>

<div class="page">

    {{-- HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:24px;font-weight:800;color:#0d1117;letter-spacing:-.4px">Locataires</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">{{ $stats['total'] }} locataire(s) enregistré(s)</p>
        </div>
        <a href="{{ route('admin.users.create', 'locataire') }}" class="btn-add">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Nouveau locataire
        </a>
    </div>

    {{-- KPI --}}
    <div class="kpi-row">
        <div class="kpi gold">
            <div class="kpi-lbl">Total locataires</div>
            <div class="kpi-val">{{ $stats['total'] }}</div>
            <div class="kpi-sub">Enregistrés dans l'agence</div>
        </div>
        <div class="kpi green">
            <div class="kpi-lbl">Avec contrat actif</div>
            <div class="kpi-val">{{ $stats['actifs'] }}</div>
            <div class="kpi-sub">Loyer en cours</div>
        </div>
        <div class="kpi blue">
            <div class="kpi-lbl">Taux d'occupation</div>
            <div class="kpi-val">{{ $stats['total'] > 0 ? round($stats['actifs']/$stats['total']*100) : 0 }}%</div>
            <div class="kpi-sub">Locataires actifs</div>
        </div>
        <div class="kpi red">
            <div class="kpi-lbl">Sans contrat</div>
            <div class="kpi-val">{{ $stats['sans_contrat'] }}</div>
            <div class="kpi-sub">À reloger ou archiver</div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-hd">
            <div style="display:flex;align-items:center;gap:12px">
                <div class="card-title">Liste des locataires</div>
                <div class="filter-tabs">
                    <button class="tab active" onclick="filterStatus('all', this)">Tous</button>
                    <button class="tab" onclick="filterStatus('actif', this)">Avec contrat</button>
                    <button class="tab" onclick="filterStatus('sans', this)">Sans contrat</button>
                </div>
            </div>
            <input type="text" class="search-input" placeholder="Rechercher..." id="search-loc"
                   oninput="searchTable(this.value)" style="width:200px">
        </div>

        <div style="overflow-x:auto">
            <table class="dt" id="dt-loc">
                <thead>
                    <tr>
                        <th>Locataire</th>
                        <th>Contact</th>
                        <th>Situation</th>
                        <th>Contrat actif</th>
                        <th style="text-align:center">Statut</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locataires as $idx => $user)
                    @php
                        $contratActif = $user->contrats->first();
                        $aContrat = (bool) $contratActif;
                        $avClasses = ['av-blue','av-purple','av-teal'];
                        $avClass = $avClasses[$idx % 3];
                        $profil = $user->locataire ?? null;
                        $telRaw    = preg_replace('/[\s\-\(\)]/', '', $user->telephone ?? '');
                        $telDigits = preg_replace('/[^0-9+]/', '', $telRaw);
                        // Ajouter indicatif Sénégal +221 si absent
                        if ($telDigits && !str_starts_with($telDigits, '+') && !str_starts_with($telDigits, '221')) {
                            $telDigits = '221' . ltrim($telDigits, '0');
                        }
                        $telDigits = ltrim($telDigits, '+');
                        $waMsg = urlencode("Bonjour {$user->name}, nous vous contactons concernant votre location. Cordialement, votre agence.");
                        $waLink = $telDigits ? "https://wa.me/{$telDigits}?text={$waMsg}" : null;
                        $estEntreprise = (bool) ($profil?->est_entreprise ?? false);
                    @endphp
                    <tr data-status="{{ $aContrat ? 'actif' : 'sans' }}">
                        <td>
                            <div style="display:flex;align-items:center;gap:12px">
                                <div class="av {{ $avClass }}">{{ strtoupper(substr($user->name,0,2)) }}</div>
                                <div>
                                    <div style="font-size:13px;font-weight:700;color:#0d1117">{{ $user->name }}</div>
                                    <div style="display:flex;align-items:center;gap:6px;margin-top:3px">
                                        @if($estEntreprise)
                                            <span class="badge badge-blue" style="font-size:9px">🏢 Entreprise</span>
                                        @endif
                                        <span style="font-size:10px;color:#9ca3af">Depuis {{ $user->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size:12px;color:#374151">{{ $user->email }}</div>
                            @if($user->telephone)
                            <div style="font-size:11px;color:#6b7280;margin-top:2px">{{ $user->telephone }}</div>
                            @endif
                        </td>
                        <td>
                            @if($profil?->profession)
                                <div style="font-size:12px;font-weight:500;color:#374151">{{ $profil->profession }}</div>
                            @endif
                            @if($profil?->employeur)
                                <div style="font-size:11px;color:#6b7280">{{ $profil->employeur }}</div>
                            @endif
                            @if(!$profil?->profession && !$profil?->employeur)
                                <span style="font-size:12px;color:#9ca3af">—</span>
                            @endif
                        </td>
                        <td>
                            @if($contratActif)
                            <div class="contrat-mini">
                                <div class="contrat-mini-ref">{{ $contratActif->bien->reference ?? '—' }}</div>
                                <div class="contrat-mini-addr">{{ $contratActif->bien->adresse ?? '' }}, {{ $contratActif->bien->ville ?? '' }}</div>
                                <div class="contrat-mini-loyer">{{ number_format($contratActif->loyer_contractuel, 0, ',', ' ') }} F/mois</div>
                            </div>
                            @else
                                <span style="font-size:12px;color:#9ca3af">Pas de contrat actif</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            @if($aContrat)
                                <span class="badge badge-green">● Actif</span>
                            @else
                                <span class="badge badge-gray">Sans contrat</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:5px">
                                <a href="{{ route('admin.users.show', $user) }}" class="act" title="Voir la fiche">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="act" title="Modifier">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                @if($waLink)
                                <a href="{{ $waLink }}" target="_blank" class="act wa" title="WhatsApp">
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width:14px;height:14px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </a>
                                @endif
                                @if($contratActif)
                                <a href="{{ route('admin.paiements.create') }}?contrat_id={{ $contratActif->id }}" class="act" title="Enregistrer paiement"
                                   style="border-color:#16a34a;color:#16a34a;background:#f0fdf4">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty">
                                <div style="font-size:48px;margin-bottom:12px">👤</div>
                                <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#0d1117;margin-bottom:6px">Aucun locataire enregistré</div>
                                <div style="font-size:13px;color:#6b7280;margin-bottom:20px">Ajoutez votre premier locataire pour commencer à gérer les locations.</div>
                                <a href="{{ route('admin.users.create', 'locataire') }}" class="btn-add" style="display:inline-flex">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    Ajouter un locataire
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locataires->hasPages())
        <div style="padding:14px 22px;border-top:1px solid #f3f4f6">{{ $locataires->links() }}</div>
        @endif
    </div>

</div>

<script>
// Filtre par statut
function filterStatus(status, btn) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#dt-loc tbody tr[data-status]').forEach(tr => {
        if (status === 'all') { tr.style.display = ''; return; }
        tr.style.display = tr.dataset.status === status ? '' : 'none';
    });
}

// Recherche texte
function searchTable(q) {
    q = q.toLowerCase();
    document.querySelectorAll('#dt-loc tbody tr[data-status]').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

</x-app-layout>