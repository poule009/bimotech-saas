@extends('layouts.app')

@php
    use App\Support\TeamAccess;
    $initiales = collect(explode(' ', trim($user->name)))
        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    $niveaux = TeamAccess::NIVEAUX; // none/view/full => labels
@endphp

@section('title', 'Accès — ' . $user->name)
@section('page-title', 'Accès de ' . $user->name)

@section('content')
<div class="max-w-[760px]" x-data="permMatrix">

    <div class="mb-6">
        <a href="{{ route('admin.equipe.index') }}" class="text-[13px] font-bold text-teal hover:underline flex items-center gap-1.5">← Mon équipe</a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green flex items-center gap-2"><x-icon name="check-circle" size="16" /> {{ session('success') }}</div>
    @endif

    {{-- Profil --}}
    <div class="bg-white border border-line rounded-2xl px-6 py-5 flex items-center gap-4 mb-5">
        <div class="w-14 h-14 rounded-[14px] bg-teal text-white flex items-center justify-center text-[19px] font-bold shrink-0">{{ $initiales }}</div>
        <div class="min-w-0">
            <h2 class="font-display font-semibold text-[20px]">{{ $user->name }}</h2>
            <div class="text-[13px] text-muted truncate">
                {{ $user->email }}@if($user->telephone) · {{ $user->telephone }}@endif
                · {{ $user->must_change_password ? 'Invitation en attente' : 'Actif' }}
            </div>
        </div>
        <div class="ml-auto shrink-0">
            <form method="POST" action="{{ route('admin.equipe.destroy', $user) }}" x-data="confirmForm" x-on:submit="submit"
                  data-confirm="Révoquer l'accès de {{ $user->name }} ? La session en cours sera coupée immédiatement.">
                @csrf @method('DELETE')
                <button type="submit" class="bg-white border-[1.5px] border-error/40 text-error px-4 py-2.5 rounded-[10px] text-[13.5px] font-bold hover:bg-error/5 flex items-center gap-1.5">
                    <x-icon name="trash" size="15" /> Révoquer l'accès
                </button>
            </form>
        </div>
    </div>

    {{-- Presets rapides --}}
    <div class="bg-white border border-line rounded-2xl px-6 py-4 mb-5 flex items-center gap-3 flex-wrap">
        <span class="text-[13px] font-bold text-teal-deep mr-1">Partir d'un profil :</span>
        @foreach(TeamAccess::PRESET_LABELS as $pk => $plabel)
            <button type="button" data-preset="{{ $pk }}" data-levels='@json(TeamAccess::presetLevels($pk))' x-on:click="applyPreset"
                    class="text-[12.5px] font-bold px-3.5 py-2 rounded-lg border-[1.5px] border-line bg-paper hover:border-teal text-ink">
                {{ $plabel }}
            </button>
        @endforeach
        <span class="text-[12px] text-muted">puis ajustez librement ci-dessous.</span>
    </div>

    {{-- Matrice --}}
    <form method="POST" action="{{ route('admin.equipe.permissions.update', $user) }}">
        @csrf
        <div class="bg-white border border-line rounded-2xl p-6">
            <div class="f-card-title">Accès par module</div>
            <p class="f-card-sub">Trois niveaux par module : aucun accès, consultation seule, ou consultation + modification.</p>

            @foreach(TeamAccess::MODULES as $modKey => $mod)
                <div @class([
                        'flex items-center gap-4 py-4',
                        'border-b border-paper-dim' => ! $mod['sensitive'],
                        'bg-gold/12 rounded-xl px-4 my-1' => $mod['sensitive'],
                    ])>
                    <span @class(['w-9 h-9 rounded-[9px] flex items-center justify-center shrink-0', 'bg-paper-dim' => !$mod['sensitive'], 'bg-white' => $mod['sensitive']])>
                        <x-icon :name="$mod['icon']" size="16" />
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-[14px]">{{ $mod['label'] }}</div>
                        @if($mod['sensitive'] && !empty($mod['warn']))
                            <div class="text-[11.5px] text-gold font-semibold mt-0.5 flex items-center gap-1"><x-icon name="alert-triangle" size="12" /> {{ $mod['warn'] }}</div>
                        @endif
                    </div>

                    <div data-group="{{ $modKey }}" class="flex gap-1.5 shrink-0">
                        <input type="hidden" name="niveaux[{{ $modKey }}]" value="{{ $levels[$modKey] }}">
                        @foreach($niveaux as $lvl => $lbl)
                            <button type="button" data-level="{{ $lvl }}" x-on:click="pick"
                                    class="lvl-opt {{ $levels[$modKey] === $lvl ? 'on-'.$lvl : '' }}">{{ $lbl }}</button>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="flex justify-end mt-6">
                <button type="submit" class="bg-teal hover:bg-teal-deep text-white px-6 py-3 rounded-[11px] text-[14px] font-bold">Enregistrer les accès</button>
            </div>
        </div>
    </form>

</div>
@endsection
