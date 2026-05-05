# Positionnement de BIMO-Tech Immo

---

## Notre position en une phrase

**BIMO-Tech est le seul logiciel de gestion locative au Sénégal avec un moteur fiscal
complet : TVA 18%, BRS, conformité CGI, suivi DGID et contrats conformes loi 81-18.**

---

## Ce que personne d'autre ne fait : notre muraille

### 1. Le moteur fiscal sénégalais

C'est notre avantage le plus important et le plus difficile à copier rapidement.

**TVA 18% sur les loyers commerciaux (Art. 355 CGI SN)**
Quand une agence loue un bureau ou un local commercial, elle doit collecter la TVA
à 18% sur le loyer. BIMO-Tech calcule automatiquement :
- Le loyer HT
- La TVA à collecter
- Le total TTC facturé au locataire
- La TVA à reverser à la DGI

Aucun concurrent local ne fait ce calcul. Les agences le font à la main ou demandent
à leur comptable, ce qui coûte du temps et génère des erreurs.

**BRS — Retenue à la source (Art. 196bis CGI SN)**
Quand le locataire est une entreprise, il doit retenir 5% du loyer et le verser
directement à la DGI. C'est la Retenue à la Source sur les Baux (BRS).
BIMO-Tech calcule automatiquement le BRS, le déduit du net à verser au propriétaire
et génère la ligne dans la quittance fiscale.

Sans outil, les agences oublient souvent de mentionner le BRS, ce qui expose le
propriétaire à un redressement fiscal.

**Suivi DGID**
Chaque bail doit être enregistré à la DGID dans les 2 mois (Art. 442 CGI SN).
BIMO-Tech stocke :
- Le numéro de quittance DGID
- La date d'enregistrement
- Le montant du droit de bail payé
- L'exonération si applicable

C'est une fonctionnalité 100% sénégalaise qu'aucun concurrent ne propose.

**Calcul du droit de bail**
Le droit d'enregistrement est de 1% pour les baux d'habitation et 2% pour les
baux commerciaux (CGI SN). BIMO-Tech peut afficher ce montant dans le contrat formel.

### 2. Les contrats conformes loi 81-18

La loi sénégalaise n° 81-18 du 30 mars 1981 est spécifique au Sénégal. Nos contrats
incluent les 10 articles légaux obligatoires :

- Art. 1 : Désignation des lieux avec destination explicite (habitation/commercial)
- Art. 2 : Durée avec préavis chiffrés (3 mois bailleur / 1 mois preneur)
- Art. 3 : Loyer en chiffres et en lettres (NombreEnLettres service)
- Art. 4 : Dépôt de garantie avec clause séquestre
- Art. 5 : Obligations preneur (assurance obligatoire, interdiction cession)
- Art. 6 : Obligations bailleur
- Art. 7 : Résiliation avec préavis explicites selon type de bail
- Art. 8 : État des lieux contradictoire
- Art. 9 : Enregistrement DGID obligatoire dans 2 mois
- Art. 10 : Élection de domicile — juridiction de la ville du bien

La clause garant est qualifiée juridiquement (caution solidaire avec renonciation
au bénéfice de discussion et de division).

Logestimmo utilise le droit OHADA (régional, 17 pays). Notre contrat est spécifiquement
sénégalais et tient compte des particularités locales.

### 3. L'intégration PayTech

PayTech est la passerelle de paiement sénégalaise officielle. Nous sommes intégrés
en mode simulation/test/prod avec :
- Vérification HMAC-SHA256 des IPN (sécurité)
- Idempotence des transactions
- Modes Wave, Orange Money, Free Money, virement
- Gestion des abonnements agence en FCFA

Logestimmo n'a pas PayTech. Ils gèrent les paiements mobile money en direct sans
gateway intégrée, ce qui complique la réconciliation comptable.

---

## Ce qu'on a et que les concurrents ont aussi

### Architecture multi-tenant correcte
Chaque agence a ses données isolées (AgencyScope). Bien fait, pas différenciant
car c'est la norme attendue.

### Portails propriétaire et locataire
Logestimmo et Systimmo l'ont aussi. Notre portail locataire permet le téléchargement
des quittances et la consultation du contrat. Fonctionnel mais pas différenciant.

### Gestion des impayés et relances email
Standard du marché. Nous envoyons les relances, les concurrents aussi.

### Rapports financiers PDF
Nos rapports incluent les données fiscales détaillées. C'est un vrai plus mais
peu de concurrents mettent en avant leurs rapports, donc difficile de comparer.

---

## Ce que les concurrents ont et que nous n'avons pas encore

### WhatsApp automatisé — Lacune critique

**Pourquoi c'est important :**
Au Sénégal, WhatsApp est le canal de communication professionnel n°1. Les agences
communiquent avec leurs locataires via WhatsApp, pas par email. Logestimmo revendique
un taux d'ouverture de 98% sur WhatsApp vs ~20% pour l'email.

**Ce que nous manquons :**
- Envoi de quittances par WhatsApp
- Relances d'impayés par WhatsApp
- Notifications de renouvellement de bail par WhatsApp
- Confirmation de paiement par WhatsApp

**Impact business :**
Un locataire qui reçoit sa quittance par WhatsApp en 30 secondes vs un email qu'il
ne lira peut-être jamais — c'est une différence visible au quotidien pour l'agence.

### Application mobile native — Lacune importante

**Pourquoi c'est important :**
Les propriétaires et locataires sénégalais sont majoritairement sur mobile. Un admin
qui veut vérifier un paiement pendant un rendez-vous client a besoin d'une app,
pas d'ouvrir un navigateur sur un ordinateur.

**Ce que nous manquons :**
- App iOS/Android pour l'admin (consulter, valider paiements, ajouter un bien)
- App locataire (télécharger quittance, voir son solde)
- App propriétaire (voir ses revenus en temps réel)

Systimmo l'a. Logestimmo l'a partiellement. Nyumba Zetu (Kenya) l'a très bien fait.

### État des lieux numérique — Lacune fonctionnelle

**Pourquoi c'est important :**
À chaque entrée et sortie de locataire, l'agence doit établir un état des lieux
contradictoire avec photos. Sans outil, c'est fait sur papier ou Word.

**Ce que nous manquons :**
- Formulaire d'état des lieux avec pièces, équipements, état
- Upload de photos par pièce
- Comparaison entrée/sortie automatique
- Signature électronique ou simple (sur tablette)
- Intégration au contrat

Systimmo l'a. SGS Immo l'a partiellement. ImmoPad en a fait son cœur de métier.

### Intelligence artificielle — Lacune à moyen terme

**Pourquoi ça compte dans 12-18 mois :**
Logestimmo a lancé son "Expert IA". Même si la réalité est modeste, le marketing
autour de l'IA attire les agences qui veulent "moderniser" leur gestion.

**Ce qu'on pourrait faire :**
- Prédiction des risques d'impayés basée sur l'historique
- Suggestion automatique de révision de loyer selon l'indexation
- Résumé IA du dossier locataire avant signature

Ce n'est pas urgent aujourd'hui mais deviendra un argument dans les pitchs.

---

## Notre profil de client idéal (ICP)

L'agence qui bénéficie le plus de BIMO-Tech est celle qui :

1. **Gère des baux mixtes** (habitation + commercial) — elle a besoin du moteur TVA/BRS
2. **A des locataires entreprises** — le BRS est obligatoire, elle doit le calculer
3. **Veut être en règle avec la DGID** — les contrôles fiscaux augmentent au Sénégal
4. **Gère 10 à 100 biens** — assez grand pour avoir besoin d'un outil, assez petit pour
   ne pas avoir de DSI interne
5. **A des propriétaires qui veulent des comptes rendus** — notre portail bailleur répond

Ce profil correspond à la majorité des agences immobilières dakaroises sérieuses.

---

## Notre profil de client à éviter (pour l'instant)

1. **Propriétaire unique avec 1-2 biens** → Applicéo gratuit leur suffit
2. **Grande agence avec 500+ biens** → besoin d'un ERP, pas d'un SaaS léger
3. **Agences multi-pays** → Logestimmo répond mieux avec sa couverture 5 pays
4. **Agences en dehors du Sénégal** → notre fiscalité est 100% sénégalaise
