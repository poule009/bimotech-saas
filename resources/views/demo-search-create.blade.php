@extends('layouts.app')

@section('title', 'Démo — Rechercher ou créer')
@section('page-title', 'Démo — Rechercher ou créer')
@section('page-subtitle', 'Aperçu du champ générique (temporaire)')

@section('content')
<div class="max-w-[520px]">
    <div class="f-card">
        <h3 class="f-card-title mb-1">Nouveau bien <span class="text-muted font-body font-normal text-[14px]">(extrait)</span></h3>
        <p class="f-card-sub">Tapez pour rechercher un propriétaire, ou créez-en un à la volée.</p>

        <x-search-or-create
            name="proprietaire_id"
            label="Propriétaire"
            type="propriétaire"
            :search-url="route('admin.users.proprietaires.search')"
            :create-url="route('admin.users.proprietaires.quick')" />
    </div>

    <p class="text-[12.5px] text-muted mt-4 leading-relaxed">
        Le même composant <code class="bg-paper-dim px-1 rounded">&lt;x-search-or-create&gt;</code> se branchera
        sur le champ Locataire du formulaire « Nouveau contrat », en changeant seulement <code class="bg-paper-dim px-1 rounded">type</code>,
        <code class="bg-paper-dim px-1 rounded">search-url</code> et <code class="bg-paper-dim px-1 rounded">create-url</code>.
    </p>
</div>
@endsection
