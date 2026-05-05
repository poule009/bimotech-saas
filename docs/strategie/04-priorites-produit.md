# Priorités produit — Ce qu'il faut construire et dans quel ordre

Basé sur l'analyse concurrentielle de mai 2026.

---

## Principe de priorisation

Chaque fonctionnalité est évaluée sur 3 critères :
- **Impact commercial** : est-ce que ça aide à vendre ou à fidéliser ?
- **Défense concurrentielle** : est-ce que ça empêche un concurrent de nous copier ?
- **Effort de développement** : combien de temps pour le faire correctement ?

---

## PRIORITÉ 1 — WhatsApp automatisé (à faire en premier)

### Pourquoi c'est urgent

Logestimmo en fait son argument principal. Au Sénégal, WhatsApp est utilisé
professionnellement par 90%+ des acteurs immobiliers. Un locataire qui reçoit
sa quittance par WhatsApp en 30 secondes vs un email lu 3 jours plus tard —
c'est visible, concret, et l'agence voit la différence au quotidien.

### Ce qu'il faut construire

**Phase 1 — Notifications sortantes (4-6 semaines)**

Utiliser l'API WhatsApp Business (Meta) ou un provider comme Twilio/Bird/Wati :
- Envoi de quittance PDF en pièce jointe WhatsApp après chaque paiement validé
- Rappel de loyer J-3 avant échéance avec montant et lien de paiement
- Confirmation de paiement reçu ("Votre loyer de mai 2026 a été encaissé")
- Alerte de bail expirant dans 30 jours

**Phase 2 — Chatbot locataire simple (8-12 semaines)**

Chatbot WhatsApp permettant au locataire de :
- Taper "QUITTANCE" pour recevoir la dernière quittance PDF
- Taper "SOLDE" pour voir son statut de paiement
- Taper "CONTRAT" pour recevoir son contrat PDF

**Architecture recommandée :**
- Provider : Twilio WhatsApp API ou Wati (interface simple, pas besoin de webhook complexe)
- Stocker le numéro WhatsApp dans le profil Locataire (champ déjà prévu : `telephone`)
- Queue Laravel pour les envois asynchrones (déjà en place avec `QUEUE_CONNECTION=database`)
- Log des envois dans ActivityLog

### Effort estimé

Phase 1 : 3-4 semaines de développement
Phase 2 : 6-8 semaines supplémentaires

### Coût

Twilio WhatsApp : ~0,005$ à 0,008$ par message envoyé (très faible)
WATI (solution clé en main) : ~50$/mois pour 1 000 conversations

---

## PRIORITÉ 2 — Export SYSCOHADA (gain immédiat de fidélisation)

### Pourquoi c'est urgent

Toutes les agences immobilières sénégalaises ont un comptable ou un expert-comptable.
Aujourd'hui, l'admin télécharge des PDF et les retape dans Excel. C'est du temps perdu
toutes les fins de mois.

Un export SYSCOHADA = fidélisation maximale. Une agence qui donne ses données BIMO-Tech
directement à son comptable ne changera jamais de logiciel.

### Ce qu'il faut construire

**Export mensuel SYSCOHADA (2-3 semaines)**

Un bouton "Exporter en comptabilité" dans le rapport financier mensuel.
Génère un CSV avec les colonnes :

```
Date | Journal | Compte débit | Compte crédit | Montant | Libellé | Référence
```

Exemples d'écritures générées :
- Encaissement loyer Wave :
  `| 01/05/2026 | BQ | 521 (Banque) | 411 (Locataire) | 250 000 | Loyer mai - BIMO-2026-00123 |`
- TVA collectée :
  `| 01/05/2026 | OD | 411 | 445 (TVA collectée) | 45 000 | TVA loyer commercial |`
- Commission agence :
  `| 01/05/2026 | OD | 411 | 706 (Prestations) | 25 000 | Commission 10% - BIMO-2026-00123 |`

**Comptes SYSCOHADA à mapper :**

| Nature | Compte SYSCOHADA |
|---|---|
| Loyers encaissés | 706 (Produits des activités annexes) |
| TVA collectée | 4431 ou 445 |
| BRS à reverser DGI | 4467 |
| Clients (locataires) | 411 |
| Caution reçue | 1671 (Dettes envers déposants) |
| Honoraires agence | 706 |
| Trésorerie Wave | 5201 |
| Trésorerie Orange Money | 5202 |
| Trésorerie espèces | 571 |

### Effort estimé

2-3 semaines. Les données sont déjà dans BIMO-Tech. C'est principalement un
travail de mapping et de génération CSV.

---

## PRIORITÉ 3 — État des lieux numérique (combler un vrai manque)

### Pourquoi c'est important

L'état des lieux est une obligation légale à chaque entrée et sortie de locataire.
Sans outil, les agences le font sur papier ou Word avec des photos prises au téléphone
et envoyées sur WhatsApp. C'est désorganisé, litigieux et chronophage.

Systimmo l'a. ImmoPad a bâti toute son entreprise dessus. C'est une fonctionnalité
attendue pour les agences professionnelles.

### Ce qu'il faut construire

**Module état des lieux (6-8 semaines)**

Formulaire d'état des lieux relié au contrat :
- Création d'un état des lieux "entrée" ou "sortie" lié à un contrat
- Structure : pièces (salon, chambre 1, cuisine, salle de bain, WC) + équipements
- Pour chaque pièce : état (neuf / bon / moyen / mauvais) + observations + photos
- Upload de photos directement dans BIMO-Tech (S3 ou local storage)
- Comparaison automatique entrée/sortie avec différences mises en évidence
- Export PDF de l'état des lieux signé (cases signature) avec photos intégrées
- Stockage dans le dossier locataire

**Signature (simple dans un premier temps) :**
- Cases de signature vides dans le PDF (à imprimer et signer physiquement)
- Ou signature électronique simple via API DocuSign/Yousign (v2)

### Effort estimé

6-8 semaines avec upload photos et PDF. 3-4 semaines si on commence sans photos.

---

## PRIORITÉ 4 — Application mobile (investissement à moyen terme)

### Pourquoi c'est important

La majorité des propriétaires et locataires sénégalais utilisent leur smartphone
comme seul outil numérique. Une application mobile améliore drastiquement l'expérience
des utilisateurs qui ne sont pas devant un ordinateur.

### Approche recommandée

**Ne pas partir de zéro : Progressive Web App (PWA)**

BIMO-Tech est déjà une PWA (manifest.json + service worker présents dans le code).
La PWA permet d'installer l'appli depuis le navigateur mobile, sans passer par
l'App Store ou Google Play.

Ce qu'il faut améliorer pour une expérience mobile excellente :
- Vues responsive optimisées pour petit écran (certaines vues admin sont trop larges)
- Mode hors ligne pour la consultation des quittances récentes
- Notifications push (loyer encaissé, bail expirant)

**Si budget pour une vraie app native :**
- React Native ou Flutter — partage de code iOS/Android
- MVP : app locataire uniquement (télécharger quittances, voir contrat, payer loyer)
- Phase 2 : app admin (ajouter paiement, voir tableau de bord)

### Effort estimé

PWA améliorée : 3-4 semaines
App native Flutter MVP : 10-14 semaines

---

## PRIORITÉ 5 — Portail diaspora (fonctionnalité premium)

### Ce qu'il faut construire (faible effort, fort impact marketing)

Le portail propriétaire existe déjà. Ajouter :

1. **Taux de change FCFA/EUR en temps réel** sur le dashboard propriétaire
   - API : exchangerate.host (gratuit) ou currencylayer (5$/mois)
   - Afficher : "Vos revenus ce mois : 150 000 FCFA ≈ 229 €"

2. **Rapport mensuel automatique par email**
   - Email envoyé le 5 de chaque mois : loyers encaissés, commissions, net reçu
   - En français avec option anglais (pour la diaspora anglophone)

3. **Option "accès propriétaire international"** dans les paramètres agence
   - Activer le taux de change sur le portail propriétaire
   - L'agence peut valoriser cette option comme service premium pour ses bailleurs

### Effort estimé

2-3 semaines. La majorité des composants existent déjà.

---

## PRIORITÉ 6 — Export DGID (différenciateur fort à 3-6 mois)

### Ce qu'il faut construire

Un export structuré pour la déclaration DGID des revenus locatifs :

1. **Format CSV déclaration revenus locatifs**
   - Colonnes : NIF propriétaire, référence bail, adresse bien, loyer annuel, TVA, BRS
   - Compatible avec les formulaires DGID (à vérifier auprès de la DGID)

2. **Bouton "Préparer déclaration DGID" dans le bilan fiscal propriétaire**
   - Génère le fichier pour l'année fiscale sélectionnée
   - Récapitulatif : nombre de baux, total revenus, total TVA, total BRS

3. **Page d'aide "Comment déclarer à la DGID"**
   - Guide étape par étape dans l'interface BIMO-Tech
   - Lien vers la plateforme DGID officielle

### Effort estimé

3-4 semaines (export) + 1 semaine (guide)

---

## Roadmap suggérée (12 mois)

```
Trimestre 1 (mai - juillet 2026)
├── WhatsApp notifications sortantes (quittances + rappels)
├── Export SYSCOHADA (CSV comptable mensuel)
└── Portail diaspora (taux de change + rapport mensuel)

Trimestre 2 (août - octobre 2026)
├── État des lieux numérique (sans photos d'abord)
├── Export DGID
└── WhatsApp chatbot locataire (phase 2)

Trimestre 3 (novembre 2026 - janvier 2027)
├── État des lieux avec photos intégrées
├── PWA mobile améliorée (responsive + notifications push)
└── Module baux commerciaux avancé

Trimestre 4 (février - avril 2027)
├── App mobile native Flutter (MVP locataire)
├── Module copropriété (si demande validée)
└── Partenariat assurance GLI (exploration)
```
