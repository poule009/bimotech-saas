# Opportunités de marché non adressées

Ces 6 opportunités ne sont couvertes par aucun concurrent identifié au Sénégal.
Elles peuvent devenir des fonctionnalités BIMO-Tech ou des offres complémentaires.

---

## GAP 1 — Portail diaspora (opportunité immédiate)

### Le problème

Plus de 1,5 million de Sénégalais vivent à l'étranger (France, États-Unis, Italie,
Espagne, Canada). Une large partie possède des biens immobiliers au Sénégal gérés
par des proches ou des agences.

Aujourd'hui, le propriétaire expatrié :
- Appelle par téléphone pour savoir si le loyer est arrivé
- Reçoit des virements en FCFA sur un compte sénégalais qu'il ne peut pas consulter
  facilement depuis l'étranger
- Ne sait pas si le bail a été renouvelé, si le bien est en bon état
- Doit faire confiance à l'agence sans visibilité réelle

### Ce que BIMO-Tech pourrait offrir

**Portail propriétaire diaspora :**
- Dashboard accessible en euros ET en FCFA (taux de change temps réel)
- Historique des paiements avec relevé téléchargeable
- Alertes SMS/WhatsApp/email à chaque loyer encaissé
- Visualisation des documents (contrat, quittances) depuis Paris ou New York
- Rapport mensuel automatique par email en français

**Pour l'agence :**
- Argument commercial fort : "Vos bailleurs à l'étranger ont une visibilité complète"
- Fidélisation des propriétaires diaspora (segment premium, peu sensibles au prix)
- Différenciation immédiate face à tous les concurrents

### Effort de développement

Faible. Le portail propriétaire existe déjà dans BIMO-Tech. Il faudrait ajouter :
- Affichage du taux de change FCFA/EUR via API (exchangerate.host gratuit)
- Option de langue anglaise pour les portails
- Export relevé au format compatible banques européennes

### Potentiel commercial

Les agences qui gèrent des biens pour la diaspora sont nombreuses à Dakar (Almadies,
Sacré-Cœur, Mermoz). C'est un segment premium qui paie volontiers pour un service
professionnel.

---

## GAP 2 — Intégration directe DGID-digitale (opportunité stratégique)

### Le contexte

La DGID (Direction Générale des Impôts et Domaines) a lancé en janvier 2024 sa
plateforme de déclaration en ligne pour les revenus locatifs. Les propriétaires
et agences doivent y déclarer leurs baux et revenus.

En janvier 2025, la DGID a publié un communiqué sur une période de régularisation
pour les revenus locatifs non déclarés, avec des pénalités réduites.

La tendance est claire : le fisc sénégalais renforce le contrôle sur les revenus
immobiliers et va continuer à digitaliser.

### Ce que BIMO-Tech pourrait offrir

**Export DGID automatique :**
- Génération d'un fichier CSV/XML au format DGID à partir des données de BIMO-Tech
- Liste de tous les baux actifs avec loyers, durées, références
- Calcul des droits d'enregistrement dus
- Un bouton "Préparer ma déclaration DGID" dans le tableau de bord admin

**Si l'API DGID devient publique :**
- Dépôt automatique des déclarations de baux directement depuis BIMO-Tech
- Confirmation de réception avec numéro de quittance DGID récupéré automatiquement

### Pourquoi c'est stratégique

Le premier logiciel à proposer une intégration DGID sera la référence officielle
auprès des agences soucieuses de conformité. La DGID pourrait même recommander
l'outil à ses contribuables.

C'est aussi une barrière à l'entrée très forte : construire et maintenir une
intégration DGID demande une expertise locale que Logestimmo (multi-pays) n'a
probablement pas.

### Effort de développement

Moyen (si export seulement) à élevé (si API bidirectionnelle). L'export CSV/Excel
au format déclaration DGID est la première étape réaliste.

---

## GAP 3 — Export comptable SYSCOHADA (opportunité de fidélisation)

### Le problème

Chaque agence immobilière sénégalaise travaille avec un expert-comptable ou un
cabinet comptable. En fin de mois, l'agence doit fournir à son comptable :
- Les loyers encaissés
- Les commissions prélevées
- La TVA collectée
- Le BRS retenu
- Les charges déductibles

Aujourd'hui, les admins exportent les PDF BIMO-Tech et les retapent manuellement
dans Excel pour leur comptable. C'est du temps perdu et une source d'erreurs.

### Ce que BIMO-Tech pourrait offrir

**Export SYSCOHADA :**
SYSCOHADA est le plan comptable harmonisé de l'OHADA (17 pays d'Afrique francophone).
C'est le référentiel utilisé par tous les experts-comptables sénégalais.

Un export mensuel au format SYSCOHADA inclurait :
- Compte 706 (prestations de services — commissions agence)
- Compte 411 (clients — loyers à encaisser)
- Compte 445 (TVA collectée)
- Compte 4671 (BRS à reverser)
- Écritures de trésorerie (Wave, Orange Money, espèces)

**Format :** CSV importable dans les logiciels comptables (SAGE, EBP Compta, CEGID)

### Pourquoi c'est une fonctionnalité de fidélisation

Une agence qui peut envoyer son export mensuel SYSCOHADA en 2 clics à son comptable
ne changera jamais de logiciel. Le coût de migration devient prohibitif.

### Effort de développement

Faible à moyen. Les données sont déjà dans BIMO-Tech. Il faut mapper les transactions
vers les comptes SYSCOHADA et générer le fichier d'export.

---

## GAP 4 — Gestion des baux commerciaux avancée

### Le problème

Le marché des baux commerciaux sénégalais (bureaux, locaux commerciaux, entrepôts,
centres commerciaux) est en forte croissance mais aucun logiciel sénégalais ne
propose une gestion spécialisée.

Les spécificités des baux commerciaux non gérées par les concurrents :
- Indices de révision commerciaux (SYSCOHADA vs indice loyers)
- Droit au bail (valeur patrimoniale cédée entre locataires)
- Pas-de-porte (payment d'entrée non remboursable)
- Déspécialisation (changement d'activité du locataire)
- Renouvellement avec indemnité d'éviction

### Ce que BIMO-Tech pourrait offrir

BIMO-Tech a déjà le type de bail "commercial" avec TVA 18% automatique. La prochaine
étape serait d'ajouter des champs spécifiques aux baux commerciaux :
- Activité autorisée (clauses d'enseigne, d'exclusivité)
- Indice de révision contractuel
- Pas-de-porte payé à l'entrée (distinct de la caution)
- Droit au bail (valorisation si cession)

### Potentiel commercial

Les agences qui gèrent des locaux commerciaux (zones industrielles, centres commerciaux,
bâtiments de bureaux) ont des loyers plus élevés (= TVA + BRS plus importants)
et paient volontiers pour un outil adapté.

---

## GAP 5 — Assurance loyers impayés (modèle partenarial)

### Le concept

En France, la "Garantie des Loyers Impayés" (GLI) est une assurance que l'agence
souscrit au nom du propriétaire. En cas d'impayé, l'assureur rembourse les loyers.

Au Nigeria, RentSmallSmall a réussi à intégrer ce modèle avec des assureurs locaux
(Coronation Insurance, etc.), devenant ainsi plus qu'un logiciel.

### Au Sénégal

Les compagnies d'assurance locales actives (NSIA Sénégal, Allianz Sénégal, AXA,
Sonam, AMSA) n'ont pas encore de produit GLI structuré pour les agences immobilières.

### Ce que BIMO-Tech pourrait faire

1. **Partenariat avec un assureur** : proposer une GLI intégrée directement dans BIMO-Tech
2. **Screening locataire** : le module de profil locataire (taux d'effort, revenu mensuel,
   employeur) peut alimenter un score de solvabilité
3. **Commission d'apport** : BIMO-Tech perçoit une commission sur chaque contrat GLI
   souscrit via la plateforme — revenus additionnels sans développement lourd

### Impact

Ce serait une transformation du modèle de revenus : de SaaS pur à plateforme
de services financiers immobiliers. Fort potentiel mais nécessite des partenariats.

---

## GAP 6 — Gestion de copropriété UEMOA

### Le problème

La gestion des copropriétés (immeubles en syndic, charges communes, travaux, AG) est
un secteur entièrement vide en Afrique de l'Ouest. En France, Syndic One a capté une
part de marché significative. Au Maroc, VotreSyndic.ma est rentable.

À Dakar, les résidences fermées, les immeubles en étages et les lotissements se
multiplient. Les copropriétaires n'ont pas d'outil pour gérer :
- Les appels de charges mensuels
- Les travaux votés en AG
- Le carnet d'entretien
- La comptabilité du syndicat

### Ce que BIMO-Tech pourrait offrir

Module "Syndic BIMO-Tech" séparé ou intégré :
- Création d'une copropriété avec lots et tantièmes
- Appels de charges automatiques par lot
- Paiement via Wave/Orange Money
- Compte-rendu d'assemblée générale
- Suivi des travaux votés

### Effort de développement

Élevé — c'est presque un produit séparé. À envisager uniquement après avoir
consolidé la gestion locative.

---

## Tableau récapitulatif des opportunités

| Gap | Impact | Urgence | Effort | Recommandation |
|---|---|---|---|---|
| Portail diaspora | Fort | Immédiat | Faible | **Faire maintenant** |
| Export DGID | Très fort | Court terme | Moyen | Faire dans 3-6 mois |
| Export SYSCOHADA | Fort | Court terme | Faible | **Faire maintenant** |
| Baux commerciaux avancés | Moyen | Moyen terme | Moyen | Faire dans 6-12 mois |
| Assurance GLI | Très fort | Long terme | Faible (côté tech) | Partenariat à engager |
| Copropriété | Fort | Long terme | Très élevé | Reporter à v2.0 |
