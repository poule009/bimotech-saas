@extends('layouts.legal')

@section('title', 'Mentions légales')
@section('heading', 'Mentions légales')

@section('content')
    <h2>1. Éditeur de la Plateforme</h2>
    <p>
        La plateforme {{ config('app.name', 'Bimothèque Immo') }} est éditée par
        <span class="legal-todo">[À COMPLÉTER : raison sociale]</span>,
        <span class="legal-todo">[À COMPLÉTER : forme juridique, ex. SARL / SAS]</span>
        au capital de <span class="legal-todo">[À COMPLÉTER : montant]</span> FCFA,
        dont le siège social est situé à <span class="legal-todo">[À COMPLÉTER : adresse complète]</span>.
    </p>
    <ul>
        <li><strong>NINEA</strong> : <span class="legal-todo">[À COMPLÉTER]</span></li>
        <li><strong>Registre du commerce (RCCM)</strong> : <span class="legal-todo">[À COMPLÉTER]</span></li>
        <li><strong>Email</strong> : <span class="legal-todo">[À COMPLÉTER]</span></li>
        <li><strong>Téléphone</strong> : <span class="legal-todo">[À COMPLÉTER]</span></li>
    </ul>

    <h2>2. Directeur de la publication</h2>
    <p><span class="legal-todo">[À COMPLÉTER : nom du directeur de la publication]</span></p>

    <h2>3. Hébergement</h2>
    <p>
        La Plateforme est hébergée par <span class="legal-todo">[À COMPLÉTER : nom de l'hébergeur]</span>,
        <span class="legal-todo">[À COMPLÉTER : adresse de l'hébergeur]</span>.
    </p>

    <h2>4. Propriété intellectuelle</h2>
    <p>
        L'ensemble des éléments de la Plateforme (marque, logo, textes, interface, code) est protégé par le droit de la
        propriété intellectuelle et demeure la propriété exclusive de l'éditeur. Toute reproduction ou représentation,
        totale ou partielle, sans autorisation préalable écrite, est interdite.
    </p>

    <h2>5. Responsabilité</h2>
    <p>
        L'éditeur s'efforce d'assurer l'exactitude et la disponibilité des informations et services proposés, sans pouvoir
        garantir une absence totale d'interruption ou d'erreur. L'agence utilisatrice demeure responsable de l'exactitude
        des données qu'elle saisit et de leur conformité à la réglementation applicable.
    </p>

    <h2>6. Données personnelles</h2>
    <p>
        Le traitement des données à caractère personnel est décrit dans notre
        <a href="{{ route('confidentialite') }}">politique de confidentialité</a>, conforme à la
        <strong>loi n° 2008-12 du 25 janvier 2008</strong> et déclarée auprès de la
        <strong>Commission de Protection des Données Personnelles (CDP)</strong>.
    </p>

    <h2>7. Conditions d'utilisation</h2>
    <p class="bg-gold/20 text-teal-deep font-semibold p-3 rounded-lg text-[13px] flex items-start gap-2">
        <x-icon name="alert-triangle" size="15" class="mt-0.5 shrink-0" /> <span>Section à part entière relevant de vos règles commerciales — à rédiger/valider par vous
        (idéalement avec un juriste). Éléments à préciser :</span>
    </p>
    <ul>
        <li><strong>Objet</strong> : mise à disposition d'un logiciel de gestion immobilière en ligne.</li>
        <li><strong>Abonnement et tarifs</strong> : <span class="legal-todo">[À COMPLÉTER : offres, prix en FCFA, période d'essai]</span></li>
        <li><strong>Obligations de l'utilisateur</strong> : usage licite, exactitude des données, confidentialité des identifiants.</li>
        <li><strong>Résiliation</strong> : <span class="legal-todo">[À COMPLÉTER : conditions et préavis]</span></li>
        <li><strong>Limitation de responsabilité</strong> : <span class="legal-todo">[À COMPLÉTER]</span></li>
    </ul>

    <h2>8. Droit applicable</h2>
    <p>
        Les présentes mentions sont régies par le <strong>droit sénégalais</strong>. Tout litige relève, à défaut de
        règlement amiable, de la compétence des tribunaux de <span class="legal-todo">[À COMPLÉTER : ville, ex. Dakar]</span>.
    </p>
@endsection
