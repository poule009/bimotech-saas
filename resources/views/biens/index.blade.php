@extends('layouts.app')

@section('title', 'Biens immobiliers')
@section('breadcrumb', 'Biens')

@section('content')
<style>
/* ── KPI biens ── */
.bien-kpi { background:#fffef9;border:1px solid #e8e3d8;border-radius:12px;padding:16px 18px;transition:box-shadow .15s; }
.bien-kpi:hover { box-shadow:0 2px 12px rgba(0,0,0,.06); }
.bien-kpi-lbl { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px; }
.bien-kpi-val { font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#0d1117; }

/* ── Filtres ── */
.filtre-input {
    width:100%;padding:8px 12px 8px 32px;
    border:1px solid #e8e3d8;border-radius:8px;
    font-size:13px;font-family:'DM Sans',sans-serif;
    color:#1c2128;background:#fffef9;
    outline:none;transition:border .15s,box-shadow .15s;
}
.filtre-input:focus { border-color:var(--ac,#c9a84c);box-shadow:0 0 0 3px rgba(201,168,76,.1); }
.filtre-select {
    font-family:'DM Sans',sans-serif;font-size:13px;
    border:1px solid #e8e3d8;border-radius:8px;
    padding:8px 12px;background:#fffef9;color:#1c2128;
    cursor:pointer;transition:border .15s;outline:none;
}
.filtre-select:focus { border-color:var(--ac,#c9a84c); }
.btn-effacer {
    display:inline-flex;align-items:center;
    padding:8px 14px;border:1px solid #e8e3d8;
    border-radius:8px;font-size:13px;color:#6b7280;
    text-decoration:none;background:#fffef9;transition:all .15s;
}
.btn-effacer:hover { border-color:#9ca3af;color:#374151; }

/* ── Carte bien ── */
.bien-card {
    background:#fffef9;border:1px solid #e8e3d8;
    border-radius:14px;overflow:hidden;
    transition:box-shadow .2s, transform .2s;
}
.bien-card:hover { box-shadow:0 8px 28px -4px rgba(0,0,0,.1);transform:translateY(-2px); }
.bien-card:active { transform:translateY(0);box-shadow:0 2px 8px rgba(0,0,0,.06); }

.bien-photo { height:160px;background:#f5f2ea;display:flex;align-items:center;justify-content:center;overflow:hidden; }
.bien-photo img { width:100%;height:100%;object-fit:cover; }

.bien-loyer { font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:var(--ac,#c9a84c); }
.bien-loyer-u { font-size:11px;color:#9ca3af;margin-left:2px; }
.bien-locataire { font-size:11px;color:#6b7280;margin-top:2px; }

.btn-voir {
    display:inline-flex;align-items:center;gap:4px;
    padding:7px 14px;border:1px solid #e8e3d8;
    border-radius:8px;font-size:12px;font-weight:500;
    color:#374151;text-decoration:none;background:#fffef9;
    transition:all .15s;
}
.btn-voir:hover { border-color:var(--ac,#c9a84c);color:#8a6e2f;background:#fdf8ed; }

/* ── Pagination ── */
.pag-wrap { display:flex;justify-content:center;gap:6px;margin-top:24px; }
.pag-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:32px;height:32px;border:1px solid #e8e3d8;border-radius:8px;
    font-size:13px;font-weight:500;color:#374151;
    text-decoration:none;background:#fffef9;transition:all .15s;
}
.pag-btn:hover { border-color:var(--ac,#c9a84c);color:#8a6e2f; }
.pag-btn.active { background:#0d1117;border-color:#0d1117;color:#fff;font-weight:700; }
.pag-btn.nav { color:#6b7280; }
</style>

<div style="padding:0 0 48px">

    {{-- HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Biens immobiliers
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ $biens->total() }} bien(s) enregistré(s)
            </p>
        </div>
        @can('create', App\Models\Bien::class)
            <a href="{{ route('admin.biens.create') }}"
               class="btn-primary">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouveau bien
            </a>
        @endcan
    </div>

    {{-- KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px">
        <div class="bien-kpi" style="border-top:3px solid var(--ac,#c9a84c)">
            <div class="bien-kpi-lbl">Total biens</div>
            <div class="bien-kpi-val">{{ $biens->total() }}</div>
        </div>
        <div class="bien-kpi" style="border-top:3px solid #16a34a">
            <div class="bien-kpi-lbl">Loués</div>
            <div class="bien-kpi-val" style="color:#16a34a">{{ $biens->where('statut','loue')->count() }}</div>
        </div>
        <div class="bien-kpi" style="border-top:3px solid #1d4ed8">
            <div class="bien-kpi-lbl">Disponibles</div>
            <div class="bien-kpi-val" style="color:#1d4ed8">{{ $biens->where('statut','disponible')->count() }}</div>
        </div>
        <div class="bien-kpi" style="border-top:3px solid #9ca3af">
            <div class="bien-kpi-lbl">En travaux</div>
            <div class="bien-kpi-val" style="color:#6b7280">{{ $biens->where('statut','en_travaux')->count() }}</div>
        </div>
    </div>

    {{-- FILTRES --}}
    <form method="GET" style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap;align-items:center">
        <div style="position:relative;flex:1;min-width:200px;max-width:320px">
            <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:14px;height:14px;color:#9ca3af;pointer-events:none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ request('q') }}"
                   placeholder="Référence, adresse, ville, propriétaire…"
                   class="filtre-input">
        </div>
        <select name="statut" onchange="this.form.submit()" class="filtre-select">
            <option value="">Tous les statuts</option>
            <option value="disponible" @selected(request('statut')==='disponible')>Disponible</option>
            <option value="loue"       @selected(request('statut')==='loue')>Loué</option>
            <option value="en_travaux" @selected(request('statut')==='en_travaux')>En travaux</option>
            <option value="archive"    @selected(request('statut')==='archive')>Archivé</option>
        </select>
        <select name="type" onchange="this.form.submit()" class="filtre-select">
            <option value="">Tous les types</option>
            <option value="appartement" @selected(request('type')==='appartement')>Appartement</option>
            <option value="villa"       @selected(request('type')==='villa')>Villa</option>
            <option value="bureau"      @selected(request('type')==='bureau')>Bureau</option>
            <option value="commerce"    @selected(request('type')==='commerce')>Commerce</option>
            <option value="terrain"     @selected(request('type')==='terrain')>Terrain</option>
        </select>
        <button type="submit" class="btn-submit">Rechercher</button>
        @if(request()->hasAny(['statut','type','q']))
            <a href="{{ route('admin.biens.index') }}" class="btn-effacer">Effacer les filtres</a>
        @endif
    </form>

    {{-- GRILLE / LISTE --}}
    @if($biens->isEmpty())
        <x-empty-state
            title="Aucun bien enregistré"
            description="Commencez par ajouter votre premier bien immobilier."
            action-label="Ajouter un bien"
            {{-- CORRIGÉ : admin.biens.create --}}
            :action-url="route('admin.biens.create')"
        />
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
            @foreach($biens as $bien)
            <div class="bien-card">

                {{-- Photo ou placeholder --}}
                <div class="bien-photo">
                    @php $photo = $bien->photos?->firstWhere('est_principale', true) ?? $bien->photos?->first(); @endphp
                    @if($photo)
                        <img src="{{ asset('storage/'.$photo->chemin) }}" alt="{{ $bien->titre }}">
                    @else
                        <svg style="width:40px;height:40px;color:#d1c9b0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    @endif
                </div>

                {{-- Contenu --}}
                <div style="padding:16px">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px">
                        <span style="font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#9ca3af">
                            {{ $bien->reference }}
                        </span>
                        @php
                            $badgeStyle = match($bien->statut) {
                                'loue'       => 'background:#dbeafe;color:#1d4ed8',
                                'disponible' => 'background:#dcfce7;color:#16a34a',
                                'en_travaux' => 'background:#fef9c3;color:#a16207',
                                default      => 'background:#f0ece3;color:#6b7280',
                            };
                        @endphp
                        <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;{{ $badgeStyle }}">
                            {{ $bien->statut_label }}
                        </span>
                    </div>

                    <h3 style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117;margin-bottom:4px">
                        {{ $bien->type_label }} — {{ $bien->titre ?? $bien->adresse }}
                    </h3>
                    <p style="font-size:12px;color:#6b7280;margin-bottom:12px">
                        {{ $bien->quartier }}, {{ $bien->ville }}
                    </p>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f0ece3">
                        <div>
                            <div class="bien-loyer">
                                {{ number_format($bien->loyer_hors_charges, 0, ',', ' ') }}<span class="bien-loyer-u">F/mois</span>
                            </div>
                            @if($bien->contratActif)
                                <div class="bien-locataire">
                                    {{ $bien->contratActif->locataire?->name ?? '—' }}
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('admin.biens.show', $bien) }}" class="btn-voir">
                            Voir
                            <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($biens->hasPages())
        <div class="pag-wrap">
            @if(!$biens->onFirstPage())
                <a href="{{ $biens->previousPageUrl() }}" class="pag-btn nav">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif
            @foreach($biens->getUrlRange(max(1,$biens->currentPage()-2), min($biens->lastPage(),$biens->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="pag-btn {{ $page === $biens->currentPage() ? 'active' : '' }}">{{ $page }}</a>
            @endforeach
            @if($biens->hasMorePages())
                <a href="{{ $biens->nextPageUrl() }}" class="pag-btn nav">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @endif
        </div>
        @endif
    @endif

</div>

@endsection