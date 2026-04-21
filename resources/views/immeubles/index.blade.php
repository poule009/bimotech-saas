@extends('layouts.app')

@section('title', 'Immeubles')
@section('breadcrumb', 'Immeubles')

@section('content')

<div style="padding:0 0 48px">

    {{-- HEADER --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;flex-wrap:wrap;gap:12px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">
                Immeubles
            </h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">
                {{ $immeubles->total() }} immeuble(s) enregistré(s)
            </p>
        </div>
        @can('isStaff')
            <a href="{{ route('admin.immeubles.create') }}"
               style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;background:#c9a84c;color:#0d1117;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;border-radius:8px;text-decoration:none;transition:opacity .15s"
               onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nouvel immeuble
            </a>
        @endcan
    </div>

    {{-- GRILLE --}}
    @if($immeubles->isEmpty())
        <x-empty-state
            title="Aucun immeuble enregistré"
            description="Commencez par ajouter votre premier immeuble."
            action-label="Ajouter un immeuble"
            :action-url="route('admin.immeubles.create')"
        />
    @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px">
            @foreach($immeubles as $immeuble)
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;transition:box-shadow .2s"
                 onmouseover="this.style.boxShadow='0 4px 20px -4px rgba(0,0,0,.1)'"
                 onmouseout="this.style.boxShadow='none'">

                {{-- En-tête colorée --}}
                <div style="height:6px;background:linear-gradient(90deg,#0d1117,#1c2333)"></div>

                <div style="padding:16px">
                    {{-- Nom + icône --}}
                    <div style="display:flex;align-items:flex-start;gap:12px;margin-bottom:12px">
                        <div style="width:38px;height:38px;border-radius:10px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                            <svg style="width:18px;height:18px;color:#6b7280" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="2" y="3" width="20" height="18" rx="2"/>
                                <line x1="2" y1="9" x2="22" y2="9"/>
                                <line x1="12" y1="3" x2="12" y2="21"/>
                            </svg>
                        </div>
                        <div style="min-width:0">
                            <h3 style="font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                {{ $immeuble->nom }}
                            </h3>
                            <p style="font-size:12px;color:#6b7280">
                                {{ $immeuble->adresse }}, {{ $immeuble->ville }}
                            </p>
                        </div>
                    </div>

                    {{-- Méta --}}
                    <div style="display:flex;gap:10px;margin-bottom:14px">
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;background:#f3f4f6;color:#374151">
                            <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                            {{ $immeuble->biens_count }} unité(s)
                        </span>
                        @if($immeuble->nombre_niveaux)
                        <span style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;padding:3px 10px;border-radius:99px;background:#dbeafe;color:#1d4ed8">
                            {{ $immeuble->nombre_niveaux }} niveau(x)
                        </span>
                        @endif
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;padding-top:12px;border-top:1px solid #f3f4f6">
                        <div style="font-size:12px;color:#6b7280">
                            {{ $immeuble->proprietaire?->name ?? '—' }}
                        </div>
                        <a href="{{ route('admin.immeubles.show', $immeuble) }}"
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
        @if($immeubles->hasPages())
        <div style="display:flex;justify-content:center;gap:6px;margin-top:24px">
            @if(!$immeubles->onFirstPage())
                <a href="{{ $immeubles->previousPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;text-decoration:none">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif
            @foreach($immeubles->getUrlRange(max(1,$immeubles->currentPage()-2), min($immeubles->lastPage(),$immeubles->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid {{ $page===$immeubles->currentPage() ? '#0d1117' : '#e5e7eb' }};border-radius:8px;font-size:13px;font-weight:500;color:{{ $page===$immeubles->currentPage() ? '#fff' : '#374151' }};background:{{ $page===$immeubles->currentPage() ? '#0d1117' : '#fff' }};text-decoration:none">
                    {{ $page }}
                </a>
            @endforeach
            @if($immeubles->hasMorePages())
                <a href="{{ $immeubles->nextPageUrl() }}" style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;text-decoration:none">
                    <svg style="width:14px;height:14px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            @endif
        </div>
        @endif
    @endif

</div>

@endsection
