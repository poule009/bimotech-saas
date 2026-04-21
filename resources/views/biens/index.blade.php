@extends('layouts.app')

@section('title', 'Biens immobiliers')
@section('breadcrumb', 'Biens')

@section('content')

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
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none;transition:opacity .15s"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouveau bien
            </a>
        @endcan
    </div>

    {{-- KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:22px">
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #c9a84c">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">Total biens</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#0d1117">{{ $biens->total() }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #16a34a">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">Loués</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#16a34a">{{ $biens->where('statut','loue')->count() }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #1d4ed8">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">Disponibles</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#1d4ed8">{{ $biens->where('statut','disponible')->count() }}</div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 18px;border-top:3px solid #9ca3af">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#9ca3af;margin-bottom:8px">En travaux</div>
            <div style="font-family:'Syne',sans-serif;font-size:26px;font-weight:700;color:#6b7280">{{ $biens->where('statut','en_travaux')->count() }}</div>
        </div>
    </div>

    {{-- FILTRES --}}
    <form method="GET" style="display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap">
        <select name="statut" onchange="this.form.submit()"
                style="font-family:'DM Sans',sans-serif;font-size:13px;border:1px solid #d0d7de;border-radius:8px;padding:8px 12px;background:#fff;color:#1c2128;cursor:pointer">
            <option value="">Tous les statuts</option>
            <option value="disponible" @selected(request('statut')==='disponible')>Disponible</option>
            <option value="loue"       @selected(request('statut')==='loue')>Loué</option>
            <option value="en_travaux" @selected(request('statut')==='en_travaux')>En travaux</option>
            <option value="archive"    @selected(request('statut')==='archive')>Archivé</option>
        </select>
        <select name="type" onchange="this.form.submit()"
                style="font-family:'DM Sans',sans-serif;font-size:13px;border:1px solid #d0d7de;border-radius:8px;padding:8px 12px;background:#fff;color:#1c2128;cursor:pointer">
            <option value="">Tous les types</option>
            <option value="appartement" @selected(request('type')==='appartement')>Appartement</option>
            <option value="villa"       @selected(request('type')==='villa')>Villa</option>
            <option value="bureau"      @selected(request('type')==='bureau')>Bureau</option>
            <option value="commerce"    @selected(request('type')==='commerce')>Commerce</option>
            <option value="terrain"     @selected(request('type')==='terrain')>Terrain</option>
        </select>
        @if(request()->hasAny(['statut','type']))
            <a href="{{ route('admin.biens.index') }}"
               style="display:inline-flex;align-items:center;padding:8px 14px;border:1px solid #d0d7de;border-radius:8px;font-size:13px;color:#6b7280;text-decoration:none;background:#fff">
                Effacer
            </a>
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
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;transition:box-shadow .2s"
                 onmouseover="this.style.boxShadow='0 4px 20px -4px rgba(0,0,0,.1)'"
                 onmouseout="this.style.boxShadow='none'">

                {{-- Photo ou placeholder --}}
                <div style="height:160px;background:#f9fafb;display:flex;align-items:center;justify-content:center;overflow:hidden">
                    @php $photo = $bien->photos?->firstWhere('est_principale', true) ?? $bien->photos?->first(); @endphp
                    @if($photo)
                        <img src="{{ asset('storage/'.$photo->chemin) }}" alt="{{ $bien->titre }}"
                             style="width:100%;height:100%;object-fit:cover">
                    @else
                        <svg style="width:40px;height:40px;color:#d1d5db" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
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
                                default      => 'background:#f3f4f6;color:#6b7280',
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

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f3f4f6">
                        <div>
                            <div style="font-family:'Syne',sans-serif;font-size:16px;font-weight:700;color:#c9a84c">
                                {{ number_format($bien->loyer_hors_charges, 0, ',', ' ') }} <span style="font-size:11px;color:#9ca3af">F/mois</span>
                            </div>
                            @if($bien->contratActif)
                                <div style="font-size:11px;color:#6b7280;margin-top:2px">
                                    {{ $bien->contratActif->locataire?->name ?? '—' }}
                                </div>
                            @endif
                        </div>
                        {{-- CORRIGÉ : admin.biens.show --}}
                        <a href="{{ route('admin.biens.show', $bien) }}"
                           style="display:inline-flex;align-items:center;gap:4px;padding:7px 14px;border:1px solid #e5e7eb;border-radius:8px;font-size:12px;font-weight:500;color:#374151;text-decoration:none;transition:all .15s"
                           onmouseover="this.style.borderColor='#c9a84c';this.style.color='#8a6e2f'"
                           onmouseout="this.style.borderColor='#e5e7eb';this.style.color='#374151'">
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
        <div style="display:flex;justify-content:center;gap:6px;margin-top:24px">
            @if(!$biens->onFirstPage())
                <a href="{{ $biens->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;text-decoration:none">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif
            @foreach($biens->getUrlRange(max(1,$biens->currentPage()-2), min($biens->lastPage(),$biens->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid {{ $page===$biens->currentPage() ? '#0d1117' : '#e5e7eb' }};border-radius:8px;font-size:13px;font-weight:500;color:{{ $page===$biens->currentPage() ? '#fff' : '#374151' }};background:{{ $page===$biens->currentPage() ? '#0d1117' : '#fff' }};text-decoration:none">
                    {{ $page }}
                </a>
            @endforeach
            @if($biens->hasMorePages())
                <a href="{{ $biens->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;text-decoration:none">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @endif
        </div>
        @endif
    @endif

</div>

@endsection