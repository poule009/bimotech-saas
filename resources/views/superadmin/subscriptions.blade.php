@extends('layouts.app')
@section('title', 'Abonnements')
@section('breadcrumb', 'Abonnements')

@section('content')
@php
$niveauColors = [
    'starter' => ['bg'=>'#f3f4f6', 'color'=>'#6b7280'],
    'pro'     => ['bg'=>'#ede9fe', 'color'=>'#7c3aed'],
    'agence'  => ['bg'=>'rgba(201,168,76,.12)', 'color'=>'#92681a'],
    'legacy'  => ['bg'=>'#ede9fe', 'color'=>'#7c3aed'],
];
$niveauLabels = config('plans.labels', ['starter'=>'Starter','pro'=>'Pro','agence'=>'Agence','legacy'=>'Pro']);
@endphp

<style>
.sub-grid { display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:24px; }
.sub-kpi  { background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px;text-align:center; }
.sub-kpi-val { font-family:'Syne',sans-serif;font-size:28px;font-weight:800;line-height:1; }
.sub-kpi-lbl { font-size:11px;color:#9ca3af;margin-top:6px; }

/* Dropdown Activer */
.dropdown { position:relative;display:inline-block; }
.dropdown-panel {
    position:absolute;right:0;top:calc(100% + 4px);
    background:#fff;border:1px solid #e5e7eb;border-radius:12px;
    box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:50;min-width:240px;
    display:none;padding:14px;
}
.dropdown-panel.open { display:block; }
.dp-label { font-size:10px;color:#9ca3af;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px; }
.dp-select {
    width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:8px;
    font-size:12px;color:#374151;background:#fff;margin-bottom:12px;
    appearance:none;-webkit-appearance:none;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 10px center;padding-right:28px;
    cursor:pointer;
}
.dp-submit {
    width:100%;padding:8px;border:none;border-radius:8px;
    background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;
    font-size:12px;font-weight:700;cursor:pointer;font-family:'Syne',sans-serif;
    transition:opacity .15s;
}
.dp-submit:hover { opacity:.9; }

@media(max-width:768px){.sub-grid{grid-template-columns:repeat(3,1fr)}}
@media(max-width:540px){.sub-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:380px){.sub-grid{grid-template-columns:1fr}}
</style>

<div style="padding:0 0 48px">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <div>
            <div style="font-family:'Syne',sans-serif;font-size:18px;font-weight:700;color:#0d1117">Abonnements</div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px">Gestion des accès agences</div>
        </div>
        <a href="{{ route('superadmin.dashboard') }}"
           style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1px solid #e5e7eb;border-radius:9px;font-size:12px;color:#374151;text-decoration:none;background:#fff">
            ← Retour
        </a>
    </div>

    {{-- KPIs --}}
    <div class="sub-grid">
        <div class="sub-kpi">
            <div class="sub-kpi-val" style="color:#6366f1">{{ $stats['nb_essai'] }}</div>
            <div class="sub-kpi-lbl">En essai</div>
        </div>
        <div class="sub-kpi">
            <div class="sub-kpi-val" style="color:#16a34a">{{ $stats['nb_actifs'] }}</div>
            <div class="sub-kpi-lbl">Abonnés actifs</div>
        </div>
        <div class="sub-kpi">
            <div class="sub-kpi-val" style="color:#dc2626">{{ $stats['nb_expires'] }}</div>
            <div class="sub-kpi-lbl">Expirés</div>
        </div>
        <div class="sub-kpi">
            <div class="sub-kpi-val" style="font-size:16px;color:#0d1117">{{ number_format($stats['revenus_total'], 0, ',', ' ') }}</div>
            <div class="sub-kpi-lbl">FCFA encaissés</div>
        </div>
        <div class="sub-kpi">
            <div class="sub-kpi-val" style="font-size:16px;color:#6366f1">{{ number_format($stats['revenus_mensuel_equiv'], 0, ',', ' ') }}</div>
            <div class="sub-kpi-lbl">MRR estimé (FCFA)</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="table-card">
        <div style="padding:14px 20px;border-bottom:1px solid #e5e7eb">
            <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117">Toutes les agences</div>
        </div>
        <div style="overflow-x:auto">
        <table class="dt">
            <thead>
                <tr>
                    <th>Agence</th>
                    <th style="text-align:center">Statut</th>
                    <th style="text-align:center">Durée</th>
                    <th style="text-align:center">Niveau</th>
                    <th style="text-align:center">Début</th>
                    <th style="text-align:center">Expiration</th>
                    <th style="text-align:center">Jours restants</th>
                    <th style="text-align:right">Montant payé</th>
                    <th style="text-align:center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                @php
                    $niv      = $sub->plan_niveau ?? 'legacy';
                    $nivColor = $niveauColors[$niv] ?? $niveauColors['legacy'];
                    $nivLabel = $niveauLabels[$niv] ?? ucfirst($niv);
                @endphp
                <tr>
                    <td>
                        <div style="font-weight:600">{{ $sub->agency->name }}</div>
                        <div style="font-size:11px;color:#9ca3af">{{ $sub->agency->email }}</div>
                    </td>
                    <td style="text-align:center">
                        @if($sub->estEnEssai())
                            <span class="badge" style="background:#ede9fe;color:#7c3aed"><span class="bdot"></span>Essai</span>
                        @elseif($sub->estActif())
                            <span class="badge" style="background:#dcfce7;color:#16a34a"><span class="bdot"></span>Actif</span>
                        @elseif($sub->statut === 'expiré')
                            <span class="badge" style="background:#fee2e2;color:#dc2626"><span class="bdot"></span>Expiré</span>
                        @else
                            <span class="badge" style="background:#f3f4f6;color:#6b7280">{{ $sub->statut }}</span>
                        @endif
                    </td>
                    <td style="text-align:center;color:#6b7280;font-size:12px">
                        {{ \App\Models\Subscription::LABELS[$sub->plan] ?? '—' }}
                    </td>
                    <td style="text-align:center">
                        <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:10px;font-weight:700;font-family:'Syne',sans-serif;background:{{ $nivColor['bg'] }};color:{{ $nivColor['color'] }}">
                            {{ $nivLabel }}
                        </span>
                    </td>
                    <td style="text-align:center;color:#6b7280;font-size:12px">
                        @if($sub->estEnEssai())
                            {{ $sub->date_debut_essai?->format('d/m/Y') }}
                        @else
                            {{ $sub->date_debut_abonnement?->format('d/m/Y') ?? '—' }}
                        @endif
                    </td>
                    <td style="text-align:center;color:#6b7280;font-size:12px">
                        @if($sub->estEnEssai())
                            {{ $sub->date_fin_essai?->format('d/m/Y') }}
                        @elseif($sub->estActif())
                            {{ $sub->date_fin_abonnement?->format('d/m/Y') }}
                        @else —
                        @endif
                    </td>
                    <td style="text-align:center">
                        @if($sub->estEnEssai())
                            @php $j = $sub->joursRestantsEssai() @endphp
                            <span style="font-weight:700;font-size:13px;color:{{ $j <= 7 ? '#dc2626' : '#6366f1' }}">{{ $j }}j</span>
                        @elseif($sub->estActif())
                            @php $j = $sub->joursRestantsAbonnement() @endphp
                            <span style="font-weight:700;font-size:13px;color:{{ $j <= 7 ? '#dc2626' : '#16a34a' }}">{{ $j }}j</span>
                        @else
                            <span style="color:#9ca3af">—</span>
                        @endif
                    </td>
                    <td style="text-align:right;font-weight:600">
                        {{ $sub->montant_paye ? number_format($sub->montant_paye, 0, ',', ' ').' F' : '—' }}
                    </td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:6px;justify-content:center;align-items:center">

                            {{-- Activer (durée + niveau) --}}
                            <div class="dropdown" id="drop-{{ $sub->id }}">
                                <button onclick="toggleDrop({{ $sub->id }}, event)"
                                    style="padding:5px 12px;border:1px solid #bbf7d0;background:#dcfce7;color:#16a34a;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                    Activer ▾
                                </button>
                                <div class="dropdown-panel" id="menu-{{ $sub->id }}">
                                    <form method="POST"
                                          action="{{ route('superadmin.agencies.abonnement.activer', $sub->agency) }}"
                                          onsubmit="return confirm('Activer cet abonnement pour {{ $sub->agency->name }} ?')">
                                        @csrf
                                        <div class="dp-label">Niveau d'accès</div>
                                        <select name="plan_niveau" class="dp-select">
                                            <option value="starter">Starter</option>
                                            <option value="pro" selected>Pro</option>
                                            <option value="agence">Agence</option>
                                        </select>
                                        <div class="dp-label">Durée</div>
                                        <select name="plan" class="dp-select">
                                            @foreach(\App\Models\Subscription::LABELS as $plan => $label)
                                            <option value="{{ $plan }}">{{ $label }} — {{ number_format(\App\Models\Subscription::TARIFS[$plan], 0, ',', ' ') }} F</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="dp-submit">Activer l'abonnement</button>
                                    </form>
                                </div>
                            </div>

                            {{-- Réinitialiser essai --}}
                            <form method="POST"
                                  action="{{ route('superadmin.agencies.essai.reinitialiser', $sub->agency) }}"
                                  onsubmit="return confirm('Réinitialiser l\'essai de {{ $sub->agency->name }} ?')">
                                @csrf
                                <button type="submit"
                                    style="padding:5px 12px;border:1px solid #bfdbfe;background:#dbeafe;color:#1d4ed8;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                    Essai
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:48px;color:#9ca3af">Aucun abonnement.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Pagination --}}
        @if($subscriptions->hasPages())
        <div style="padding:14px 20px;border-top:1px solid #e5e7eb">
            {{ $subscriptions->links() }}
        </div>
        @endif
    </div>

</div>

<script>
function toggleDrop(id, e) {
    e.stopPropagation();
    const menu = document.getElementById('menu-' + id);
    const isOpen = menu.classList.contains('open');

    // Fermer tous les autres dropdowns
    document.querySelectorAll('.dropdown-panel.open').forEach(m => m.classList.remove('open'));

    if (!isOpen) {
        menu.classList.add('open');
        // Fermer au clic extérieur
        setTimeout(() => {
            document.addEventListener('click', function close(e) {
                if (!document.getElementById('drop-' + id).contains(e.target)) {
                    menu.classList.remove('open');
                    document.removeEventListener('click', close);
                }
            });
        }, 0);
    }
}
</script>
@endsection
