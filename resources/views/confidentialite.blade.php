@extends('layouts.vitrine')

@section('meta_title', 'Politique de confidentialité — Bimmo')
@section('meta_description', 'Comment Bimmo traite et protège les données personnelles des agences, propriétaires et locataires.')

@section('content')
<div class="wrap">
    <div class="simple-page">
        <h1>Politique de confidentialité</h1>
        <div class="updated">Bimmo — BIMO-tech · Dakar, Sénégal</div>

        {{-- TODO Malick : contenu à valider avant publication — sujet sensible (données
             personnelles réelles de propriétaires et locataires). Ne pas générer de texte
             juridique générique à la place d'une validation. --}}
        <div class="placeholder">
            Contenu en cours de rédaction. La politique de confidentialité définitive
            (nature des données traitées, finalités, durée de conservation, droits des
            personnes, sécurité) sera publiée après validation. Pour toute question relative
            à vos données, contactez-nous.
        </div>

        <h2>1. Données que nous traitons</h2>
        <p>Bimmo traite des données nécessaires à la gestion locative : identité et coordonnées des agences, propriétaires et locataires, contrats, paiements et documents associés. <em>[détail à compléter]</em></p>

        <h2>2. Finalités</h2>
        <p>Ces données servent exclusivement au fonctionnement du service de gestion (suivi des loyers, quittances, obligations fiscales). <em>[à compléter]</em></p>

        <h2>3. Sécurité &amp; conservation</h2>
        <p>Chaque agence dispose d'un espace isolé et sécurisé. <em>[durée de conservation et mesures de sécurité à compléter]</em></p>

        <h2>4. Vos droits</h2>
        <p>Vous pouvez demander l'accès, la rectification ou l'export de vos données à tout moment. <em>[modalités d'exercice à compléter]</em></p>
    </div>
</div>
@endsection
