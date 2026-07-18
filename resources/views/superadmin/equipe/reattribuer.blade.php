@extends('layouts.superadmin')

@section('title', 'Réattribuer les agences')

@php $fmt = fn ($n) => number_format((float) $n, 0, ',', ' '); @endphp

@section('content')
<div class="max-w-[820px] mx-auto">

    {{-- En-tête --}}
    <div class="mb-6">
        <a href="{{ route('superadmin.equipe.index') }}" class="text-[13px] font-semibold text-teal hover:underline inline-flex items-center gap-1.5 mb-3">← Équipe interne</a>
        <div class="text-[12px] font-semibold tracking-[0.12em] uppercase text-gold">Gestion plateforme</div>
        <h1 class="font-display font-medium text-[30px] text-ink mt-1">Réattribuer les agences de {{ $collaborateur->name }}</h1>
        <p class="text-[14.5px] text-muted mt-1.5">Change le champ « Amenée par » des agences actuellement attribuées à ce collaborateur. La réattribution est volontaire — elle ne se déclenche jamais automatiquement à la révocation.</p>
    </div>

    @if(session('error'))
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ session('error') }}</div>
    @endif

    @if($agences->isEmpty())
        <div class="bg-white border border-dashed border-line rounded-2xl px-6 py-8 text-center text-[13.5px] text-muted">
            {{ $collaborateur->name }} n'a aucune agence attribuée pour l'instant.
        </div>
    @else
        {{-- ─── Réattribution en masse (chemin principal) ─── --}}
        <div class="f-card mb-5">
            <div class="f-card-title">Tout réattribuer en une action</div>
            <p class="f-card-sub">Déplace les {{ $agences->count() }} agence(s) de {{ $collaborateur->name }} vers un autre compte, ou les repasse en « Non attribué ».</p>

            <form method="POST" action="{{ route('superadmin.equipe.reattribuer', $collaborateur) }}"
                  x-data="confirmForm" x-on:submit="submit"
                  data-confirm="Réattribuer TOUTES les agences de {{ $collaborateur->name }} ?"
                  class="flex flex-wrap items-end gap-3">
                @csrf
                <input type="hidden" name="mode" value="masse">
                <div class="flex-1 min-w-[240px]">
                    <label class="f-label">Nouvelle attribution</label>
                    <select name="cible" class="f-input cursor-pointer">
                        <option value="">Non attribué</option>
                        @foreach($cibles as $cible)
                            <option value="{{ $cible->id }}">{{ $cible->name }}@if($cible->sa_est_principal) (principal)@endif</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold whitespace-nowrap">Tout réattribuer</button>
            </form>
        </div>

        {{-- ─── Réattribution agence par agence (cas particuliers) ─── --}}
        <form method="POST" action="{{ route('superadmin.equipe.reattribuer', $collaborateur) }}">
            @csrf
            <input type="hidden" name="mode" value="individuel">
            <div class="f-card">
                <div class="f-card-title">Agence par agence</div>
                <p class="f-card-sub">Pour les cas particuliers : ne changez que les lignes voulues, laissez les autres sur {{ $collaborateur->name }}.</p>

                <div class="divide-y divide-paper-dim">
                    @foreach($agences as $agence)
                        <div class="flex flex-wrap items-center gap-3 py-3">
                            <div class="flex-1 min-w-[180px]">
                                <div class="font-semibold text-[13.5px] text-ink">{{ $agence->name }}</div>
                                <div class="text-[12px] text-muted">{{ $agence->email }}</div>
                            </div>
                            <select name="agences[{{ $agence->id }}]" class="f-input cursor-pointer max-w-[240px]">
                                <option value="{{ $collaborateur->id }}" selected>{{ $collaborateur->name }} (actuel)</option>
                                <option value="">Non attribué</option>
                                @foreach($cibles as $cible)
                                    <option value="{{ $cible->id }}">{{ $cible->name }}@if($cible->sa_est_principal) (principal)@endif</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end gap-3 mt-5">
                    <a href="{{ route('superadmin.equipe.index') }}" class="px-5 py-3 rounded-[11px] border-[1.5px] border-line text-[14px] font-bold text-ink hover:border-teal">Annuler</a>
                    <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold">Enregistrer les changements</button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
