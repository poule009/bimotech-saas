# PRICING_DECISIONS.md — Bimothèque Immo
## Document de référence pour tous les chantiers pricing

---

## 1. Contexte
Bimothèque Immo est une plateforme Laravel de gestion immobilière 
ciblant les agences et propriétaires sénégalais. Déployée sur Hetzner 
via Laravel Forge. Le modèle de données central est Immeuble → Unités.

---

## 2. Décision tarifaire

Métrique choisie : nombre d'unités actives (biens dont statut != 'archive').

| Plan    | Unités    | Mensuel       | Annuel ("10+2 offerts") |
|---------|-----------|---------------|--------------------------|
| Starter | ≤ 15      | 19 900 FCFA   | 199 000 FCFA             |
| Pro ⭐  | ≤ 50      | 39 900 FCFA   | 399 000 FCFA             |
| Agence  | Illimité  | 69 900 FCFA   | 699 000 FCFA             |
| Legacy  | ≤ 50      | 25 000 FCFA   | (clients existants only) |

Le plan Pro est positionné "Recommandé" dans l'UI.

---

## 3. Refactor Subscription (décision structurelle critique)

Le champ `plan` actuel stocke la durée (mensuel/trimestriel/annuel).
Il faut ajouter `plan_niveau` pour le niveau d'accès.

Deux champs distincts après refactor :
- `plan_niveau` : starter | pro | agence | legacy
- `plan` : mensuel | trimestriel | annuel (inchangé)

Tous les clients existants → plan_niveau = 'legacy' par défaut.

---

## 4. Features par plan (basé sur le code existant)

| Feature                          | Starter | Pro | Agence |
|----------------------------------|---------|-----|--------|
| Dashboard + graphiques 12 mois   | ✅      | ✅  | ✅     |
| Biens + contrats + paiements     | ✅      | ✅  | ✅     |
| Locataires + propriétaires       | ✅      | ✅  | ✅     |
| Impayés + relances               | ✅      | ✅  | ✅     |
| PDF quittances                   | ✅      | ✅  | ✅     |
| Gestion immeubles (Immeuble→Unités) | —   | ✅  | ✅     |
| Rapport financier mensuel PDF    | —       | ✅  | ✅     |
| Export CSV paiements             | —       | ✅  | ✅     |
| Relevé PDF par propriétaire      | —       | ✅  | ✅     |
| Recherche globale (topbar)       | —       | ✅  | ✅     |
| Import Excel                     | —       | ✅  | ✅     |
| Contrat de bail formel PDF       | —       | ✅  | ✅     |
| Déclarations DGID (BRS,TVA,IRPP) | —       | —   | ✅     |
| Bilans fiscaux PDF               | —       | —   | ✅     |
| Logs d'activité                  | —       | —   | ✅     |
| Support prioritaire              | —       | —   | ✅     |

Legacy = mêmes accès que Pro.

---

## 5. Comptage des unités

Méthode à ajouter dans Agency.php :
- Compter tous les biens dont statut != 'archive'
- Un bien archivé = non facturé = non compté

---

## 6. Parcours d'upsell (4 moments)

- À 80% de limite → bandeau amber discret dans le dashboard
- Limite atteinte → modal d'upgrade (jamais un bloc brutal)
- Fin d'essai J-5 → WhatsApp + email avec récap personnalisé
- Navigation vers feature locked → bandeau contextuel à l'endroit exact

Règle absolue : les biens existants continuent de fonctionner.
On ne dégrade jamais le service en cours d'abonnement.

---

## 7. Migration clients existants

- Tous les abonnés actuels (25 000 FCFA) → plan_niveau = 'legacy'
- Accès Pro maintenu pendant 6 mois au prix actuel
- Communication via WhatsApp direct, ton informatif pas commercial
- Au mois 6 : invitation à choisir leur plan définitif

---

## 8. Essai gratuit

- 30 jours, accès Pro complet
- Aucune carte bancaire demandée à l'inscription
- date_fin_essai existe déjà dans le code

---

## 9. Paiements

PayTech gère déjà Wave, Orange Money, Free Money, Visa.
Changement UI uniquement : afficher les logos Wave et Orange Money 
explicitement sur la page abonnement.
Pour les paiements annuels Agence (699 000 FCFA) : prévoir option 
virement bancaire + activation manuelle SuperAdmin.

---

## 10. Programme de parrainage

- Parrain : +1 mois offert quand le filleul passe en payant
- Filleul : essai 45j au lieu de 30j
- Code basé sur le slug de l'agence, visible dans les paramètres
- Déclenchement uniquement à la conversion payante (pas pendant l'essai)

---

## 11. Ordre d'implémentation validé

1. Refactor modèle Subscription (plan_niveau)
2. Compteur nbUnitesActives dans Agency
3. Middleware CheckPlanFeature + config/plans.php
4. Bandeau avertissement à 80% de limite
5. Modal de blocage à la limite
6. Page pricing publique
7. Migration Legacy clients existants
8. Programme de parrainage

---

## 12. Ce qui n'est PAS encore validé terrain

- Seuil 15 unités : adapté à la réalité des agences dakaroises ?
- 39 900 FCFA/mois : perçu comme raisonnable ?
- Module fiscal DGID : vraie douleur ou géré par comptable externe ?
- Paiement annuel : intéressant pour les agences sénégalaises ?
