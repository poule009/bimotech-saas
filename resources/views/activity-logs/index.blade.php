@extends('layouts.app')
@section('header', 'Journal d\'activité')

@section('content')

@php
$logsRoute = auth()->user()->isSuperAdmin()
    ? route('superadmin.activity-logs.index')
    : route('admin.activity-logs.index');

$modelRoutes = [
    'App\\Models\\Bien'     => 'admin.biens.show',
    'App\\Models\\Contrat'  => 'admin.contrats.show',
    'App\\Models\\Paiement' => 'admin.paiements.show',
    'App\\Models\\User'     => 'admin.users.show',
];

$modelLabels = [
    'App\\Models\\Bien'       => 'Bien',
    'App\\Models\\Contrat'    => 'Contrat',
    'App\\Models\\Paiement'   => 'Paiement',
    'App\\Models\\User'       => 'Utilisateur',
    'App\\Models\\Agency'     => 'Agence',
    'App\\Models\\Locataire'  => 'Locataire',
    'App\\Models\\Proprietaire' => 'Propriétaire',
];
@endphp

<div class="space-y-5 md:space-y-6">

    {{-- En-tête --}}
    <div>
        <h1 class="font-display font-extrabold text-2xl md:text-3xl text-bimo-text tracking-tight">Journal d'activité</h1>
        <p class="font-body text-sm text-bimo-text/50 mt-1">{{ $logs->total() }} entrée(s) enregistrées</p>
    </div>

    {{-- KPIs --}}
    <div class="grid grid-cols-3 gap-3 md:gap-4">
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-4 md:p-5">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-navy/[6%] flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl text-bimo-text leading-none">{{ $actionStats['created'] ?? 0 }}</div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-text/50 mt-1.5">Créations</div>
        </div>
        <div class="bg-bimo-gold/[8%] rounded-[14px] border border-bimo-gold/25 p-4 md:p-5">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-[8px] bg-bimo-gold/15 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl text-bimo-gold leading-none">{{ $actionStats['updated'] ?? 0 }}</div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest text-bimo-gold/70 mt-1.5">Modifications</div>
        </div>
        <div class="bg-white rounded-[14px] border {{ ($actionStats['deleted'] ?? 0) > 0 ? 'border-bimo-red/25' : 'border-bimo-navy/10' }} p-4 md:p-5">
            <div class="flex items-center gap-2.5 mb-3">
                <div class="w-8 h-8 rounded-[8px] {{ ($actionStats['deleted'] ?? 0) > 0 ? 'bg-bimo-red/10' : 'bg-bimo-navy/[6%]' }} flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 {{ ($actionStats['deleted'] ?? 0) > 0 ? 'text-bimo-red' : 'text-bimo-text/40' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        <path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                    </svg>
                </div>
            </div>
            <div class="font-display font-extrabold text-2xl {{ ($actionStats['deleted'] ?? 0) > 0 ? 'text-bimo-red' : 'text-bimo-text' }} leading-none">{{ $actionStats['deleted'] ?? 0 }}</div>
            <div class="font-body font-medium text-[9.5px] uppercase tracking-widest {{ ($actionStats['deleted'] ?? 0) > 0 ? 'text-bimo-red/70' : 'text-bimo-text/50' }} mt-1.5">Suppressions</div>
        </div>
    </div>

    {{-- Filtres --}}
    <form method="GET" action="{{ $logsRoute }}"
          class="bg-white rounded-[14px] border border-bimo-navy/10 px-5 py-4 flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-[160px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Description, utilisateur…"
                   class="w-full pl-9 pr-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold focus:bg-white transition-all duration-150">
        </div>
        <select name="action" class="px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            <option value="">Toutes les actions</option>
            <option value="created"  {{ request('action')==='created'  ? 'selected':'' }}>Créations</option>
            <option value="updated"  {{ request('action')==='updated'  ? 'selected':'' }}>Modifications</option>
            <option value="deleted"  {{ request('action')==='deleted'  ? 'selected':'' }}>Suppressions</option>
        </select>
        <select name="model" class="px-3 py-2.5 border border-bimo-navy/15 rounded-[8px] font-body text-sm text-bimo-text bg-bimo-bg focus:outline-none focus:border-bimo-gold cursor-pointer transition-all duration-150">
            <option value="">Tous les modèles</option>
            @foreach(['Paiement','Contrat','Bien','User','Agency','Locataire','Proprietaire'] as $m)
            <option value="{{ $m }}" {{ request('model')===$m ? 'selected':'' }}>{{ $modelLabels['App\\Models\\'.$m] ?? $m }}</option>
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

    {{-- Contenu --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">

        {{-- Header table --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-bimo-navy/[5%] bg-bimo-bg2">
            <span class="font-display font-bold text-sm text-bimo-text">
                Historique · Page {{ $logs->currentPage() }}/{{ $logs->lastPage() }}
            </span>
            <div class="hidden md:flex items-center gap-4">
                @foreach(['bg-bimo-navy/50' => 'Créé', 'bg-bimo-gold' => 'Modifié', 'bg-bimo-red' => 'Supprimé'] as $color => $lbl)
                <span class="flex items-center gap-1.5 font-body text-xs text-bimo-text/40">
                    <span class="w-2 h-2 rounded-full {{ $color }} inline-block"></span>{{ $lbl }}
                </span>
                @endforeach
            </div>
        </div>

        @if($logs->isEmpty())
        <div class="px-5 py-14 text-center">
            <div class="w-12 h-12 bg-bimo-navy/[5%] rounded-[14px] flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="font-display font-bold text-sm text-bimo-text mb-1">Aucune activité</div>
            <div class="font-body text-sm text-bimo-text/40">
                @if(request()->hasAny(['q','action','model','date']))
                    Aucun résultat pour ces filtres.
                    <a href="{{ $logsRoute }}" class="text-bimo-gold hover:text-bimo-text ml-1 transition-colors duration-150">Effacer</a>
                @else
                    Les actions sur la plateforme apparaîtront ici.
                @endif
            </div>
        </div>
        @else

        @php
            $today     = now()->format('Y-m-d');
            $yesterday = now()->subDay()->format('Y-m-d');
            $grouped   = $logs->groupBy(fn($l) => $l->created_at->format('Y-m-d'));
        @endphp

        {{-- Mobile --}}
        <div class="md:hidden">
            @foreach($grouped as $date => $dayLogs)
            {{-- Séparateur de jour --}}
            <div class="flex items-center gap-3 px-4 py-2.5 bg-bimo-bg border-b border-bimo-navy/[5%]">
                <span class="font-display font-bold text-xs text-bimo-text/60">
                    @if($date === $today) Aujourd'hui
                    @elseif($date === $yesterday) Hier
                    @else {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                    @endif
                </span>
                <span class="font-body text-[10px] text-bimo-text/30">{{ $dayLogs->count() }} action(s)</span>
            </div>
            @foreach($dayLogs as $log)
            @php
                $dotColor = match($log->action) { 'created'=>'bg-bimo-navy/50', 'updated'=>'bg-bimo-gold', 'deleted'=>'bg-bimo-red', default=>'bg-bimo-text/30' };
                $actionLabel = match($log->action) { 'created'=>'Créé', 'updated'=>'Modifié', 'deleted'=>'Supprimé', default=>ucfirst($log->action) };
                $badgeClass = match($log->action) { 'created'=>'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70', 'updated'=>'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'deleted'=>'bg-bimo-red/10 border-bimo-red/20 text-bimo-red', default=>'bg-bimo-navy/[5%] text-bimo-text/50' };
            @endphp
            <div class="px-4 py-3.5 border-b border-bimo-navy/[5%] last:border-0">
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-body font-semibold border {{ $badgeClass }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }} mr-1"></span>{{ $actionLabel }}
                    </span>
                    <span class="font-body text-[11px] text-bimo-text/30">{{ $log->created_at?->format('H:i') }}</span>
                </div>
                <div class="font-body text-sm text-bimo-text leading-snug">{{ $log->description ?? '—' }}</div>
                @if($log->user)
                <div class="font-body text-xs text-bimo-text/40 mt-0.5">{{ $log->user->name }}</div>
                @endif
            </div>
            @endforeach
            @endforeach
        </div>

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            @foreach($grouped as $date => $dayLogs)

            {{-- Séparateur de jour --}}
            <div class="flex items-center gap-3 px-5 py-2.5 bg-bimo-bg border-b border-t border-bimo-navy/[5%] first:border-t-0">
                <div class="w-1.5 h-1.5 rounded-full bg-bimo-navy/20"></div>
                <span class="font-display font-bold text-xs text-bimo-text/50">
                    @if($date === $today) Aujourd'hui — {{ now()->translatedFormat('d F Y') }}
                    @elseif($date === $yesterday) Hier — {{ now()->subDay()->translatedFormat('d F Y') }}
                    @else {{ \Carbon\Carbon::parse($date)->translatedFormat('l d F Y') }}
                    @endif
                </span>
                <span class="font-body text-[10px] text-bimo-text/30">{{ $dayLogs->count() }} action(s)</span>
            </div>

            <table class="w-full text-sm">
                <tbody class="divide-y divide-bimo-navy/[4%]">
                @foreach($dayLogs as $log)
                @php
                    $actionBadge  = match($log->action) { 'created'=>'bg-bimo-navy/10 border-bimo-navy/15 text-bimo-text/70', 'updated'=>'bg-bimo-gold/10 border-bimo-gold/20 text-bimo-gold', 'deleted'=>'bg-bimo-red/10 border-bimo-red/20 text-bimo-red', default=>'bg-bimo-navy/[5%] text-bimo-text/50' };
                    $actionLabel  = match($log->action) { 'created'=>'Créé', 'updated'=>'Modifié', 'deleted'=>'Supprimé', default=>ucfirst($log->action) };
                    $dotColor     = match($log->action) { 'created'=>'bg-bimo-navy/50', 'updated'=>'bg-bimo-gold', 'deleted'=>'bg-bimo-red', default=>'bg-bimo-text/30' };
                    $modelLabel   = $modelLabels[$log->model_type] ?? ($log->model_type ? class_basename($log->model_type) : '—');
                    $routeName    = $modelRoutes[$log->model_type] ?? null;
                    $entityUrl    = null;
                    if ($routeName && $log->model_id) {
                        try { $entityUrl = route($routeName, $log->model_id); } catch (\Exception $e) {}
                    }
                @endphp
                <tr class="hover:bg-bimo-bg transition-colors duration-100">

                    {{-- Heure --}}
                    <td class="px-5 py-3.5 whitespace-nowrap w-[90px]">
                        <div class="font-body text-sm text-bimo-text/60">{{ $log->created_at?->format('H:i') }}</div>
                        <div class="font-body text-[10px] text-bimo-text/30 mt-0.5">{{ $log->created_at?->diffForHumans() }}</div>
                    </td>

                    {{-- Action --}}
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-body font-semibold border {{ $actionBadge }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $dotColor }}"></span>
                            {{ $actionLabel }}
                        </span>
                    </td>

                    {{-- Description --}}
                    <td class="px-5 py-3.5 max-w-xs">
                        <div class="font-body text-sm text-bimo-text leading-snug" title="{{ $log->description }}">
                            {{ $log->description ?? '—' }}
                        </div>
                    </td>

                    {{-- Modèle + lien --}}
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        @if($log->model_type)
                        @if($entityUrl)
                        <a href="{{ $entityUrl }}"
                           class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-bimo-navy/[5%] rounded-[7px] font-body text-[11px] font-medium text-bimo-text/70 hover:bg-[var(--ac)]/10 hover:text-[var(--ac)] transition-all duration-150 group">
                            {{ $modelLabel }}
                            <span class="text-bimo-text/30 group-hover:text-[var(--ac)]/60">#{{ $log->model_id }}</span>
                            <svg class="w-2.5 h-2.5 opacity-0 group-hover:opacity-100 transition-opacity duration-150" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </a>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-bimo-navy/[5%] rounded-[7px] font-body text-[11px] font-medium text-bimo-text/70">
                            {{ $modelLabel }}<span class="text-bimo-text/30">#{{ $log->model_id }}</span>
                        </span>
                        @endif
                        @else
                        <span class="font-body text-xs text-bimo-text/30">—</span>
                        @endif
                    </td>

                    {{-- Utilisateur --}}
                    <td class="px-5 py-3.5 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            @if($log->user)
                            <div class="w-7 h-7 rounded-[7px] bg-[var(--ac)]/15 flex items-center justify-center font-display font-bold text-[10px] text-[var(--ac)] flex-shrink-0">
                                {{ mb_strtoupper(mb_substr($log->user->name, 0, 2)) }}
                            </div>
                            <span class="font-body font-medium text-sm text-bimo-text">{{ $log->user->name }}</span>
                            @else
                            <div class="w-7 h-7 rounded-[7px] bg-bimo-navy/[5%] flex items-center justify-center flex-shrink-0">
                                <svg class="w-3 h-3 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>
                                </svg>
                            </div>
                            <span class="font-body text-xs text-bimo-text/40 italic">Système</span>
                            @endif
                        </div>
                    </td>

                    {{-- Agence (superadmin only) --}}
                    @if(auth()->user()->isSuperAdmin())
                    <td class="px-5 py-3.5 font-body text-xs text-bimo-text/60 whitespace-nowrap">
                        {{ $log->agency->name ?? '—' }}
                    </td>
                    @endif

                    {{-- IP --}}
                    <td class="px-5 py-3.5">
                        <span class="font-body text-xs text-bimo-text/30" style="font-variant-numeric: tabular-nums">
                            {{ $log->ip_address ?? '—' }}
                        </span>
                    </td>

                </tr>
                @endforeach
                </tbody>
            </table>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($logs->hasPages())
        <div class="flex items-center justify-between px-5 py-3.5 border-t border-bimo-navy/[5%]">
            <span class="font-body text-xs text-bimo-text/40">
                {{ $logs->firstItem() }}–{{ $logs->lastItem() }} sur {{ $logs->total() }}
            </span>
            <div class="flex items-center gap-1">
                <a href="{{ $logs->previousPageUrl() ?? '#' }}"
                   class="w-8 h-8 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[7px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ $logs->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
                @foreach($logs->getUrlRange(max(1,$logs->currentPage()-2), min($logs->lastPage(),$logs->currentPage()+2)) as $page => $url)
                <a href="{{ $url }}"
                   class="w-8 h-8 inline-flex items-center justify-center border rounded-[7px] font-body text-xs transition-all duration-150
                          {{ $page === $logs->currentPage() ? 'bg-[var(--ac)] border-[var(--ac)] text-white font-bold' : 'border-bimo-navy/15 text-bimo-text/50 hover:border-bimo-gold hover:text-bimo-gold' }}">
                    {{ $page }}
                </a>
                @endforeach
                <a href="{{ $logs->nextPageUrl() ?? '#' }}"
                   class="w-8 h-8 inline-flex items-center justify-center border border-bimo-navy/15 rounded-[7px] text-bimo-text/40 hover:border-bimo-gold hover:text-bimo-gold transition-all duration-150 {{ !$logs->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
            </div>
        </div>
        @endif

        @endif
    </div>

</div>
@endsection
