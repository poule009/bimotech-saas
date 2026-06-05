@extends('layouts.app')
@section('header', 'Journal d\'activité')

@section('content')

@php
$logsRoute = auth()->user()->isSuperAdmin()
    ? route('superadmin.activity-logs.index')
    : route('admin.activity-logs.index');
@endphp

<div class="space-y-4 md:space-y-5">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight">Journal d'activité</h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">Toutes les actions effectuées sur la plateforme · {{ $logs->total() }} entrée(s)</p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mb-1.5">Créations</div>
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $actionStats['created'] ?? 0 }}</div>
            <div class="font-body text-[10.5px] text-bimo-text/40 mt-1.5">Nouveaux enregistrements</div>
        </div>
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mb-1.5">Modifications</div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ $actionStats['updated'] ?? 0 }}</div>
            <div class="font-body text-[10.5px] text-bimo-gold/60 mt-1.5">Données mises à jour</div>
        </div>
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4">
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest {{ ($actionStats['deleted'] ?? 0) > 0 ? 'text-bimo-red/70' : 'text-bimo-text/50' }} mb-1.5">Suppressions</div>
            <div class="font-display font-extrabold text-2xl {{ ($actionStats['deleted'] ?? 0) > 0 ? 'text-bimo-red' : 'text-bimo-text' }} leading-none">{{ $actionStats['deleted'] ?? 0 }}</div>
            <div class="font-body text-[10.5px] {{ ($actionStats['deleted'] ?? 0) > 0 ? 'text-bimo-red/60' : 'text-bimo-text/40' }} mt-1.5">Enregistrements supprimés</div>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ $logsRoute }}"
          class="bg-white rounded-[14px] border border-bimo-navy/10 px-5 py-4 flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-[160px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Description, utilisateur, modèle…"
                   class="w-full pl-9 pr-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold focus:bg-white transition-all duration-150">
        </div>
        <select name="action"
                class="px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            <option value="">Toutes les actions</option>
            <option value="created" {{ request('action')==='created' ? 'selected':'' }}>Créations</option>
            <option value="updated" {{ request('action')==='updated' ? 'selected':'' }}>Modifications</option>
            <option value="deleted" {{ request('action')==='deleted' ? 'selected':'' }}>Suppressions</option>
        </select>
        <select name="model"
                class="px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            <option value="">Tous les modèles</option>
            @foreach(['Paiement','Contrat','Bien','User','Agency'] as $m)
            <option value="{{ $m }}" {{ request('model')===$m ? 'selected':'' }}>{{ $m }}</option>
            @endforeach
        </select>
        <input type="date" name="date" value="{{ request('date') }}"
               class="px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold transition-all duration-150">
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
            Filtrer
        </button>
        @if(request()->hasAny(['q','action','model','date']))
        <a href="{{ $logsRoute }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 border border-bimo-navy/15 rounded-[10px] font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
            <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            Effacer
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <div>
                <span class="font-display font-bold text-sm text-bimo-text">Historique des actions</span>
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $logs->total() }} entrée(s) · Page {{ $logs->currentPage() }} / {{ $logs->lastPage() }}</div>
            </div>
            <div class="hidden md:flex items-center gap-3 font-body text-xs text-bimo-text/40">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-bimo-navy/50 inline-block"></span>Créé</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-bimo-gold inline-block"></span>Modifié</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-bimo-red inline-block"></span>Supprimé</span>
            </div>
        </div>

        @if($logs->isEmpty())
        <div class="px-5 py-14 text-center">
            <div class="w-13 h-13 bg-bimo-navy/[5%] rounded-[14px] flex items-center justify-center mx-auto mb-4" style="width:52px;height:52px">
                <svg class="w-6 h-6 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="font-display font-bold text-sm text-bimo-text mb-2">Aucune activité enregistrée</div>
            <div class="font-body text-sm text-bimo-text/40">
                @if(request()->hasAny(['q','action','model','date']))
                    Aucun résultat pour ces filtres.
                    <a href="{{ $logsRoute }}" class="text-bimo-gold hover:text-bimo-text ml-1 transition-colors duration-150">Effacer les filtres</a>
                @else
                    Les actions sur la plateforme apparaîtront ici.
                @endif
            </div>
        </div>
        @else

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-bimo-navy/[5%]">
            @foreach($logs as $log)
            @php
                $actionClass = match($log->action) { 'created'=>['bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70','Créé'], 'updated'=>['bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold','Modifié'], 'deleted'=>['bg-bimo-red/10 border-bimo-red/20 text-bimo-red','Supprimé'], default=>['bg-bimo-navy/[5%] text-bimo-text/50',''.ucfirst($log->action)] };
                $modelName = $log->model_type ? class_basename($log->model_type) : '—';
            @endphp
            <div class="px-4 py-3.5">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold border {{ $actionClass[0] }}">{{ $actionClass[1] }}</span>
                    <span class="font-body text-xs text-bimo-text/40">{{ $log->created_at?->format('d/m/Y H:i') }}</span>
                </div>
                <div class="font-body text-sm text-bimo-text truncate">{{ $log->description ?? '—' }}</div>
                @if($log->user)<div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $log->user->name }}</div>@endif
            </div>
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-bimo-navy/[5%] bg-bimo-bg">
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40 whitespace-nowrap">Date & heure</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Action</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Description</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Modèle</th>
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Utilisateur</th>
                        @if(auth()->user()->isSuperAdmin())
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">Agence</th>
                        @endif
                        <th class="px-5 py-3 text-left font-body font-medium text-[10px] uppercase tracking-widest text-bimo-text/40">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-bimo-navy/[5%]">
                    @foreach($logs as $log)
                    @php
                        $actionBadge = match($log->action) { 'created'=>'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70', 'updated'=>'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'deleted'=>'bg-bimo-red/10 border-bimo-red/20 text-bimo-red', default=>'bg-bimo-navy/[5%] text-bimo-text/50' };
                        $actionLabel = match($log->action) { 'created'=>'Créé', 'updated'=>'Modifié', 'deleted'=>'Supprimé', default=>ucfirst($log->action) };
                        $modelName = $log->model_type ? class_basename($log->model_type) : '—';
                    @endphp
                    <tr class="hover:bg-bimo-bg transition-colors duration-100">
                        <td class="px-5 py-3.5 whitespace-nowrap">
                            <div class="font-body text-xs text-bimo-text/70">{{ $log->created_at?->format('d/m/Y H:i') }}</div>
                            <div class="font-body text-[11px] text-bimo-text/30 mt-0.5">{{ $log->created_at?->diffForHumans() }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-body font-semibold border {{ $actionBadge }}">
                                <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>{{ $actionLabel }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 max-w-[280px]">
                            <div class="font-body text-xs text-bimo-text/70 truncate" title="{{ $log->description }}">{{ $log->description ?? '—' }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($log->model_type)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 bg-bimo-navy/[5%] rounded-[6px] font-body text-[11px] font-medium text-bimo-text/70">
                                {{ $modelName }}<span class="text-bimo-text/30">#{{ $log->model_id }}</span>
                            </span>
                            @else
                            <span class="font-body text-xs text-bimo-text/30">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                @if($log->user)
                                <div class="w-7 h-7 rounded-[7px] bg-bimo-gold/15 flex items-center justify-center font-display font-bold text-[10px] text-bimo-gold flex-shrink-0">{{ mb_strtoupper(mb_substr($log->user->name,0,2)) }}</div>
                                <span class="font-body font-medium text-sm text-bimo-text">{{ $log->user->name }}</span>
                                @else
                                <div class="w-7 h-7 rounded-[7px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/></svg>
                                </div>
                                <span class="font-body text-xs text-bimo-text/40 italic">Système</span>
                                @endif
                            </div>
                        </td>
                        @if(auth()->user()->isSuperAdmin())
                        <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60">{{ $log->agency->name ?? '—' }}</td>
                        @endif
                        <td class="px-5 py-3.5" style="font-family:monospace">
                            <span class="font-body text-xs text-bimo-text/40">{{ $log->ip_address ?? '—' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%]">
            <span class="font-body text-xs text-bimo-text/40">{{ $logs->firstItem() }}–{{ $logs->lastItem() }} sur {{ $logs->total() }}</span>
            <div class="flex items-center gap-1">
                <a href="{{ $logs->previousPageUrl() ?? '#' }}" class="w-8 h-8 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[7px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ $logs->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2),min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}" class="w-8 h-8 inline-flex items-center justify-center border rounded-[7px] font-body text-xs transition-all duration-150 {{ $page === $logs->currentPage() ? 'bg-[var(--ac)] border-[var(--ac)] text-white font-bold' : 'border-bimo-navy/15 text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold' }}">{{ $page }}</a>
                @endforeach
                <a href="{{ $logs->nextPageUrl() ?? '#' }}" class="w-8 h-8 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[7px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ !$logs->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>
        @endif

        @endif
    </div>

</div>
@endsection
