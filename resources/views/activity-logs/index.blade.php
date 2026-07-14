{{-- Journal partagé : shell Super Admin (cross-agence) pour le superadmin,
     shell agence pour un admin d'agence. --}}
@extends(auth()->user()?->isSuperAdmin() ? 'layouts.superadmin' : 'layouts.app')

@php
    use Illuminate\Support\Facades\Route;

    // Regroupement chronologique
    $bucketOf = function ($date) {
        $d = \Carbon\Carbon::parse($date);
        if ($d->isToday())     return ['today', "Aujourd'hui — " . $d->locale('fr')->isoFormat('D MMMM Y')];
        if ($d->isYesterday()) return ['yesterday', 'Hier — ' . $d->locale('fr')->isoFormat('D MMMM Y')];
        if ($d->greaterThanOrEqualTo(now()->startOfWeek())) return ['week', 'Cette semaine'];
        if ($d->greaterThanOrEqualTo(now()->startOfMonth())) return ['month', 'Ce mois-ci'];
        return ['older', $d->locale('fr')->isoFormat('MMMM Y')];
    };

    // Lien de ligne selon le rôle :
    //  - Superadmin : on ne quitte JAMAIS l'espace back-office → fiche agence
    //    (principe produit : le superadmin ne manipule pas la donnée métier
    //    d'une agence hors impersonation). Vaut aussi pour les suppressions,
    //    car l'agence, elle, existe toujours.
    //  - Admin agence : lien direct vers la fiche de l'entité concernée
    //    (jamais pour une suppression : l'entité n'existe plus).
    $entityUrl = function ($log) {
        if (auth()->user()?->isSuperAdmin()) {
            return $log->agency_id ? route('superadmin.agencies.show', $log->agency_id) : null;
        }
        if ($log->action === 'deleted') return null;
        return match (class_basename($log->model_type)) {
            'Contrat' => Route::has('admin.contrats.show') ? route('admin.contrats.show', $log->model_id) : null,
            'Bien'    => Route::has('admin.biens.show')    ? route('admin.biens.show', $log->model_id)    : null,
            'User'    => Route::has('admin.users.show')    ? route('admin.users.show', $log->model_id)     : null,
            default   => null,
        };
    };

    // Icône (nom composant) + couleur par type / action
    $iconOf = function ($log) {
        if ($log->action === 'deleted') return ['bg-error/10 text-error', 'trash'];
        return match (class_basename($log->model_type)) {
            'Paiement'     => ['bg-gold/15 text-gold', 'dollar'],
            'Contrat'      => ['bg-green/10 text-green', 'file-text'],
            'Bien'         => ['bg-teal/10 text-teal', 'home'],
            'User', 'Locataire', 'Proprietaire' => ['bg-[#EDE6F5] text-[#6B4A9C]', 'user'],
            'DepenseGestion' => ['bg-gold/15 text-gold', 'receipt'],
            default        => ['bg-paper-dim text-muted', 'file-text'],
        };
    };

    $fmtVal = fn ($v) => is_numeric($v) ? number_format((float) $v, 0, ',', ' ') : (string) ($v ?? '—');

    $chipClass = fn ($active) => $active
        ? 'bg-teal text-paper border-teal'
        : 'bg-paper text-muted border-line hover:border-teal';

    $currentBucket = null;
@endphp

@section('title', "Journal d'activité")
@section('page-title', "Journal d'activité")
@section('page-subtitle', "Historique complet et permanent de toutes les actions — rien n'est modifiable ni supprimable ici.")

@section('content')
<div class="max-w-[1000px]">

    {{-- Barre de filtres --}}
    <div class="bg-white border border-line rounded-2xl p-4 mb-6">
        <div class="flex flex-wrap items-center gap-2.5">
            <form method="GET" class="flex items-center gap-2.5 bg-paper border-[1.5px] border-line rounded-[10px] px-3.5 py-2.5 flex-1 min-w-[200px] max-w-[320px] focus-within:border-teal transition-colors">
                <svg class="w-[18px] h-[18px] text-muted shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="q" value="{{ $q }}" placeholder="Rechercher un nom, un bien…" class="flex-1 bg-transparent outline-none text-[13.5px] text-ink placeholder:text-muted">
                @if($categorie)<input type="hidden" name="categorie" value="{{ $categorie }}">@endif
                @if($sensibles)<input type="hidden" name="sensibles" value="1">@endif
            </form>

            <div class="w-px h-6 bg-line"></div>

            <a href="{{ request()->fullUrlWithQuery(['categorie' => null, 'page' => null]) }}" @class(['inline-flex items-center gap-1.5 text-[12.5px] font-bold rounded-full px-4 py-2 border transition-colors', $chipClass(! $categorie)])>Tout</a>
            <a href="{{ request()->fullUrlWithQuery(['categorie' => 'paiements', 'page' => null]) }}" @class(['inline-flex items-center gap-1.5 text-[12.5px] font-bold rounded-full px-3.5 py-2 border transition-colors', $chipClass($categorie === 'paiements')])><x-icon name="dollar" size="14" /> Paiements</a>
            <a href="{{ request()->fullUrlWithQuery(['categorie' => 'contrats', 'page' => null]) }}" @class(['inline-flex items-center gap-1.5 text-[12.5px] font-bold rounded-full px-3.5 py-2 border transition-colors', $chipClass($categorie === 'contrats')])><x-icon name="file-text" size="14" /> Contrats</a>
            <a href="{{ request()->fullUrlWithQuery(['categorie' => 'biens', 'page' => null]) }}" @class(['inline-flex items-center gap-1.5 text-[12.5px] font-bold rounded-full px-3.5 py-2 border transition-colors', $chipClass($categorie === 'biens')])><x-icon name="home" size="14" /> Biens</a>
            <a href="{{ request()->fullUrlWithQuery(['categorie' => 'personnes', 'page' => null]) }}" @class(['inline-flex items-center gap-1.5 text-[12.5px] font-bold rounded-full px-3.5 py-2 border transition-colors', $chipClass($categorie === 'personnes')])><x-icon name="users" size="14" /> Locataires & propriétaires</a>

            <div class="w-px h-6 bg-line"></div>

            <a href="{{ request()->fullUrlWithQuery(['sensibles' => $sensibles ? null : 1, 'page' => null]) }}" @class(['inline-flex items-center gap-1.5 text-[12.5px] font-bold rounded-full px-3.5 py-2 border transition-colors', $sensibles ? 'bg-error text-white border-error' : 'bg-paper text-error border-error/40 hover:border-error'])><x-icon name="alert-triangle" size="14" /> Sensibles uniquement</a>
        </div>
    </div>

    {{-- Fil d'activité --}}
    @forelse($logs as $log)
        @php
            [$bkey, $blabel] = $bucketOf($log->created_at);
            [$iconClass, $glyph] = $iconOf($log);
            $url = $entityUrl($log);
            $auto = $log->user_id === null;
            $showFullDate = in_array($bkey, ['week', 'month', 'older'], true);
        @endphp

        @if($bkey !== $currentBucket)
            @php $currentBucket = $bkey; @endphp
            <div class="font-display font-semibold text-[14.5px] text-teal mb-3 mt-6 first:mt-0">{{ $blabel }}</div>
        @endif

        <div @class([
            'flex gap-3.5 bg-white border rounded-[13px] p-4 mb-2.5',
            'border-error/35 bg-error/[0.03]' => $log->is_sensitive,
            'border-line' => ! $log->is_sensitive,
        ])>
            <div class="w-[38px] h-[38px] rounded-[10px] flex items-center justify-center shrink-0 {{ $iconClass }}"><x-icon :name="$glyph" size="18" /></div>

            <div class="flex-1 min-w-0">
                @if($url)
                    <a href="{{ $url }}" class="text-[14px] font-semibold leading-snug text-ink hover:text-teal transition-colors">{{ $log->description }}</a>
                @else
                    <div class="text-[14px] font-semibold leading-snug">{{ $log->description }}</div>
                @endif

                {{-- Diff avant/après --}}
                @if($log->action === 'updated' && ! empty($log->properties))
                    <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1">
                        @foreach($log->properties as $prop)
                            <div class="text-[13px]">
                                <span class="text-[11.5px] text-muted">{{ $prop['label'] ?? '' }} :</span>
                                <span class="text-muted line-through">{{ $fmtVal($prop['old'] ?? null) }}</span>
                                <span class="text-muted mx-1">→</span>
                                <span class="text-green font-bold">{{ $fmtVal($prop['new'] ?? null) }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Méta --}}
                <div class="flex items-center flex-wrap gap-x-2.5 gap-y-1 mt-2 text-[12px] text-muted">
                    @if($auto)
                        <span class="inline-flex items-center gap-1.5 font-semibold"><x-icon name="cpu" size="13" /> Système</span>
                    @else
                        <span class="inline-flex items-center gap-1.5 font-semibold">
                            <span class="w-[18px] h-[18px] rounded-[5px] bg-teal text-white text-[8.5px] font-bold flex items-center justify-center">{{ mb_strtoupper(mb_substr($log->user->name ?? '?', 0, 2)) }}</span>
                            {{ $log->user->name ?? 'Utilisateur' }}
                        </span>
                    @endif
                    <span>·</span>
                    <span>{{ \Carbon\Carbon::parse($log->created_at)->locale('fr')->isoFormat($showFullDate ? 'D MMM, HH:mm' : 'HH:mm') }}</span>

                    {{-- Vue cross-agence (superadmin) : rattacher chaque ligne à son agence. --}}
                    @if(auth()->user()?->isSuperAdmin() && $log->agency)
                        <span>·</span>
                        <span class="inline-flex items-center gap-1 font-semibold text-teal"><x-icon name="home" size="12" /> {{ $log->agency->name }}</span>
                    @endif

                    @if($auto)
                        <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-paper-dim text-muted">Automatique</span>
                    @else
                        <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-teal/10 text-teal">Manuel</span>
                    @endif

                    @if($log->is_sensitive)
                        <span class="inline-flex items-center gap-1 text-[10.5px] font-bold px-2 py-0.5 rounded-full bg-error/10 text-error"><x-icon name="alert-triangle" size="11" /> Sensible</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white border border-line rounded-2xl py-16 text-center">
            <div class="text-[15px] font-semibold mb-1">Aucune activité</div>
            <div class="text-[13px] text-muted">{{ $q !== '' || $categorie || $sensibles ? 'Aucun événement ne correspond à ces filtres.' : (auth()->user()?->isSuperAdmin() ? 'Les actions des agences apparaîtront ici.' : 'Les actions sur votre agence apparaîtront ici.') }}</div>
        </div>
    @endforelse

    @if($logs->hasPages())
        <div class="mt-6">{{ $logs->links() }}</div>
    @endif

</div>
@endsection
