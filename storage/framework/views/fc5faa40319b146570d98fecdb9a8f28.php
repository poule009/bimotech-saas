<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Confidentialité — BimoTech Immo</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--gold:#c9a84c;--dark:#0d1117;--dark2:#161b22;--text:#e6edf3;--muted:#8b949e;--border:rgba(255,255,255,.08)}
body{font-family:'DM Sans',sans-serif;background:var(--dark);color:var(--text)}
nav{position:fixed;top:0;left:0;right:0;z-index:100;padding:0 5%;height:64px;display:flex;align-items:center;justify-content:space-between;background:rgba(13,17,23,.85);backdrop-filter:blur(12px);border-bottom:1px solid var(--border)}
.nav-logo{font-family:'Syne',sans-serif;font-size:18px;font-weight:800;color:var(--gold);text-decoration:none}
.nav-logo span{color:var(--text)}
.nav-back{font-size:13px;color:var(--muted);text-decoration:none;transition:color .2s}
.nav-back:hover{color:var(--text)}

.page{max-width:720px;margin:0 auto;padding:120px 5% 6rem}
.page-tag{font-size:11px;color:var(--gold);font-weight:600;letter-spacing:2px;text-transform:uppercase;margin-bottom:1rem}
h1{font-family:'Syne',sans-serif;font-size:clamp(26px,4vw,40px);font-weight:800;letter-spacing:-1px;margin-bottom:.5rem}
.update-date{font-size:13px;color:#484f58;margin-bottom:3rem}

h2{font-family:'Syne',sans-serif;font-size:17px;font-weight:700;color:var(--text);margin:2.5rem 0 .75rem;padding-bottom:8px;border-bottom:1px solid var(--border)}
h3{font-size:14px;font-weight:600;color:var(--text);margin:1.25rem 0 .5rem}
p{font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:.75rem}
ul{list-style:none;padding:0;margin-bottom:.75rem}
ul li{font-size:14px;color:var(--muted);line-height:1.7;padding:3px 0 3px 16px;position:relative}
ul li::before{content:'·';position:absolute;left:0;color:var(--gold);font-size:18px;line-height:1.4}
a{color:var(--gold);text-decoration:none}
a:hover{text-decoration:underline}
strong{color:var(--text);font-weight:500}

.highlight-box{background:rgba(201,168,76,.06);border:1px solid rgba(201,168,76,.15);border-radius:12px;padding:1.25rem 1.5rem;margin:1.5rem 0}
.highlight-box p{color:#d4b96a;margin-bottom:0}

footer{padding:2rem 5%;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-top:2rem}
.footer-logo{font-family:'Syne',sans-serif;font-size:15px;font-weight:800;color:var(--gold)}
.footer-links{display:flex;gap:1.5rem}
.footer-links a{font-size:12px;color:var(--muted);text-decoration:none}
.footer-copy{font-size:12px;color:#484f58}
</style>
</head>
<body>

<nav>
    <a href="<?php echo e(url('/')); ?>" class="nav-logo">Bimo<span>Tech</span></a>
    <a href="<?php echo e(url('/')); ?>" class="nav-back">← Retour à l'accueil</a>
</nav>

<div class="page">
    <div class="page-tag">Légal</div>
    <h1>Politique de confidentialité</h1>
    <p class="update-date">Dernière mise à jour : <?php echo e(date('d/m/Y')); ?></p>

    <div class="highlight-box">
        <p>BimoTech Immo s'engage à protéger vos données personnelles. Nous ne vendons jamais vos données à des tiers et nous ne faisons aucune publicité ciblée.</p>
    </div>

    <h2>1. Qui sommes-nous ?</h2>
    <p>BimoTech Immo est une plateforme SaaS de gestion immobilière destinée aux agences immobilières sénégalaises, éditée par BimoTech, dont le siège est à Dakar, Sénégal.</p>
    <p>Pour toute question relative à vos données : <a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a></p>

    <h2>2. Données collectées</h2>

    <h3>Lors de la création de compte</h3>
    <ul>
        <li>Nom et prénom de l'administrateur</li>
        <li>Adresse email professionnelle</li>
        <li>Mot de passe (chiffré, jamais stocké en clair)</li>
        <li>Nom et NINEA de l'agence</li>
        <li>Numéro de téléphone de l'agence</li>
    </ul>

    <h3>Dans le cadre de l'utilisation du service</h3>
    <ul>
        <li>Informations sur les biens immobiliers gérés</li>
        <li>Données des propriétaires et locataires (noms, contacts, pièces d'identité)</li>
        <li>Contrats de bail et documents associés</li>
        <li>Historique des paiements de loyer</li>
        <li>Logs d'activité (actions réalisées, dates, adresses IP)</li>
    </ul>

    <h3>Données techniques</h3>
    <ul>
        <li>Adresse IP lors de la connexion</li>
        <li>Navigateur et système d'exploitation</li>
        <li>Cookies de session (strictement nécessaires)</li>
    </ul>

    <h2>3. Finalités du traitement</h2>
    <p>Vos données sont utilisées exclusivement pour :</p>
    <ul>
        <li>Fournir et améliorer le service BimoTech Immo</li>
        <li>Gérer votre compte et authentifier vos connexions</li>
        <li>Générer les quittances de loyer et documents légaux</li>
        <li>Assurer la sécurité de la plateforme (logs d'audit)</li>
        <li>Vous contacter en cas de problème technique ou de facturation</li>
    </ul>

    <h2>4. Base légale du traitement</h2>
    <p>Le traitement de vos données est fondé sur :</p>
    <ul>
        <li><strong>L'exécution du contrat :</strong> pour la fourniture du service souscrit</li>
        <li><strong>L'obligation légale :</strong> conservation des données fiscales et comptables</li>
        <li><strong>L'intérêt légitime :</strong> sécurité de la plateforme et prévention des fraudes</li>
    </ul>

    <h2>5. Durée de conservation</h2>
    <ul>
        <li><strong>Données de compte actif :</strong> pendant toute la durée de l'abonnement</li>
        <li><strong>Données après résiliation :</strong> 30 jours (export possible avant suppression)</li>
        <li><strong>Quittances et documents fiscaux :</strong> 10 ans (obligation légale)</li>
        <li><strong>Logs de sécurité :</strong> 12 mois glissants</li>
    </ul>

    <h2>6. Partage des données</h2>
    <p>BimoTech ne vend, ne loue et ne partage jamais vos données avec des tiers à des fins commerciales.</p>
    <p>Vos données peuvent être partagées avec :</p>
    <ul>
        <li><strong>Hébergeur :</strong> uniquement pour le stockage sécurisé des données</li>
        <li><strong>Prestataires techniques :</strong> envoi d'emails transactionnels (ex : confirmation d'inscription)</li>
        <li><strong>Autorités légales :</strong> uniquement sur réquisition judiciaire</li>
    </ul>

    <h2>7. Sécurité</h2>
    <p>BimoTech met en œuvre les mesures techniques et organisationnelles suivantes :</p>
    <ul>
        <li>Chiffrement des mots de passe (bcrypt)</li>
        <li>Protection CSRF sur tous les formulaires</li>
        <li>Isolation des données par agence (multi-tenancy)</li>
        <li>Connexions HTTPS obligatoires</li>
        <li>Logs d'audit de toutes les actions sensibles</li>
        <li>Accès aux données de production restreint</li>
    </ul>

    <h2>8. Vos droits</h2>
    <p>Conformément à la loi sénégalaise n°2008-12 du 25 janvier 2008 sur la protection des données personnelles, vous disposez des droits suivants :</p>
    <ul>
        <li><strong>Droit d'accès :</strong> obtenir une copie de vos données</li>
        <li><strong>Droit de rectification :</strong> corriger des données inexactes</li>
        <li><strong>Droit à l'effacement :</strong> demander la suppression de vos données</li>
        <li><strong>Droit à la portabilité :</strong> exporter vos données dans un format lisible</li>
        <li><strong>Droit d'opposition :</strong> vous opposer à certains traitements</li>
    </ul>
    <p>Pour exercer ces droits : <a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a></p>
    <p>Vous pouvez également adresser une réclamation à la <strong>Commission de Protection des Données Personnelles (CDP)</strong> du Sénégal : <a href="https://www.cdp.sn" target="_blank">www.cdp.sn</a></p>

    <h2>9. Cookies</h2>
    <p>BimoTech utilise uniquement des cookies strictement nécessaires au fonctionnement de la plateforme :</p>
    <ul>
        <li><strong>Cookie de session :</strong> maintient votre connexion active</li>
        <li><strong>Cookie CSRF :</strong> protège contre les attaques de falsification de requête</li>
    </ul>
    <p>Aucun cookie publicitaire, analytique tiers ou de tracking n'est utilisé.</p>

    <h2>10. Modifications</h2>
    <p>BimoTech se réserve le droit de modifier la présente politique à tout moment. En cas de modification substantielle, vous serez notifié par email au moins 15 jours avant l'entrée en vigueur des changements.</p>

    <h2>11. Contact</h2>
    <p>Pour toute question relative à la protection de vos données personnelles :</p>
    <p><strong>Email :</strong> <a href="mailto:contact@bimotech.sn">contact@bimotech.sn</a><br>
    <strong>Adresse :</strong> BimoTech Immo, Dakar, Sénégal</p>
</div>

<footer>
    <div class="footer-logo">BimoTech Immo</div>
    <div class="footer-links">
        <a href="<?php echo e(url('/')); ?>">Accueil</a>
        <a href="<?php echo e(route('contact')); ?>">Contact</a>
        <a href="<?php echo e(route('mentions-legales')); ?>">Mentions légales</a>
    </div>
    <div class="footer-copy">© <?php echo e(date('Y')); ?> BimoTech · Dakar, Sénégal</div>
</footer>

</body>
</html><?php /**PATH C:\Users\ph\bimotech-immo\resources\views/confidentialite.blade.php ENDPATH**/ ?>