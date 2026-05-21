# PORTAIL_DECISIONS.md — Bimothèque Immo
## Portail public de recherche immobilière

---

## 1. Concept

Un portail public /biens où les biens disponibles
des agences abonnées s'affichent automatiquement.
- Bien sans contrat actif + statut disponible = affiché
- Bien avec contrat actif = retiré automatiquement
- Zéro action manuelle des agences
- Accessible à tous les plans (Starter inclus)
- Argument commercial principal pour les commerciaux

---

## 2. Groupe 1 — Migrations

- biens.visible_portail BOOLEAN DEFAULT TRUE
- biens.slug VARCHAR UNIQUE NULLABLE
- agencies.whatsapp VARCHAR NULLABLE
- Index composite contrats(bien_id, statut)
- Rattrapage slugs null :
  Str::slug(name).'-'.id pour toutes les agences existantes sans slug
  IMPORTANT : slug absent de $fillable sur Agency → utiliser
  DB::table('agencies') directement, pas $agency->update()

---

## 3. Groupe 2 — Modèle Bien

- booted() : slug auto généré au created sur
  type + quartier + ville + id, jamais recalculé
- scopePortail() :
  * Bien::withoutAgencyScope() — méthode dédiée du trait HasAgencyScope
    (équivalent de withoutGlobalScope(AgencyScope::class) mais utiliser
    la méthode existante, pas l'appel direct)
  * statut = disponible
  * visible_portail = true
  * whereDoesntHave(contratActif)
  * whereHas(agency, actif = true)
  * whereNotNull(titre)
  * whereNotNull(quartier)
  * whereHas(photos)
  * with([photos principale limitée à 1, agency])

---

## 4. Groupe 3 — Photos

- Compression GD native dans BienPhotoController@store
  max 1200px de large, JPEG 82%
  une photo de 3Mo devient ~180Ko
  Pas de lib externe — GD natif PHP 8.2, déjà disponible
- Image fallback OG : public/images/portail-og-default.jpg

---

## 5. Groupe 4 — Portail public

- PortailController@index : listing paginé 24 biens,
  filtres GET (type, quartier, prix_max, meublé, agence)
- PortailController@show : fiche bien avec withTrashed()
  pour gérer les slugs de biens supprimés/archivés
- Routes publiques /biens et /biens/{slug}
  zéro middleware
- Vue portail/index : cards photo principale
  ou placeholder, filtres, tri par date
- Vue portail/show : fiche complète + CTA WhatsApp
  prioritaire + Open Graph complet
  (og:title, og:description, og:image avec fallback, og:url)
- Vue portail/bien-indisponible : page propre
  "Ce bien n'est plus disponible" + lien retour portail
  Jamais de 404

---

## 6. Groupe 5 — Back-office

- BienController@show : bandeau amber si bien disponible
  mais absent du portail — liste exactement ce qui manque
  ("Il manque : un titre, une photo")
- Toggle visible_portail dans biens/edit (cas off-market)
- Dashboard agence : bandeau si des biens disponibles
  sont invisibles sur le portail + lien vers chaque bien à compléter
- Bouton "Copier mon lien portail" dans le dashboard
  génère /biens?agence={slug} en un clic
- AgencySettingsController : champ whatsapp avec
  validation format +221XXXXXXXXX
  message d'aide affiché sous le champ

---

## 7. Groupe 6 — Immeubles

- Titre fallback pour les unités sans titre :
  {immeuble.nom} — {type_label}
  C'est un accesseur dans Bien.php, pas une écriture en base
  La relation immeuble() existe déjà sur le modèle Bien

---

## 8. À faire après le lancement (non bloquant)

- Sitemap dynamique pour les fiches /biens/{slug}
- meta noindex sur toutes les pages /admin/*

---

## 9. Ordre d'implémentation

1. Migration (Groupe 1)
2. Modèle Bien — slug + scopePortail (Groupe 2)
3. Compression photos (Groupe 3)
4. PortailController + routes + vues (Groupe 4)
5. Back-office (Groupe 5)
6. Immeubles fallback (Groupe 6)
