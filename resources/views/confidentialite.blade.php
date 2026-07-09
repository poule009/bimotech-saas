@extends('layouts.legal')

@section('title', 'Politique de confidentialité')
@section('heading', 'Politique de confidentialité')

@section('content')
    <p>
        La présente politique décrit la manière dont <span class="legal-todo">[À COMPLÉTER : raison sociale de l'éditeur]</span>
        (ci-après « nous ») collecte, utilise et protège les données à caractère personnel des utilisateurs
        de la plateforme {{ config('app.name', 'Bimothèque Immo') }} (ci-après « la Plateforme »).
        Elle est établie conformément à la <strong>loi n° 2008-12 du 25 janvier 2008</strong> sur la protection des
        données à caractère personnel et à son <strong>décret d'application n° 2008-721 du 30 juin 2008</strong>.
    </p>

    <h2>1. Responsable du traitement</h2>
    <p>
        Le responsable du traitement des données est <span class="legal-todo">[À COMPLÉTER : raison sociale]</span>,
        dont le siège est situé à <span class="legal-todo">[À COMPLÉTER : adresse du siège]</span>,
        joignable à l'adresse <span class="legal-todo">[À COMPLÉTER : email de contact]</span>.
    </p>

    <h2>2. Données que nous collectons</h2>
    <p>Dans le cadre de l'utilisation de la Plateforme, nous sommes amenés à traiter :</p>
    <ul>
        <li><strong>Données de compte</strong> : nom de l'agence et de l'administrateur, adresse email, mot de passe (chiffré), identifiant Google le cas échéant.</li>
        <li><strong>Données de gestion locative</strong> saisies par l'agence : informations sur les biens, les propriétaires, les locataires, les baux et les paiements.</li>
        <li><strong>Données fiscales</strong> nécessaires aux obligations sénégalaises (TVA, BRS, NINEA, loi 81-18).</li>
        <li><strong>Données techniques</strong> : journaux de connexion, adresse IP, horodatage, à des fins de sécurité.</li>
    </ul>

    <h2>3. Finalités du traitement</h2>
    <ul>
        <li>Fournir et faire fonctionner le service de gestion immobilière ;</li>
        <li>Générer les documents fiscaux et comptables prévus par la réglementation sénégalaise ;</li>
        <li>Assurer la sécurité des comptes et prévenir la fraude ;</li>
        <li>Gérer l'abonnement et la relation client.</li>
    </ul>

    <h2>4. Base légale</h2>
    <p>
        Les traitements reposent sur le <strong>consentement</strong> de l'utilisateur lors de la création du compte,
        sur l'<strong>exécution du contrat</strong> de service qui nous lie à l'agence, et sur le respect de nos
        <strong>obligations légales</strong> (notamment fiscales). Conformément à la loi 2008-12, aucune donnée
        n'est collectée ou traitée sans fondement légitime.
    </p>

    <h2>5. Déclaration auprès de la CDP</h2>
    <p>
        Conformément à l'article 18 de la loi n° 2008-12, les traitements de données mis en œuvre sur la Plateforme
        font l'objet des formalités requises auprès de la <strong>Commission de Protection des Données Personnelles (CDP)</strong>.
        Récépissé / numéro de déclaration : <span class="legal-todo">[À COMPLÉTER : n° de récépissé CDP]</span>.
    </p>

    <h2>6. Destinataires des données</h2>
    <p>
        Les données ne sont accessibles qu'aux personnes habilitées de l'agence concernée et à notre personnel autorisé.
        Elles peuvent être transmises à nos sous-traitants techniques (hébergeur, prestataire de paiement) dans la stricte
        mesure nécessaire au service, et aux <strong>administrations compétentes</strong> lorsque la loi l'exige.
        Nous ne vendons ni ne louons aucune donnée personnelle.
    </p>

    <h2>7. Durée de conservation</h2>
    <p>
        Les données sont conservées pour la durée de la relation contractuelle, puis archivées conformément aux
        obligations légales de conservation (notamment comptables et fiscales), soit
        <span class="legal-todo">[À COMPLÉTER : durée, ex. 10 ans pour les pièces comptables]</span>,
        avant suppression ou anonymisation.
    </p>

    <h2>8. Sécurité</h2>
    <p>
        Nous mettons en œuvre des mesures techniques et organisationnelles pour préserver la confidentialité, l'intégrité
        et la disponibilité des données : chiffrement des mots de passe, connexions sécurisées (HTTPS), contrôle des accès,
        journalisation et sauvegardes régulières.
    </p>

    <h2>9. Vos droits</h2>
    <p>
        Conformément à la loi n° 2008-12, vous disposez d'un droit d'<strong>accès</strong>, de <strong>rectification</strong>,
        d'<strong>opposition</strong> et de <strong>suppression</strong> de vos données. Vous pouvez exercer ces droits en
        écrivant à <span class="legal-todo">[À COMPLÉTER : email de contact]</span>. En cas de difficulté, vous pouvez saisir
        la CDP (<a href="https://www.cdp.sn" target="_blank" rel="noopener">www.cdp.sn</a>).
    </p>

    <h2>10. Cookies</h2>
    <p>
        La Plateforme utilise des cookies strictement nécessaires à son fonctionnement (session, sécurité, authentification).
        <span class="legal-todo">[À COMPLÉTER si des cookies de mesure d'audience ou tiers sont ajoutés.]</span>
    </p>

    <h2>11. Modifications</h2>
    <p>
        Nous pouvons faire évoluer la présente politique. Toute modification substantielle sera portée à la connaissance
        des utilisateurs. La date de dernière mise à jour figure en haut de cette page.
    </p>
@endsection
