@extends('layouts.app')
@section('title', 'Mes paiements')
@section('breadcrumb', 'Mes paiements')

@section('content')
@php
$modes = [
    'especes'      => 'Espèces',
    'virement'     => 'Virement',
    'cheque'       => 'Chèque',
    'wave'         => 'Wave',
    'orange_money' => 'Orange Money',
    'free_money'   => 'Free Money',
    'e_money'      => 'E-Money',
];
@endphp

<style>
.page-title { font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px; }
.page-sub   { font-size:13px;color:#6b7280;margin-top:3px; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden; }
.card-hd { padding:15px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.badge-count { background:#f3f4f6;color:#374151;font-size:11px;font-weight:600;padding:2px 8px;border-radius:6px; }
.dt { width:100%;border-collapse:collapse; }
.dt th { padding:9px 16px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;background:#f9fafb;border-bottom:1px solid #e5e7eb;text-align:left; }
.dt td { padding:13px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;vertical-align:middle; }
.dt tbody tr:last-child td { border-bottom:none; }
.dt tbody tr:hover { background:#f9fafb; }
.periode-pill { display:inline-block;padding:3px 9px;background:#f5e9c9;color:#8a6e2f;border-radius:6px;font-size:11px;font-weight:600;font-family:'Syne',sans-serif; }
.amt { font-family:'Syne',sans-serif;font-weight:700;color:#0d1117; }
.mode-badge { display:inline-flex;align-items:center;gap:4px;padding:3px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:11px;color:#374151; }
.empty-state { padding:60px 20px;text-align:center; }
.empty-icon { width:48px;height:48px;border-radius:12px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 14px; }
.empty-title { font-family:'Syne',sans-serif;font-size:15px;font-weight:700;color:#0d1117;margin-bottom:6px; }
.empty-sub { font-size:13px;color:#9ca3af; }
.pagination { display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280; }
.pag-links { display:flex;gap:4px; }
.pag-link { display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:7px;border:1px solid #e5e7eb;font-size:12px;color:#374151;text-decoration:none;transition:all .15s; }
.pag-link:hover { border-color:#c9a84c;color:#8a6e2f; }
.pag-link.active { background:#c9a84c;border-color:#c9a84c;color:#0d1117;font-weight:700; }
.pag-link.disabled { opacity:.4;pointer-events:none; }
</style>

<div style="padding:0 0 48px">

    {{-- En-tête --}}
    <div style="margin-bottom:24px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:12px">
            <a href="{{ route('locataire.dashboard') }}" style="color:#6b7280;text-decoration:none">Mon espace</a>
            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            <span style="color:#0d1117;font-weight:500">Mes paiements</span>
        </div>
        <h1 class="page-title">Historique des paiements</h1>
        <p class="page-sub">Tous vos loyers validés, avec téléchargement de quittance.</p>
    </div>

    <div class="card">
        <div class="card-hd">
            <div class="card-title">Paiements validés</div>
            <span class="badge-count">{{ $paiements->total() }} au total</span>
        </div>

        @forelse($paiements as $p)
        @if($loop->first)
        <div style="overflow-x:auto">
            <table class="dt">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Période</th>
                        <th>Date de paiement</th>
                        <th>Mode</th>
                        <th style="text-align:right">Montant encaissé</th>
                        <th style="text-align:center">Quittance</th>
                    </tr>
                </thead>
                <tbody>
        @endif
                    <tr>
                        <td style="font-family:'Syne',sans-serif;font-size:11px;color:#9ca3af">
                            {{ $p->reference_paiement ?? '—' }}
                        </td>
                        <td>
                            <span class="periode-pill">
                                {{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}
                            </span>
                        </td>
                        <td style="color:#6b7280;font-size:12px">
                            {{ \Carbon\Carbon::parse($p->date_paiement)->isoFormat('D MMM YYYY') }}
                        </td>
                        <td>
                            <span class="mode-badge">{{ $modes[$p->mode_paiement] ?? $p->mode_paiement }}</span>
                        </td>
                        <td style="text-align:right">
                            <span class="amt">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} FCFA</span>
                        </td>
                        <td style="text-align:center">
                            <a href="{{ route('locataire.paiements.pdf', $p) }}" target="_blank"
                               style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border:1px solid #e5e7eb;border-radius:7px;color:#6b7280;text-decoration:none;transition:all .15s"
                               onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f';this.style.background='#fffbeb'"
                               onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#6b7280';this.style.background='transparent'"
                               title="Télécharger la quittance PDF">
                                <svg style="width:13px;height:13px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="12" y1="18" x2="12" y2="12"/>
                                    <polyline points="9 15 12 18 15 15"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
        @if($loop->last)
                </tbody>
            </table>
        </div>
        @endif
    @empty
        <div class="empty-state">
            <div class="empty-icon">
                <svg style="width:22px;height:22px;color:#9ca3af" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="2" y="5" width="20" height="14" rx="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </div>
            <div class="empty-title">Aucun paiement enregistré</div>
            <p class="empty-sub">Vos loyers validés apparaîtront ici avec les quittances téléchargeables.</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($paiements->hasPages())
    <div class="pagination">
        <span>Page {{ $paiements->currentPage() }} / {{ $paiements->lastPage() }}</span>
        <div class="pag-links">
            <a href="{{ $paiements->previousPageUrl() ?? '#' }}"
               class="pag-link {{ $paiements->onFirstPage() ? 'disabled' : '' }}">
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
            @foreach($paiements->getUrlRange(max(1, $paiements->currentPage()-2), min($paiements->lastPage(), $paiements->currentPage()+2)) as $page => $url)
            <a href="{{ $url }}" class="pag-link {{ $page == $paiements->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            <a href="{{ $paiements->nextPageUrl() ?? '#' }}"
               class="pag-link {{ !$paiements->hasMorePages() ? 'disabled' : '' }}">
                <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>
    @endif

    </div>{{-- .card --}}

</div>
@endsection
