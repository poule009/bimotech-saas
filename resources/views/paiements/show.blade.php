@extends('layouts.app')
@section('title', 'Paiement — ' . $paiement->reference_paiement)
@section('breadcrumb', 'Paiements › Détail')

@section('content')
<style>
.page-grid { display:grid;grid-template-columns:1fr 280px;gap:24px;align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px; }
.card-icon { width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.card-icon svg { width:15px;height:15px; }
.card-icon.gold  { background:#f5e9c9;color:#8a6e2f; }
.card-icon.green { background:#dcfce7;color:#16a34a; }
.card-icon.blue  { background:#dbeafe;color:#1d4ed8; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.info-grid { display:grid;grid-template-columns:1fr 1fr;gap:14px; }
.info-grid-3 { display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px; }
.il { font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:4px; }
.iv { font-size:13px;font-weight:500;color:#0d1117; }

.actions-bar { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px; }
.btn-act { display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:9px;font-size:12px;font-weight:500;font-family:'DM Sans',sans-serif;cursor:pointer;text-decoration:none;transition:all .15s;border:none; }
.btn-dark    { background:#0d1117;color:#fff; }
.btn-dark:hover { opacity:.85; }
.btn-outline { background:#fff;color:#374151;border:1px solid #e5e7eb; }
.btn-outline:hover { border-color:#c9a84c;color:#8a6e2f; }
.btn-red  { background:#fee2e2;color:#dc2626;border:1px solid #fecaca; }
.btn-act svg { width:14px;height:14px; }

/* Décompte fiscal */
.fiscal-card { background:#0d1117;border-radius:14px;overflow:hidden; }
.fiscal-hd { padding:12px 16px;border-bottom:1px solid rgba(255,255,255,.07); }
.fiscal-title { font-family:'Syne',sans-serif;font-size:12px;font-weight:700;color:#fff; }
.fiscal-body { padding:14px 16px; }
.fp-row { display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.fp-row:last-child { border-bottom:none; }
.fp-lbl { color:rgba(255,255,255,.4); }
.fp-val { color:#e6edf3;font-weight:500;font-family:'Syne',sans-serif; }
.fp-val.gold  { color:#c9a84c; }
.fp-val.green { color:#4ade80; }
.fp-sep { height:1px;background:rgba(255,255,255,.07);margin:8px 0; }
.fp-total { background:rgba(201,168,76,.1);border:1px solid rgba(201,168,76,.2);border-radius:8px;padding:10px 12px;margin-top:10px; }
.fp-total-lbl { font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:3px; }
.fp-total-val { font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#c9a84c; }

/* Hero */
.hero { background:linear-gradient(135deg,#0d1117,#1c2333);border-radius:14px;padding:20px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px; }

/* Responsive dépenses */
@media(max-width:640px){
    .page-grid { grid-template-columns:1fr; }
    .dep-form-grid { grid-template-columns:1fr !important; }
    .info-grid { grid-template-columns:1fr; }
    .info-grid-3 { grid-template-columns:1fr 1fr; }
}
@media(max-width:400px){.info-grid-3{grid-template-columns:1fr}}
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('admin.paiements.index') }}" style="color:#6b7280;text-decoration:none">Paiements</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">{{ $paiement->reference_paiement }}</span>
    </div>

    {{-- Actions --}}
    <div class="actions-bar">
        <a href="{{ route('admin.paiements.pdf', $paiement) }}" target="_blank" class="btn-act btn-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Télécharger quittance
        </a>
        <a href="{{ route('admin.contrats.show', $paiement->contrat) }}" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Voir le contrat
        </a>
        <a href="{{ route('admin.paiements.index') }}" class="btn-act btn-outline">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Retour
        </a>
        @if($paiement->statut === 'valide')
        <form method="POST" action="{{ route('admin.paiements.annuler', $paiement) }}"
              data-confirm="Le paiement {{ $paiement->reference_paiement }} sera annulé. Cette action est irréversible."
              data-confirm-title="Annuler ce paiement ?"
              data-confirm-ok="Oui, annuler"
              data-confirm-color="#d97706"
              data-confirm-icon-bg="#fef3c7">
            @csrf @method('PATCH')
            <button type="submit" class="btn-act btn-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                Annuler
            </button>
        </form>
        @endif
    </div>

    {{-- Hero --}}
    <div class="hero">
        <div>
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.3);margin-bottom:6px">
                {{ $paiement->reference_paiement }}
            </div>
            <div style="font-family:'Syne',sans-serif;font-size:20px;font-weight:700;color:#fff;margin-bottom:4px">
                {{ $paiement->contrat?->bien?->reference }} — {{ $paiement->contrat?->locataire?->name }}
            </div>
            <div style="font-size:13px;color:rgba(255,255,255,.5)">
                Période : {{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}
                · Payé le {{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : '—' }}
            </div>
            <div style="margin-top:10px">
                @php
                    $bs = match($paiement->statut) {
                        'valide' => 'background:rgba(74,222,128,.15);color:#4ade80',
                        'annule' => 'background:rgba(248,113,113,.15);color:#f87171',
                        default  => 'background:rgba(255,255,255,.1);color:#9ca3af',
                    };
                @endphp
                <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 12px;border-radius:99px;font-size:11px;font-weight:600;{{ $bs }}">
                    <span style="width:5px;height:5px;border-radius:50%;background:currentColor"></span>
                    {{ ucfirst($paiement->statut) }}
                </span>
                <span style="margin-left:8px;font-size:12px;color:rgba(255,255,255,.4)">
                    {{ \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$paiement->mode_paiement] ?? $paiement->mode_paiement }}
                </span>
            </div>
        </div>
        <div style="text-align:right">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:4px">Montant encaissé</div>
            <div style="font-family:'Syne',sans-serif;font-size:30px;font-weight:700;color:#c9a84c">
                {{ number_format($paiement->montant_encaisse, 0, ',', ' ') }}<span style="font-size:14px;color:rgba(201,168,76,.5);margin-left:4px">FCFA</span>
            </div>
        </div>
    </div>

    <div class="page-grid">

        {{-- COLONNE GAUCHE --}}
        <div>

            {{-- PARTIES --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-icon gold">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div class="card-title">Parties</div>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div>
                            <div class="il">Locataire</div>
                            <div class="iv">{{ $paiement->contrat?->locataire?->name ?? '—' }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $paiement->contrat?->locataire?->email }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $paiement->contrat?->locataire?->telephone ?? '' }}</div>
                        </div>
                        <div>
                            <div class="il">Propriétaire</div>
                            <div class="iv">{{ $paiement->contrat?->bien?->proprietaire?->name ?? '—' }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $paiement->contrat?->bien?->proprietaire?->email }}</div>
                            <div style="font-size:11px;color:#6b7280">{{ $paiement->contrat?->bien?->proprietaire?->telephone ?? '' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if(config('features.fiscalite'))
            {{-- DÉCOMPTE FISCAL COMPLET --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-icon gold">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div class="card-title">Décompte fiscal complet</div>
                </div>
                <div style="padding:0">
                    <x-fiscal.decompte-paiement :paiement="$paiement" />
                </div>
            </div>
            @endif

            {{-- NOTES --}}
            @if($paiement->notes)
            <div class="card">
                <div class="card-hd">
                    <div class="card-icon blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/></svg>
                    </div>
                    <div class="card-title">Notes</div>
                </div>
                <div class="card-body">
                    <p style="font-size:13px;color:#374151">{{ $paiement->notes }}</p>
                </div>
            </div>
            @endif

            {{-- ── DÉPENSES DE GESTION ─────────────────────────────────── --}}
            @php
                $depenses      = $paiement->depenses ?? collect();
                $totalDep      = (float) $depenses->sum('montant');
                $netBailleur   = (float) ($paiement->montant_net_bailleur ?? $paiement->net_a_verser_proprietaire ?? 0);
                $netFinalAff   = round($netBailleur - $totalDep, 2);
                $canEditDep    = auth()->user()?->role === 'admin' || auth()->user()?->role === 'superadmin';
            @endphp
            <div class="card" id="card-depenses">
                <div class="card-hd" style="justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="card-icon" style="background:#fef2f2;color:#dc2626">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px"><path d="M9 14l2-2 4 4"/><path d="M3 6h18M3 12h9m-9 6h5"/></svg>
                        </div>
                        <div class="card-title">Dépenses pour le bailleur</div>
                        @if($totalDep > 0)
                        <span style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;border-radius:99px;font-size:11px;font-weight:700;padding:2px 9px">
                            − {{ number_format($totalDep, 0, ',', ' ') }} F
                        </span>
                        @endif
                    </div>
                    @if($canEditDep)
                    <button onclick="toggleDepForm()" id="btn-dep-toggle"
                        style="display:flex;align-items:center;gap:5px;padding:6px 12px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#374151;font-size:12px;font-weight:500;cursor:pointer">
                        <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Ajouter
                    </button>
                    @endif
                </div>

                {{-- Liste des dépenses existantes --}}
                <div class="card-body" style="padding-top:12px">

                    @if($depenses->isEmpty())
                    <div id="dep-empty" style="text-align:center;padding:20px 0;color:#9ca3af;font-size:13px">
                        <svg style="width:28px;height:28px;margin:0 auto 8px;display:block;color:#d1d5db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M9 14l2-2 4 4"/><path d="M3 6h18M3 12h9m-9 6h5"/></svg>
                        Aucune dépense enregistrée pour ce mois
                    </div>
                    @else
                    <div id="dep-empty" style="display:none"></div>
                    @endif

                    <div id="dep-list">
                    @foreach($depenses as $dep)
                    <div class="dep-row" style="display:flex;align-items:flex-start;gap:10px;padding:10px 0;border-bottom:1px solid #f3f4f6" id="dep-{{ $dep->id }}">
                        {{-- Icône catégorie --}}
                        <div style="width:32px;height:32px;border-radius:8px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg style="width:14px;height:14px;color:#dc2626" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 14l2-2 4 4"/><path d="M3 6h18M3 12h9m-9 6h5"/></svg>
                        </div>
                        {{-- Infos --}}
                        <div style="flex:1;min-width:0">
                            <div style="font-size:13px;font-weight:600;color:#0d1117">{{ $dep->libelle }}</div>
                            <div style="font-size:11px;color:#9ca3af;margin-top:2px;display:flex;gap:8px;flex-wrap:wrap">
                                <span>{{ $dep->categorie_libelle }}</span>
                                <span>·</span>
                                <span>{{ \Carbon\Carbon::parse($dep->date_depense)->format('d/m/Y') }}</span>
                                @if($dep->prestataire)
                                <span>· {{ $dep->prestataire }}</span>
                                @endif
                            </div>
                            @if($dep->notes)
                            <div style="font-size:11px;color:#6b7280;margin-top:3px;font-style:italic">{{ $dep->notes }}</div>
                            @endif
                        </div>
                        {{-- Montant + supprimer --}}
                        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0">
                            <div style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#dc2626">
                                {{ number_format($dep->montant, 0, ',', ' ') }} F
                            </div>
                            @if($canEditDep)
                            <form method="POST"
                                  action="{{ route('admin.paiements.depenses.destroy', [$paiement, $dep]) }}"
                                  data-confirm="Supprimer « {{ $dep->libelle }} » ({{ number_format($dep->montant, 0, ',', ' ') }} F) ?"
                                  data-confirm-title="Supprimer cette dépense ?"
                                  data-confirm-ok="Oui, supprimer"
                                  data-confirm-color="#dc2626"
                                  data-confirm-icon-bg="#fef2f2">
                                @csrf @method('DELETE')
                                <button type="submit" title="Supprimer"
                                    style="border:none;background:none;cursor:pointer;color:#9ca3af;padding:4px;border-radius:6px;display:flex;align-items:center"
                                    onmouseover="this.style.color='#dc2626';this.style.background='#fef2f2'"
                                    onmouseout="this.style.color='#9ca3af';this.style.background='none'">
                                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                    </div>

                    {{-- Total si plusieurs dépenses --}}
                    @if($depenses->count() > 1)
                    <div style="display:flex;justify-content:space-between;padding:10px 0 4px;font-size:12px;color:#6b7280">
                        <span>Total dépenses ({{ $depenses->count() }})</span>
                        <span style="font-weight:700;color:#dc2626">{{ number_format($totalDep, 0, ',', ' ') }} F</span>
                    </div>
                    @endif

                    {{-- Net final bailleur mis à jour --}}
                    @if($totalDep > 0)
                    <div style="margin-top:12px;padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:9px;display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#16a34a;margin-bottom:2px">Net à reverser au bailleur</div>
                            <div style="font-size:10px;color:#6b7280">Après déduction des dépenses</div>
                        </div>
                        <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#16a34a">
                            {{ number_format($netFinalAff, 0, ',', ' ') }} F
                        </div>
                    </div>
                    @endif

                    {{-- Formulaire ajout (masqué par défaut) --}}
                    @if($canEditDep)
                    <div id="dep-form-wrap" style="display:none;margin-top:16px;padding:16px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:10px">
                        <div style="font-size:12px;font-weight:700;color:#374151;margin-bottom:12px;text-transform:uppercase;letter-spacing:.6px">Nouvelle dépense</div>

                        @if($errors->any() && old('_dep_form_open'))
                        <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 12px;margin-bottom:12px;font-size:12px;color:#dc2626">
                            <ul style="margin:0;padding-left:14px">
                                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('admin.paiements.depenses.store', $paiement) }}" id="dep-form">
                            @csrf
                            <input type="hidden" name="_dep_form_open" value="1">

                            {{-- Libellé --}}
                            <div style="margin-bottom:12px">
                                <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:4px">
                                    Libellé <span style="color:#dc2626">*</span>
                                </label>
                                <input type="text" name="libelle" value="{{ old('libelle') }}" required
                                    placeholder="Ex : Facture plombier Moussa"
                                    style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;box-sizing:border-box"
                                    onfocus="this.style.borderColor='var(--ac,#c9a84c)'"
                                    onblur="this.style.borderColor='#e5e7eb'">
                            </div>

                            {{-- Montant + Catégorie --}}
                            <div class="dep-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:4px">
                                        Montant (FCFA) <span style="color:#dc2626">*</span>
                                    </label>
                                    <input type="number" name="montant" value="{{ old('montant') }}" required min="1" step="1"
                                        placeholder="25 000"
                                        style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--ac,#c9a84c)'"
                                        onblur="this.style.borderColor='#e5e7eb'">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:4px">
                                        Catégorie <span style="color:#dc2626">*</span>
                                    </label>
                                    <select name="categorie" required
                                        style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;box-sizing:border-box;appearance:none"
                                        onfocus="this.style.borderColor='var(--ac,#c9a84c)'"
                                        onblur="this.style.borderColor='#e5e7eb'">
                                        <option value="">Choisir…</option>
                                        @foreach(\App\Models\DepenseGestion::CATEGORIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('categorie') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Date + Prestataire --}}
                            <div class="dep-form-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px">
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:4px">
                                        Date de la dépense <span style="color:#dc2626">*</span>
                                    </label>
                                    <input type="date" name="date_depense" value="{{ old('date_depense', now()->format('Y-m-d')) }}" required
                                        style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--ac,#c9a84c)'"
                                        onblur="this.style.borderColor='#e5e7eb'">
                                </div>
                                <div>
                                    <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:4px">
                                        Prestataire
                                    </label>
                                    <input type="text" name="prestataire" value="{{ old('prestataire') }}"
                                        placeholder="Ex : Moussa Diallo"
                                        style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;box-sizing:border-box"
                                        onfocus="this.style.borderColor='var(--ac,#c9a84c)'"
                                        onblur="this.style.borderColor='#e5e7eb'">
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div style="margin-bottom:14px">
                                <label style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.6px;display:block;margin-bottom:4px">
                                    Notes (optionnel)
                                </label>
                                <textarea name="notes" rows="2" placeholder="Détails supplémentaires…"
                                    style="width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;font-family:'DM Sans',sans-serif;background:#fff;box-sizing:border-box;resize:vertical"
                                    onfocus="this.style.borderColor='var(--ac,#c9a84c)'"
                                    onblur="this.style.borderColor='#e5e7eb'">{{ old('notes') }}</textarea>
                            </div>

                            {{-- Boutons --}}
                            <div style="display:flex;gap:8px;justify-content:flex-end">
                                <button type="button" onclick="toggleDepForm()"
                                    style="padding:8px 16px;border-radius:8px;border:1px solid #e5e7eb;background:#fff;color:#6b7280;font-size:12px;font-weight:500;cursor:pointer">
                                    Annuler
                                </button>
                                <button type="submit"
                                    style="padding:8px 18px;border-radius:8px;border:none;background:var(--ac,#c9a84c);color:#fff;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px">
                                    <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Enregistrer la dépense
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif

                </div>
            </div>
            {{-- ── FIN DÉPENSES ────────────────────────────────────────── --}}

        </div>

        {{-- COLONNE DROITE --}}
        <div>
            <div class="fiscal-card">
                <div class="fiscal-hd"><div class="fiscal-title">Résumé fiscal</div></div>
                <div class="fiscal-body">
                    <div class="fp-row">
                        <span class="fp-lbl">Référence</span>
                        <span class="fp-val" style="font-size:10px">{{ $paiement->reference_paiement }}</span>
                    </div>
                    <div class="fp-row">
                        <span class="fp-lbl">Référence bail</span>
                        <span class="fp-val" style="font-size:10px">{{ $paiement->reference_bail ?? '—' }}</span>
                    </div>
                    <div class="fp-row">
                        <span class="fp-lbl">Période</span>
                        <span class="fp-val">{{ \Carbon\Carbon::parse($paiement->periode)->translatedFormat('F Y') }}</span>
                    </div>
                    <div class="fp-row">
                        <span class="fp-lbl">Date paiement</span>
                        <span class="fp-val">{{ $paiement->date_paiement ? \Carbon\Carbon::parse($paiement->date_paiement)->format('d/m/Y') : '—' }}</span>
                    </div>
                    <div class="fp-row">
                        <span class="fp-lbl">Mode</span>
                        <span class="fp-val">{{ \App\Http\Controllers\PaiementController::MODES_PAIEMENT[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</span>
                    </div>
                    <div class="fp-sep"></div>
                    <div class="fp-row">
                        <span class="fp-lbl">Loyer encaissé</span>
                        <span class="fp-val gold">{{ number_format($paiement->montant_encaisse, 0, ',', ' ') }} F</span>
                    </div>
                    @if(($paiement->frais_agence_ttc ?? 0) > 0)
                    <div class="fp-row">
                        <span class="fp-lbl">Honoraires TTC</span>
                        <span class="fp-val" style="color:#60a5fa">{{ number_format($paiement->frais_agence_ttc, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    @if(($paiement->caution_montant ?? 0) > 0)
                    <div class="fp-row">
                        <span class="fp-lbl">Caution</span>
                        <span class="fp-val" style="color:#a78bfa">{{ number_format($paiement->caution_montant, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    @if(($paiement->total_encaissement_initial ?? 0) > $paiement->montant_encaisse)
                    <div class="fp-row" style="border-top:1px solid rgba(201,168,76,.3)">
                        <span class="fp-lbl" style="color:rgba(201,168,76,.7)">Total facturé</span>
                        <span class="fp-val gold" style="font-size:14px">{{ number_format($paiement->total_encaissement_initial, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    @if(config('features.fiscalite') && ($paiement->brs_amount ?? 0) > 0)
                    <div class="fp-row">
                        <span class="fp-lbl" style="color:rgba(248,113,113,.7)">BRS retenu</span>
                        <span class="fp-val" style="color:#f87171">- {{ number_format($paiement->brs_amount, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    <div class="fp-sep"></div>
                    {{-- NET LOCATAIRE — en gras, ligne principale --}}
                    <div style="background:rgba(201,168,76,.12);border:1px solid rgba(201,168,76,.25);border-radius:8px;padding:10px 12px;margin-bottom:10px">
                        <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(201,168,76,.6);margin-bottom:3px">Net à payer — Locataire</div>
                        <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#c9a84c">
                            {{ number_format($paiement->montant_net_locataire ?? ($paiement->total_encaissement_initial ?? $paiement->montant_encaisse), 0, ',', ' ') }} F
                        </div>
                    </div>
                    {{-- NET BAILLEUR --}}
                    @php $netBrut = (float)($paiement->montant_net_bailleur ?? $paiement->net_a_verser_proprietaire ?? 0); @endphp
                    @if($totalDep > 0)
                    <div style="margin-bottom:6px;padding:8px 10px;background:rgba(220,38,38,.08);border:1px solid rgba(220,38,38,.2);border-radius:7px;display:flex;justify-content:space-between;align-items:center">
                        <span style="font-size:10px;color:rgba(220,38,38,.7)">Dépenses déduites</span>
                        <span style="font-size:12px;font-weight:700;color:#dc2626">− {{ number_format($totalDep, 0, ',', ' ') }} F</span>
                    </div>
                    @endif
                    <div class="fp-total" style="{{ $totalDep > 0 ? 'border-color:rgba(22,163,74,.4);background:rgba(22,163,74,.08)' : '' }}">
                        <div class="fp-total-lbl" style="{{ $totalDep > 0 ? 'color:rgba(22,163,74,.7)' : '' }}">
                            Net à reverser — Bailleur{{ $totalDep > 0 ? ' (après dépenses)' : '' }}
                        </div>
                        <div class="fp-total-val" style="{{ $totalDep > 0 ? 'color:#16a34a' : '' }}">
                            {{ number_format($totalDep > 0 ? $netFinalAff : $netBrut, 0, ',', ' ') }} F
                        </div>
                        @if($totalDep > 0)
                        <div style="font-size:10px;color:rgba(255,255,255,.3);margin-top:3px">
                            Brut : {{ number_format($netBrut, 0, ',', ' ') }} F
                        </div>
                        @endif
                    </div>
                    @if($paiement->caution_percue > 0)
                    <div style="margin-top:10px;padding:8px 10px;background:rgba(29,78,216,.1);border:1px solid rgba(29,78,216,.2);border-radius:7px">
                        <div style="font-size:10px;color:rgba(29,78,216,.6);font-weight:700;text-transform:uppercase;letter-spacing:.8px;margin-bottom:3px">Caution perçue (saisie manuelle)</div>
                        <div style="font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#1d4ed8">
                            {{ number_format($paiement->caution_percue, 0, ',', ' ') }} F
                        </div>
                    </div>
                    @endif
                    <div style="margin-top:10px;text-align:center">
                        <a href="{{ route('admin.paiements.pdf', $paiement) }}" target="_blank"
                           style="display:flex;align-items:center;justify-content:center;gap:6px;padding:9px;border:1px solid rgba(255,255,255,.1);border-radius:8px;color:#c9a84c;font-size:12px;font-weight:600;text-decoration:none">
                            <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            Télécharger la quittance PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function toggleDepForm() {
    var wrap = document.getElementById('dep-form-wrap');
    var btn  = document.getElementById('btn-dep-toggle');
    if (!wrap) return;
    var open = wrap.style.display === 'none' || wrap.style.display === '';
    wrap.style.display = open ? 'block' : 'none';
    if (btn) btn.innerHTML = open
        ? '<svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg> Fermer'
        : '<svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Ajouter';
    if (open) {
        setTimeout(function() {
            wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 60);
    }
}

// Rouvrir le formulaire automatiquement après une erreur de validation
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    var wrap = document.getElementById('dep-form-wrap');
    var btn  = document.getElementById('btn-dep-toggle');
    if (wrap) {
        wrap.style.display = 'block';
        if (btn) btn.innerHTML = '<svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/></svg> Fermer';
        wrap.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
});
@endif
</script>
@endpush
@endsection