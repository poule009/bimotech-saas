# 📋 TODO — Corrections BimoTech Immo

> **Statut global : ✅ 25/25 tâches complétées**
> Dernière mise à jour : {{ date('d/m/Y') }}

---

## 🔴 Bugs Critiques — TOUS CORRIGÉS
- [x] 1. `config/app.php` → Locale `fr`, faker `fr_FR`, fallback_locale `fr`
- [x] 2. `routes/web.php` → Corruption NaN supprimée, toutes les routes présentes (subscription, contrats edit/update, users edit/update, rapports export-pdf)
- [x] 3. `GenerateMonthlyRent.php` → Corrigé : `$contrat->bien->taux_commission` + eager load `bien`
- [x] 4. `RapportController.php` → Fuite inter-agences corrigée : `User::where('agency_id', ...)` + `where('role', ...)`
- [x] 5. `PaiementController.php` → Données agence dynamiques via `Auth::user()->agency` (plus de hardcode)
- [x] 6. `BienController.php` → Référence robuste : préfixe agence + timestamp + uniqid (plus de `Bien::count()`)

## 🟠 Paiements & Abonnements — TOUS COMPLÉTÉS
- [x] 7. `app/Services/PaymentService.php` → Service complet : simulation/test/live, initierPaiement(), traiterCallbackIPN(), verifierStatutFacture()
- [x] 8. `SubscriptionController.php` → Injection PaymentService, routes initierPaiement/callbackPaydunya/succes/echec
- [x] 9. Migration `2026_04_10_000001` → ENUM mode_paiement étendu : wave, orange_money, free_money
- [x] 10. `StorePaiementRequest.php` → Validation mise à jour : wave/orange_money/free_money inclus
- [x] 11. Vue `subscription/index.blade.php` → Route corrigée `subscription.initier`, plans FCFA, historique paiements

## 🟡 Modèles & Migrations — TOUS COMPLÉTÉS
- [x] 12. Migration `2026_04_10_000002` → Champs `biens` : quartier, commune, meuble (boolean)
- [x] 13. Migration `2026_04_10_000003` → Champs `contrats` : type_bail, frais_agence, charges_mensuelles, indexation_annuelle, nombre_mois_caution, garant_nom/telephone/adresse
- [x] 14. `Bien.php` → fillable mis à jour + constantes TYPES, STATUTS
- [x] 15. `Contrat.php` → fillable mis à jour + constantes TYPES_BAIL, STATUTS
- [x] 16. `Paiement.php` → Constantes MODES_PAIEMENT (Wave/OM/Free/Espèces/Virement/Chèque) + STATUTS

## 🟡 Contrôleurs — Fonctionnalités manquantes — TOUTES AJOUTÉES
- [x] 17. `ContratController.php` → `edit()` + `update()` avec validation complète (type_bail, garant, charges, indexation)
- [x] 18. `UserController.php` → `edit()` + `update()` avec profil propriétaire/locataire ; genre corrigé `M/F` ; mode_paiement_prefere étendu
- [x] 19. `RapportController.php` → `exportPdf()` via DomPDF, rapport A4 paysage avec KPIs, détail paiements, récap par propriétaire, biens impayés
- [x] 20. `BienController.php` → Validation mise à jour : quartier, commune, meuble, type libre

## 🔵 Qualité & Sécurité — TOUTES COMPLÉTÉES
- [x] 21. `app/Rules/TelephoneSenegalais.php` → Validation numéros sénégalais (70/75/76/77/78/33/30)
- [x] 22. `resources/views/errors/404.blade.php` → Page 404 personnalisée (Tailwind, français)
- [x] 23. `resources/views/errors/403.blade.php` → Page 403 personnalisée (Tailwind, français)
- [x] 24. `resources/views/errors/500.blade.php` → Page 500 personnalisée (Tailwind, français)
- [x] 25. `resources/views/rapports/financier_pdf.blade.php` → Vue PDF complète (en-tête agence, KPIs, tableau paiements, récap propriétaires, biens impayés, pied de page)

---

## 🆕 Corrections supplémentaires (hors scope initial)
- [x] 26. `users/edit.blade.php` → `@method('PATCH')` (était `PUT`, mismatch avec la route)
- [x] 27. `UserController::store()` → genre `M/F` (était `homme/femme`, incohérent avec la BDD)
- [x] 28. `Paiement.php` → Doublon de constantes supprimé (MODES_PAIEMENT + STATUTS dupliqués)
- [x] 29. `resources/views/subscription/succes.blade.php` → Page retour PayDunya succès
- [x] 30. `resources/views/subscription/echec.blade.php` → Page retour PayDunya échec/annulation
- [x] 31. `resources/views/admin/contrats/edit.blade.php` → Formulaire édition contrat complet
- [x] 32. `config/services.php` → Bloc configuration PayDunya (mode/master_key/private_key/token)
- [x] 33. `app/Models/SubscriptionPayment.php` → METHODE_LABELS étendu (free_money, simulation)

---

## 📌 Prochaines étapes recommandées
- [ ] A. Intégrer les vraies clés PayDunya dans `.env` et passer `PAYDUNYA_MODE=live`
- [ ] B. Ajouter la vue `resources/views/biens/create.blade.php` avec les nouveaux champs (quartier, commune, meuble)
- [ ] C. Ajouter la vue `resources/views/admin/contrats/create.blade.php` avec les nouveaux champs (type_bail, garant, charges)
- [ ] D. Exécuter `php artisan migrate` pour appliquer les 3 nouvelles migrations
- [ ] E. Ajouter des tests Feature pour les nouvelles routes (edit/update contrats, users, export PDF)
- [ ] F. Implémenter la notification `AgencyWelcomeNotification` à l'envoi lors de la création d'agence
