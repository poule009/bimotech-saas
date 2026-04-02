# TODO - Correction RelanceImpayeNotification

- [x] Remplacer `app/Notifications/RelanceImpayeNotification.php` par une vraie classe Notification (namespace `App\Notifications`)
- [x] Garder la signature du constructeur compatible avec `new RelanceImpayeNotification($contrat, $periode)`
- [x] Implémenter les canaux + contenu email de relance impayé
- [ ] Vérifier la syntaxe PHP du fichier
- [ ] Confirmer la résolution de l'erreur Intelephense P1009
