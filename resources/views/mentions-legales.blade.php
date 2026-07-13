@extends('layouts.vitrine')

@section('meta_title', 'Mentions légales — Bimmo')
@section('meta_description', 'Mentions légales de Bimmo, édité par BIMO-tech (Dakar, Sénégal).')

@section('content')
<div class="wrap">
    <div class="simple-page">
        <h1>Mentions légales</h1>
        <div class="updated">Bimmo — BIMO-tech · Dakar, Sénégal</div>

        {{-- TODO Malick : contenu juridique exact à valider avant publication.
             Ne pas inventer de mentions légales génériques. --}}
        <div class="placeholder">
            Contenu en cours de rédaction. Les mentions légales définitives (raison sociale,
            forme juridique, NINEA, RCCM, adresse du siège, contact) seront publiées après
            validation. Pour toute question, contactez-nous.
        </div>

        <h2>1. Éditeur de la plateforme</h2>
        <p>Raison sociale : <em>[à compléter]</em> · Forme juridique : <em>[à compléter]</em> · NINEA : <em>[à compléter]</em> · RCCM : <em>[à compléter]</em>.</p>
        <p>Siège social : <em>[à compléter — adresse complète, Dakar]</em>.</p>

        <h2>2. Contact</h2>
        <p>Email : <em>[à compléter]</em> · Téléphone / WhatsApp : <em>[à compléter]</em>.</p>

        <h2>3. Hébergement</h2>
        <p>La plateforme est hébergée par <em>[à compléter — hébergeur, adresse]</em>.</p>

        <h2>4. Propriété intellectuelle</h2>
        <p>L'ensemble des contenus de ce site (marque « Bimmo », textes, éléments graphiques) est protégé. Toute reproduction sans autorisation est interdite.</p>
    </div>
</div>
@endsection
