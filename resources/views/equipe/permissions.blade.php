@extends('layouts.app')
@section('header', 'Permissions — ' . $user->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-5 md:space-y-6">

    {{-- En-tête --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('admin.equipe.index') }}"
                   class="font-body text-sm text-bimo-text/40 hover:text-bimo-text transition-colors duration-150">
                    Mon équipe
                </a>
                <svg class="w-3 h-3 text-bimo-text/30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="font-body text-sm text-bimo-text/60">{{ $user->name }}</span>
            </div>
            <h1 class="font-display font-extrabold text-xl md:text-2xl text-bimo-text tracking-tight leading-tight">
                Permissions
            </h1>
            <p class="font-body text-sm text-bimo-text/50 mt-1">
                Définissez exactement ce que <strong class="font-medium text-bimo-text">{{ $user->name }}</strong> peut consulter et modifier.
            </p>
        </div>
        <div class="w-11 h-11 rounded-[11px] bg-[var(--ac)]/10 flex items-center justify-center flex-shrink-0">
            <span class="font-display font-bold text-base text-[var(--ac)]">{{ mb_strtoupper(mb_substr($user->name, 0, 2)) }}</span>
        </div>
    </div>

    {{-- Profils prédéfinis --}}
    <div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
        <h2 class="font-display font-bold text-sm text-bimo-text mb-1">Appliquer un profil prédéfini</h2>
        <p class="font-body text-xs text-bimo-text/50 mb-4">Raccourci pour configurer rapidement les accès. Vous pouvez affiner case par case ensuite.</p>

        <form method="POST" action="{{ route('admin.equipe.permissions.update', $user) }}">
            @csrf
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                @foreach($roleLabels as $rKey => $rLabel)
                @php
                    $descriptions = [
                        'gestionnaire'  => 'Accès complet sauf fiscal avancé et comptabilité',
                        'comptable'     => 'Paiements + comptabilité en écriture',
                        'fiscaliste'    => 'Module fiscal en écriture, reste en lecture',
                        'lecture_seule' => 'Consultation uniquement, aucune modification',
                    ];
                    $icons = [
                        'gestionnaire'  => '<path d="M20 7H4a2 2 0 00-2 2v6a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
                        'comptable'     => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
                        'fiscaliste'    => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
                        'lecture_seule' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
                    ];
                    $isActive = $userRoleName === $rKey;
                @endphp
                <button type="submit" name="preset_role" value="{{ $rKey }}"
                        class="relative text-left p-3.5 rounded-[12px] border-2 transition-all duration-150
                               {{ $isActive ? 'border-[var(--ac)] bg-[var(--ac)]/5' : 'border-bimo-navy/10 hover:border-[var(--ac)]/40 hover:bg-bimo-bg' }}">
                    @if($isActive)
                    <span class="absolute top-2 right-2 w-4 h-4 rounded-full bg-[var(--ac)] flex items-center justify-center">
                        <svg class="w-2.5 h-2.5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                    </span>
                    @endif
                    <svg class="w-4 h-4 {{ $isActive ? 'text-[var(--ac)]' : 'text-bimo-text/40' }} mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        {!! $icons[$rKey] !!}
                    </svg>
                    <p class="font-display font-bold text-xs {{ $isActive ? 'text-[var(--ac)]' : 'text-bimo-text' }}">{{ $rLabel }}</p>
                    <p class="font-body text-[10px] {{ $isActive ? 'text-[var(--ac)]/70' : 'text-bimo-text/40' }} mt-0.5 leading-tight">{{ $descriptions[$rKey] }}</p>
                </button>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Permissions granulaires --}}
    <form method="POST" action="{{ route('admin.equipe.permissions.update', $user) }}" class="space-y-4">
        @csrf

        <div class="flex items-center justify-between">
            <h2 class="font-display font-bold text-sm text-bimo-text">Permissions détaillées</h2>
            <span class="font-body text-xs text-bimo-text/40">{{ count($userPermNames) }} permission(s) active(s)</span>
        </div>

        @foreach($permissions as $module => $perms)
        <div class="bg-white rounded-[14px] border border-bimo-navy/10 overflow-hidden">
            {{-- Header module --}}
            <div class="flex items-center gap-3 px-5 py-3 bg-bimo-bg2 border-b border-bimo-navy/[6%]">
                <div class="w-7 h-7 rounded-[7px] bg-bimo-navy/8 flex items-center justify-center flex-shrink-0">
                    @php
                    $moduleIcons = [
                        'biens'         => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
                        'contrats'      => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>',
                        'paiements'     => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>',
                        'locataires'    => '<path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>',
                        'proprietaires' => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
                        'immeubles'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v16"/>',
                        'impayes'       => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
                        'rapports'      => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
                        'fiscal'        => '<path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>',
                        'comptabilite'  => '<path d="M2 3h6a4 4 0 014 4v14a3 3 0 00-3-3H2z"/><path d="M22 3h-6a4 4 0 00-4 4v14a3 3 0 013-3h7z"/>',
                        'logs'          => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>',
                    ];
                    @endphp
                    <svg class="w-3.5 h-3.5 text-bimo-text/50" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        {!! $moduleIcons[$module] ?? '<circle cx="12" cy="12" r="10"/>' !!}
                    </svg>
                </div>
                <span class="font-display font-semibold text-xs text-bimo-text uppercase tracking-wider">
                    {{ $moduleLabels[$module] ?? $module }}
                </span>
            </div>

            {{-- Checkboxes --}}
            <div class="px-5 py-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($perms as $perm)
                @php
                    $action  = explode('.', $perm)[1] ?? $perm;
                    $label   = $permLabels[$action] ?? ucfirst($action);
                    $checked = in_array($perm, $userPermNames);
                    $isDanger = in_array($action, ['supprimer', 'annuler']);
                @endphp
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <div class="relative flex-shrink-0">
                        <input type="checkbox" name="permissions[]" value="{{ $perm }}"
                               {{ $checked ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-5 h-5 rounded-[5px] border-2 transition-all duration-150
                                    {{ $checked ? ($isDanger ? 'border-bimo-red bg-bimo-red' : 'border-[var(--ac)] bg-[var(--ac)]') : 'border-bimo-navy/20 bg-white group-hover:border-[var(--ac)]/50' }}
                                    peer-focus:ring-2 peer-focus:ring-[var(--ac)]/20 flex items-center justify-center">
                            @if($checked)
                            <svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                            @endif
                        </div>
                    </div>
                    <span class="font-body text-sm {{ $checked ? ($isDanger ? 'text-bimo-red font-medium' : 'text-bimo-text font-medium') : 'text-bimo-text/50' }} transition-colors duration-150">
                        {{ $label }}
                    </span>
                </label>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Barre sticky --}}
        <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-bimo-navy/10 px-0 py-3 flex items-center justify-between gap-3">
            <p class="font-body text-xs text-bimo-text/40 hidden sm:block">
                Les modifications prennent effet immédiatement.
            </p>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.equipe.index') }}"
                   class="inline-flex items-center px-5 py-2.5 rounded-[10px] border border-bimo-navy/15 font-body text-sm text-bimo-text/60 hover:text-bimo-text hover:border-bimo-navy/30 transition-all duration-150">
                    Annuler
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-[10px] bg-[var(--ac)] text-white font-display font-bold text-sm hover:opacity-90 transition-opacity duration-150">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Enregistrer
                </button>
            </div>
        </div>
    </form>

</div>

@push('scripts')
<script>
// Rend les checkboxes interactives sans rechargement de page
document.querySelectorAll('input[type="checkbox"]').forEach(input => {
    input.addEventListener('change', function () {
        const box = this.closest('label').querySelector('div > div');
        const label = this.closest('label').querySelector('span');
        const isDanger = ['supprimer', 'annuler'].includes(this.value.split('.')[1]);
        if (this.checked) {
            box.innerHTML = `<svg class="w-3 h-3 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>`;
            box.className = box.className.replace(/border-bimo-navy\/20 bg-white[^\s]*/g, '').trim();
            if (isDanger) {
                box.classList.add('border-bimo-red', 'bg-bimo-red');
                label.className = label.className.replace('text-bimo-text/50', 'text-bimo-red font-medium');
            } else {
                box.classList.add('border-[var(--ac)]', 'bg-[var(--ac)]');
                label.className = label.className.replace('text-bimo-text/50', 'text-bimo-text font-medium');
            }
        } else {
            box.innerHTML = '';
            box.classList.remove('border-[var(--ac)]', 'bg-[var(--ac)]', 'border-bimo-red', 'bg-bimo-red');
            box.classList.add('border-bimo-navy/20', 'bg-white');
            label.className = label.className.replace(/text-bimo-text font-medium|text-bimo-red font-medium/, 'text-bimo-text/50');
        }
    });
});
</script>
@endpush

@endsection
