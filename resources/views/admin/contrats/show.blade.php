@extends('layouts.app')

@php
    $fmt  = fn ($n) => number_format((float) $n, 0, ',', ' ');
    $bien = $contrat->bien;
    $loc  = $contrat->locataire;
    $proprio = $bien?->proprietaire;
    $joursEcheance = $contrat->date_fin
        ? (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($contrat->date_fin)->startOfDay(), false)
        : null;
    $bientot = $contrat->statut === 'actif' && $joursEcheance !== null && $joursEcheance >= 0 && $joursEcheance <= 30;
    // WhatsApp locataire
    $telDigits = preg_replace('/\D/', '', (string) $loc?->telephone);
    if ($telDigits !== '' && ! str_starts_with($telDigits, '221')) $telDigits = '221' . ltrim($telDigits, '0');
    $waLink = $telDigits !== '' ? 'https://wa.me/' . $telDigits : null;
@endphp

@section('title', 'Contrat ' . ($contrat->reference_bail ?? ''))
@section('page-title', 'Fiche contrat')
@section('page-subtitle')
    <a href="{{ route('admin.contrats.index') }}" class="text-teal font-semibold hover:underline">Contrats</a>
    <span class="text-muted"> / {{ $bien->reference ?? '' }} — {{ $loc->name ?? '' }}</span>
@endsection

@section('content')
<div class="max-w-[1000px]">

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif

    {{-- En-tête --}}
    <div class="bg-white border border-line rounded-2xl overflow-hidden mb-5">
        <div class="px-6 pt-6 flex items-center gap-4">
            <div class="w-[52px] h-[52px] rounded-[13px] bg-teal text-paper flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 21v-5h4v5"/></svg>
            </div>
            <div class="min-w-0">
                <h2 class="font-display font-semibold text-[24px]">{{ $bien->reference ?? 'Bien' }}</h2>
                <div class="text-[13.5px] text-muted">Locataire : {{ $loc->name ?? '—' }} · Propriétaire : {{ $proprio->name ?? '—' }}</div>
            </div>
        </div>
        <div class="px-6 py-4 mt-4 border-t border-paper-dim flex flex-wrap items-center gap-3">
            @php
                $stMap = ['actif'=>['Actif','bg-green/10 text-green'],'resilié'=>['Résilié','bg-paper-dim text-muted'],'expiré'=>['Expiré','bg-error/10 text-error']];
                [$sl,$sc] = $bientot ? ['Échéance dans '.$joursEcheance.' j','bg-gold/15 text-gold'] : ($stMap[$contrat->statut] ?? [ucfirst($contrat->statut),'bg-paper-dim text-muted']);
            @endphp
            <span class="inline-flex items-center gap-1.5 text-[12.5px] font-bold px-3.5 py-1.5 rounded-full {{ $sc }}"><span class="w-[7px] h-[7px] rounded-full bg-current"></span> {{ $sl }}</span>
            <span class="text-[12.5px] text-muted">Réf. {{ $contrat->reference_bail }}</span>
            <div class="flex items-center gap-2.5 ml-auto">
                @if($contrat->statut === 'actif')
                    <a href="{{ route('admin.contrats.edit', $contrat) }}" class="px-4 py-2.5 rounded-[10px] border-[1.5px] border-line bg-white text-ink text-[13.5px] font-bold hover:border-teal transition-colors">Modifier</a>
                @endif
                @if(Route::has('admin.contrats.bail-formel-pdf'))
                    <a href="{{ route('admin.contrats.bail-formel-pdf', $contrat) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-[10px] border-[1.5px] border-teal text-teal bg-white text-[13.5px] font-bold hover:bg-paper transition-colors"><x-icon name="file-text" size="15" /> Exporter (PDF)</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Bannière échéance (gold plein, sans dégradé) --}}
    @if($bientot)
        <div class="bg-gold text-teal-deep rounded-2xl p-5 md:px-7 flex flex-col sm:flex-row sm:items-center gap-4 mb-5">
            <div class="w-[46px] h-[46px] rounded-[12px] bg-teal-deep/15 flex items-center justify-center text-[21px] shrink-0">⏱</div>
            <div class="flex-1 min-w-0">
                <div class="font-display font-semibold text-[15.5px]">Ce bail arrive à échéance le {{ \Carbon\Carbon::parse($contrat->date_fin)->locale('fr')->isoFormat('D MMMM Y') }}</div>
                <div class="text-[12.5px] text-teal-deep/80 mt-0.5">Renouvelez en un clic pour reconduire les mêmes conditions avec de nouvelles dates.</div>
            </div>
            <a href="{{ route('admin.contrats.create', ['from_contrat' => $contrat->id]) }}" class="bg-teal-deep text-paper px-5 py-2.5 rounded-[10px] text-[13.5px] font-bold hover:opacity-90 transition-opacity shrink-0 whitespace-nowrap">Renouveler</a>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-[1.4fr_1fr] gap-5 items-start">
        <div class="space-y-5">
            {{-- Conditions --}}
            <div class="f-card">
                <h3 class="f-card-title mb-4">Conditions du bail</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Début</div><div class="text-[15px] font-semibold">{{ optional($contrat->date_debut)->locale('fr')->isoFormat('D MMMM Y') }}</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Fin</div><div class="text-[15px] font-semibold">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->locale('fr')->isoFormat('D MMMM Y') : 'Indéterminée' }}</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Loyer mensuel</div><div class="text-[15px] font-semibold text-gold">{{ $fmt($contrat->loyer_contractuel) }} F</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Dépôt de garantie</div><div class="text-[15px] font-semibold">{{ $fmt($contrat->caution) }} F</div></div>
                    <div><div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Type de bail</div><div class="text-[15px] font-semibold">{{ \App\Models\Contrat::TYPES_BAIL[$contrat->type_bail] ?? ucfirst($contrat->type_bail) }}</div></div>
                </div>
                @if($contrat->clauses_particulieres)
                    <div class="mt-5 pt-5 border-t border-paper-dim">
                        <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Clauses particulières</div>
                        <div class="text-[14px] text-ink/80 leading-relaxed">{{ $contrat->clauses_particulieres }}</div>
                    </div>
                @endif
            </div>

            {{-- Historique des quittances --}}
            <div class="f-card">
                <h3 class="f-card-title mb-1">Historique des quittances</h3>
                <p class="f-card-sub">Générées automatiquement le 1er de chaque mois.</p>
                @if($paiements->isEmpty())
                    <div class="py-8 text-center text-[13.5px] text-muted">Aucune quittance pour l'instant.</div>
                @else
                    <div class="space-y-2.5">
                        @foreach($paiements as $p)
                            @php $paye = $p->statut === 'valide'; @endphp
                            <div class="flex flex-wrap items-center gap-3 p-4 border border-line rounded-xl bg-white">
                                <div class="font-bold text-[14px] w-[110px]">{{ \Carbon\Carbon::parse($p->periode)->locale('fr')->isoFormat('MMMM Y') }}</div>
                                <div class="text-[14px] text-muted w-[100px]">{{ $fmt($p->montant_encaisse ?: $contrat->loyer_contractuel) }} F</div>
                                @if($paye)
                                    <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full bg-green/10 text-green">Payé{{ $p->date_paiement ? ' le '.\Carbon\Carbon::parse($p->date_paiement)->isoFormat('D/MM') : '' }}</span>
                                @else
                                    <span class="text-[11.5px] font-bold px-3 py-1.5 rounded-full bg-error/10 text-error">En attente</span>
                                @endif
                                <div class="ml-auto flex items-center gap-2">
                                    @if($paye)
                                        @if(Route::has('admin.paiements.pdf'))<a href="{{ route('admin.paiements.pdf', $p) }}" class="text-[12px] font-bold px-3 py-1.5 rounded-lg bg-white border border-line text-ink hover:border-teal transition-colors">PDF</a>@endif
                                        @if($waLink)<a href="{{ $waLink }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 text-[12px] font-bold px-3 py-1.5 rounded-lg bg-[#25D366] text-white hover:opacity-90 transition-opacity"><x-icon-whatsapp /> Envoyer</a>@endif
                                    @else
                                        <form method="POST" action="{{ route('admin.paiements.marquer-paye', $p) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-[12px] font-bold px-3 py-1.5 rounded-lg bg-teal text-paper hover:bg-teal-deep transition-colors">Marquer payé</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="space-y-5">
            {{-- Parties --}}
            <div class="f-card">
                <h3 class="f-card-title mb-4">Parties</h3>
                <div class="space-y-4">
                    <div>
                        <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Propriétaire</div>
                        @if($proprio)<a href="{{ route('admin.users.show', $proprio) }}" class="text-[15px] font-semibold text-teal hover:underline">{{ $proprio->name }}</a>@else<span class="text-[15px] font-semibold">—</span>@endif
                    </div>
                    <div>
                        <div class="text-[11.5px] uppercase tracking-wide text-muted font-bold mb-1">Locataire</div>
                        @if($loc)<a href="{{ route('admin.users.show', $loc) }}" class="text-[15px] font-semibold text-teal hover:underline">{{ $loc->name }}</a>@else<span class="text-[15px] font-semibold">—</span>@endif
                    </div>
                </div>
            </div>

            {{-- Résiliation --}}
            @if($contrat->statut === 'actif')
                <div class="f-card">
                    <h3 class="f-card-title mb-1">Résiliation</h3>
                    <p class="f-card-sub">Le bien redeviendra disponible.</p>
                    <form method="POST" action="{{ route('admin.contrats.destroy', $contrat) }}"
                          x-data="confirmForm" x-on:submit="submit" data-confirm="Résilier ce contrat ? Le bien redeviendra disponible.">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 rounded-[10px] border-[1.5px] border-error/40 text-error bg-white text-[13.5px] font-bold hover:bg-error/5 transition-colors">Résilier le contrat</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
