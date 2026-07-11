@extends('layouts.app')

@section('title', 'Bilans fiscaux')
@section('page-title', 'Bilans fiscaux')
@section('page-subtitle')
    <span class="text-muted">Récapitulatif fiscal annuel par propriétaire</span>
@endsection

@section('content')
<div class="max-w-[1100px]">

    {{-- Sélecteur d'année --}}
    <form method="GET" class="mb-5 flex items-center gap-3">
        <label for="annee" class="f-label mb-0">Année</label>
        <select id="annee" name="annee" onchange="this.form.submit()" class="f-select max-w-[160px]">
            @php $annees = $anneesDisponibles->count() ? $anneesDisponibles : collect([$annee]); @endphp
            @foreach($annees as $a)
                <option value="{{ $a }}" @selected((int) $a === (int) $annee)>{{ $a }}</option>
            @endforeach
        </select>
    </form>

    <div class="f-card p-0 overflow-hidden">
        <table class="w-full text-[14px]">
            <thead>
                <tr class="text-left text-[12px] uppercase tracking-wide text-muted border-b border-line">
                    <th class="px-4 py-3">Propriétaire</th>
                    <th class="px-4 py-3">Contact</th>
                    <th class="px-4 py-3 text-right">Bilan {{ $annee }}</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proprietaires as $proprietaire)
                    @php $bilan = $bilans->get($proprietaire->id); @endphp
                    <tr class="border-b border-paper-dim hover:bg-paper/60">
                        <td class="px-4 py-3 font-semibold text-ink">{{ $proprietaire->name }}</td>
                        <td class="px-4 py-3 text-muted">{{ $proprietaire->email ?? $proprietaire->telephone ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($bilan)
                                <span class="text-teal font-semibold">{{ number_format((float) $bilan->revenus_bruts_total, 0, ',', ' ') }} F</span>
                            @else
                                <span class="text-muted text-[12.5px]">Non calculé</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.bilans-fiscaux.show', [$proprietaire, 'annee' => $annee]) }}"
                               class="text-teal font-semibold hover:underline">Voir le bilan</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-muted">Aucun propriétaire dans votre agence.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $proprietaires->appends(request()->query())->links() }}
    </div>
</div>
@endsection
