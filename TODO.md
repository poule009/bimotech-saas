# BIMO-Tech — Liste des tâches

## Avant le lancement commercial

- [ ] Renseigner les clés PayDunya live dans `.env` (`PAYDUNYA_MODE=live` + 3 clés)
- [ ] Configurer l'hébergeur : worker queue `php artisan queue:work --daemon`
- [ ] Configurer le cron sur l'hébergeur : `* * * * * php /chemin/artisan schedule:run >> /dev/null 2>&1`
- [ ] Changer `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`
- [ ] Configurer le mail de production (Brevo / Mailgun) dans `.env`
- [x] Tester le PDF quittance en production  → couvert par QuittancePdfTest (8 tests : admin/proprio/locataire, 403 annulé, taille PDF > 10 Ko)
- [x] Tester le paiement PayDunya end-to-end en sandbox → couvert par PayDunyaSandboxTest (18 tests : simulation, Http::fake() sandbox, IPN idempotent, succes/echec)
