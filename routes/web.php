<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AgencySettingsController;
use App\Http\Controllers\Admin\BilanFiscalController;
use App\Http\Controllers\Admin\ChargeAgenceController;
use App\Http\Controllers\Admin\ComptabiliteController;
use App\Http\Controllers\Admin\EcheancesFiscalesController;
use App\Http\Controllers\Admin\EtatTrimestrielController;
use App\Http\Controllers\Admin\FiscalDashboardController;
use App\Http\Controllers\Admin\ReversementController;
use App\Http\Controllers\Admin\TvaAgenceController;
use App\Http\Controllers\Auth\AgencyRegistrationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\BailleurController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\DepenseGestionController;
use App\Http\Controllers\BienPhotoController;
use App\Http\Controllers\ImmeubleController;
use App\Http\Controllers\ContratController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\LocataireDashboardController;
use App\Http\Controllers\Dashboard\ProprietaireDashboardController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\ImpayeController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\TwoFactorController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Routes sans session (health + sitemap) ────────────────────────────────
// Exclues du middleware StartSession pour fonctionner même si la DB est indisponible.
Route::withoutMiddleware([\Illuminate\Session\Middleware\StartSession::class])
    ->group(function () {

Route::get('/health', function () {
    $checks = [];

    // Vérification base de données
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        $checks['database'] = 'ok';
    } catch (\Throwable) {
        $checks['database'] = 'error';
    }

    // Vérification cache
    try {
        \Illuminate\Support\Facades\Cache::put('health_check', true, 5);
        $checks['cache'] = \Illuminate\Support\Facades\Cache::get('health_check') ? 'ok' : 'error';
    } catch (\Throwable) {
        $checks['cache'] = 'error';
    }

    $allOk  = collect($checks)->every(fn($v) => $v === 'ok');
    $status = $allOk ? 200 : 503;

    return response()->json([
        'status'  => $allOk ? 'healthy' : 'degraded',
        'checks'  => $checks,
        'version' => config('app.version', '1.0'),
    ], $status);
})->name('health');


}); // fin groupe withoutMiddleware(StartSession)

// ── Pages publiques ────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/contact',          fn() => view('contact'))->name('contact');
Route::get('/demo',             fn() => view('demo'))->name('demo');
Route::get('/faq',              fn() => view('faq'))->name('faq');
Route::get('/mentions-legales', fn() => view('mentions-legales'))->name('mentions-legales');
Route::get('/confidentialite',  fn() => view('confidentialite'))->name('confidentialite');
Route::get('/pricing',    [\App\Http\Controllers\PricingController::class,  'index'])->name('pricing');
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class,  'index'])->name('sitemap');
Route::get('/portail',      [\App\Http\Controllers\PortailController::class, 'home'])->name('portail.home');
Route::get('/biens',        [\App\Http\Controllers\PortailController::class, 'index'])->name('portail.index');
Route::get('/biens/quartier/{quartier}', [\App\Http\Controllers\PortailController::class, 'quartier'])
     ->name('portail.quartier')
     ->where('quartier', '[^/]+');
Route::get('/biens/{slug}',   [\App\Http\Controllers\PortailController::class, 'show'])->name('portail.show');
Route::get('/agences/{slug}', [\App\Http\Controllers\PortailController::class, 'agence'])->name('portail.agence');
Route::post('/contact', [ContactController::class, 'send'])->middleware('throttle:5,60')->name('contact.send');
Route::post('/demo',    [DemoController::class,    'send'])->middleware('throttle:5,60')->name('demo.send');

// ── Inscription agence (invités) ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register/agency',  [AgencyRegistrationController::class, 'create'])->name('agency.register');
    Route::post('/register/agency', [AgencyRegistrationController::class, 'store'])->middleware('throttle:5,60')->name('agency.register.store');

    // Étape finale inscription Google (nom d'agence)
    Route::get('/register/google/complete',  [GoogleAuthController::class, 'showComplete'])->name('agency.register.google.complete');
    Route::post('/register/google/complete', [GoogleAuthController::class, 'storeComplete'])->name('agency.register.google.store');
});

// ── Google OAuth (hors middleware guest pour que le callback fonctionne) ───
Route::get('/auth/google',          [GoogleAuthController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// ── Zone authentifiée ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/home',      [RedirectController::class, 'index'])->name('redirect.home');
    Route::get('/dashboard', [RedirectController::class, 'index'])->name('dashboard');

    Route::get('/profile',             [ProfileController::class,  'edit'])->name('profile.edit');
    Route::patch('/profile',           [ProfileController::class,  'update'])->name('profile.update');
    Route::delete('/profile',          [ProfileController::class,  'destroy'])->name('profile.destroy');
    Route::put('/profile/password/set', [\App\Http\Controllers\Auth\PasswordController::class, 'set'])->name('password.set');

    // ── Abonnements ────────────────────────────────────────────────────────
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/',         [SubscriptionController::class, 'index'])->name('index');
        // Déclaration de paiement manuelle (Wave/OM/virement + justificatif)
        Route::get('declarer',  [SubscriptionController::class, 'declarer'])->name('declarer');
        Route::post('declarer', [SubscriptionController::class, 'store'])->name('store');
        Route::post('initier',  [SubscriptionController::class, 'initierPaiement'])->name('initier');
        Route::post('callback', [SubscriptionController::class, 'callbackPaytech'])
            ->name('callback')
            ->withoutMiddleware(['auth', 'verified'])
            ->middleware('throttle:10,1'); // 10 req/min — PayTech légitime n'en envoie pas plus
        Route::get('succes',            [SubscriptionController::class, 'succes'])->name('succes');
        Route::get('echec',             [SubscriptionController::class, 'echec'])->name('echec');
        Route::get('upgrade-required',  function () {
            $requiredPlan = session('required_plan');
            return view('subscription.upgrade-required', compact('requiredPlan'));
        })->name('upgrade-required');
    });

    // Stop impersonation — hors isSuperAdmin (l'user courant est alors admin/locataire/proprio)
    Route::get('superadmin/impersonate/stop', [SuperAdminController::class, 'stopImpersonation'])
        ->name('superadmin.impersonate.stop');

    // ── SuperAdmin ─────────────────────────────────────────────────────────
    Route::middleware('isSuperAdmin')->prefix('superadmin')->name('superadmin.')->group(function () {

        // ── Routes 2FA challenge — sans require2fa (évite la boucle infinie) ──
        Route::get('2fa/challenge',  [TwoFactorController::class, 'showChallenge'])->name('2fa.challenge');
        Route::post('2fa/challenge', [TwoFactorController::class, 'verifyChallenge'])->name('2fa.verify')->middleware('throttle:5,10');

        // ── Routes protégées par require2fa ────────────────────────────────
        Route::middleware('require2fa')->group(function () {
            Route::get('dashboard',                     [SuperAdminController::class, 'dashboard'])->name('dashboard');
            Route::get('subscriptions',                 [SuperAdminController::class, 'subscriptions'])->name('subscriptions');
            // Déclarations de paiement manuelles à valider (back-office BIMO-tech)
            Route::get('paiements-attente',                     [SuperAdminController::class, 'paiementsAttente'])->name('paiements.attente');
            Route::post('paiements/{payment}/confirmer',        [SuperAdminController::class, 'confirmerPaiement'])->name('paiements.confirmer');
            Route::post('paiements/{payment}/rejeter',          [SuperAdminController::class, 'rejeterPaiement'])->name('paiements.rejeter');
            Route::get('activity-logs',                 [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('agencies/create',               [SuperAdminController::class, 'createAgency'])->name('agencies.create');
            Route::post('agencies',                     [SuperAdminController::class, 'storeAgency'])->name('agencies.store');
            Route::patch('agencies/{agency}/toggle',    [SuperAdminController::class, 'toggleActif'])->name('agencies.toggle');
            Route::get('agencies/{agency}/edit',        [SuperAdminController::class, 'editAgency'])->name('agencies.edit');
            Route::patch('agencies/{agency}',           [SuperAdminController::class, 'updateAgency'])->name('agencies.update');
            Route::post('agencies/{agency}/abonnement',           [SuperAdminController::class, 'activerAbonnement'])->name('agencies.abonnement.activer');
            Route::post('agencies/{agency}/essai',                [SuperAdminController::class, 'reinitialiserEssai'])->name('agencies.essai.reinitialiser');
            Route::post('agencies/{agency}/features/{feature}',   [SuperAdminController::class, 'toggleFeature'])->name('agencies.features.toggle');
            Route::delete('agencies/{agency}/features/{feature}', [SuperAdminController::class, 'removeFeatureOverride'])->name('agencies.features.remove');
            Route::post('agencies/{agency}/users/{userId}/reset-password', [SuperAdminController::class, 'resetUserPassword'])->name('agencies.users.reset-password');
            Route::patch('agencies/{agency}/users/{userId}/toggle',        [SuperAdminController::class, 'toggleUser'])->name('agencies.users.toggle');
            Route::post('impersonate/{user}',           [SuperAdminController::class, 'impersonate'])->name('impersonate');
            Route::get('agencies/{agency}',             [SuperAdminController::class, 'showAgency'])->name('agencies.show');

            // ── Gestion 2FA (setup, disable, régénération) ─────────────────
            Route::get('2fa/setup',                             [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
            Route::post('2fa/setup',                            [TwoFactorController::class, 'confirmSetup'])->name('2fa.confirm');
            Route::post('2fa/disable',                          [TwoFactorController::class, 'disable'])->name('2fa.disable');
            Route::post('2fa/recovery-codes/regenerate',        [TwoFactorController::class, 'regenerateCodes'])->name('2fa.recovery-codes.regenerate');
        });
    });

    // ── Routes accessibles admin ET propriétaires (isStaff) ───────────────
    // ── Admin agence — écriture uniquement ────────────────────────────────
    // IMPORTANT : ce groupe doit être déclaré AVANT le groupe isStaff
    // pour que les routes spécifiques (create, store…) soient enregistrées
    // avant les routes paramétrées ({contrat}, {paiement}…).
    Route::middleware(['isAdmin', 'force.password'])->prefix('admin')->name('admin.')->group(function () {

        Route::get('dashboard', AdminDashboardController::class)->name('dashboard');
        Route::view('demo/rechercher-creer', 'demo-search-create')->name('demo.search-create'); // démo composant (temporaire)
        Route::post('onboarding/dismiss', [AgencySettingsController::class, 'dismissOnboarding'])->name('onboarding.dismiss');
        Route::get('search', \App\Http\Controllers\SearchController::class)->name('search')->middleware('check.feature:recherche_globale');

        // Paramètres agence
        Route::get('agency/settings',   [AgencySettingsController::class, 'edit'])->name('agency.settings');
        Route::patch('agency/settings', [AgencySettingsController::class, 'update'])->name('agency.settings.update');
        Route::delete('agency/logo',      [AgencySettingsController::class, 'deleteLogo'])->name('agency.logo.delete');
        Route::delete('agency/logo-dark', [AgencySettingsController::class, 'deleteLogoDark'])->name('agency.logo-dark.delete');
        Route::delete('agency/signature', [AgencySettingsController::class, 'deleteSignature'])->name('agency.signature.delete');
        Route::delete('agency/cachet',    [AgencySettingsController::class, 'deleteCachet'])->name('agency.cachet.delete');

        // Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index')->middleware(['check.feature:logs_activite', 'agency.can:logs.lire']);

        // Paiements — écriture
        Route::get('paiements/dernier-periode/{contrat}', [PaiementController::class, 'dernierePeriode'])->name('paiements.dernier-periode');
        if (config('features.fiscalite')) {
            Route::get('paiements/fiscal-preview/{contrat}', [PaiementController::class, 'fiscalPreview'])->name('paiements.fiscal-preview')->middleware('check.feature:fiscalite');
        }
        Route::get('paiements/create',               [PaiementController::class, 'create'])->name('paiements.create')->middleware('agency.can:paiements.creer');
        Route::post('paiements',                     [PaiementController::class, 'store'])->name('paiements.store')->middleware('agency.can:paiements.creer');
        Route::patch('paiements/{paiement}/annuler', [PaiementController::class, 'annuler'])->name('paiements.annuler')->middleware('agency.can:paiements.annuler');
        Route::patch('paiements/{paiement}/marquer-paye', [PaiementController::class, 'marquerPaye'])->name('paiements.marquer-paye')->middleware('agency.can:paiements.creer');

        // Contrats — écriture
        Route::post('contrats/locataire-rapide', [ContratController::class, 'storeLocataireRapide'])->name('contrats.locataire-rapide')->middleware(['throttle:20,1', 'agency.can:locataires.creer']);
        Route::get('contrats/create',            [ContratController::class, 'create'])->name('contrats.create')->middleware('agency.can:contrats.creer');
        Route::post('contrats',                  [ContratController::class, 'store'])->name('contrats.store')->middleware('agency.can:contrats.creer');
        Route::get('contrats/{contrat}/edit',    [ContratController::class, 'edit'])->name('contrats.edit')->middleware('agency.can:contrats.modifier');
        Route::put('contrats/{contrat}',         [ContratController::class, 'update'])->name('contrats.update')->middleware('agency.can:contrats.modifier');
        Route::delete('contrats/{contrat}',      [ContratController::class, 'destroy'])->name('contrats.destroy')->middleware('agency.can:contrats.supprimer');

        // Impayés — écriture (relance + export)
        Route::post('impayes/{contrat}/relance', [ImpayeController::class, 'relance'])->name('impayes.relance')->middleware('agency.can:impayes.relance');
        Route::get('impayes/export',             [ImpayeController::class, 'export'])->name('impayes.export');

        // Utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('proprietaires',  [UserController::class, 'proprietaires'])->name('proprietaires')->middleware('agency.can:proprietaires.lire');
            Route::get('locataires',     [UserController::class, 'locataires'])->name('locataires')->middleware('agency.can:locataires.lire');
            // Composant « Rechercher-ou-Créer » — recherche + création rapide (JSON)
            Route::get('proprietaires/search', [UserController::class, 'proprietaireSearch'])->name('proprietaires.search');
            Route::post('proprietaires/quick', [UserController::class, 'proprietaireQuickStore'])->name('proprietaires.quick')->middleware('agency.can:proprietaires.creer');
            Route::get('locataires/search',    [UserController::class, 'locataireSearch'])->name('locataires.search');
            Route::post('locataires/quick',    [UserController::class, 'locataireQuickStore'])->name('locataires.quick')->middleware('agency.can:locataires.creer');
            Route::get('create/{role}',  [UserController::class, 'create'])->name('create')->middleware('agency.can:locataires.creer,proprietaires.creer');
            Route::post('store',         [UserController::class, 'store'])->name('store')->middleware('agency.can:locataires.creer,proprietaires.creer');
            Route::get('{user}',         [UserController::class, 'show'])->name('show')->middleware('agency.can:proprietaires.lire,locataires.lire');
            Route::get('{user}/edit',    [UserController::class, 'edit'])->name('edit')->middleware('agency.can:locataires.modifier,proprietaires.modifier');
            Route::patch('{user}',       [UserController::class, 'update'])->name('update')->middleware('agency.can:locataires.modifier,proprietaires.modifier');
            Route::delete('{user}',      [UserController::class, 'destroy'])->name('destroy')->middleware('agency.can:locataires.modifier,proprietaires.modifier');
        });

        // Gestion équipe (collaborateurs) — accès contrôlé par gates dans le controller
        // (voirEquipe pour la liste, gererEquipe pour les actions).
        Route::prefix('equipe')->name('equipe.')->group(function () {
            Route::get('/',                          [\App\Http\Controllers\EquipeController::class, 'index'])->name('index');
            Route::get('/invite',                    [\App\Http\Controllers\EquipeController::class, 'create'])->name('create');
            Route::post('/',                         [\App\Http\Controllers\EquipeController::class, 'store'])->name('store');
            Route::get('/{user}/permissions',        [\App\Http\Controllers\EquipeController::class, 'editPermissions'])->name('permissions');
            Route::post('/{user}/permissions',       [\App\Http\Controllers\EquipeController::class, 'updatePermissions'])->name('permissions.update');
            Route::delete('/{user}',                 [\App\Http\Controllers\EquipeController::class, 'destroy'])->name('destroy');
        });

        // Changement de mot de passe forcé (1ʳᵉ connexion d'un collaborateur invité).
        // Exempté du middleware ForcePasswordChange (cf. son whitelist).
        Route::get('mot-de-passe/changer',  [\App\Http\Controllers\ForcePasswordController::class, 'edit'])->name('password.force');
        Route::post('mot-de-passe/changer', [\App\Http\Controllers\ForcePasswordController::class, 'update'])->name('password.force.update');

        // Import de données (flux upload → aperçu → confirmation → annulation)
        Route::prefix('import')->name('import.')->middleware('check.feature:import_excel')->group(function () {
            Route::get('/',                     [ImportController::class, 'index'])->name('index');
            Route::get('historique',            [ImportController::class, 'historique'])->name('historique');
            Route::get('modele/{type}',         [ImportController::class, 'template'])->name('template');
            Route::post('{type}/apercu',        [ImportController::class, 'preview'])->name('preview');
            Route::delete('{type}/apercu',      [ImportController::class, 'discard'])->name('discard');
            Route::post('lot/{batch}/confirmer',[ImportController::class, 'commit'])->name('commit');
            Route::delete('lot/{batch}',        [ImportController::class, 'undo'])->name('undo');
            Route::get('lot/{batch}/codes',     [ImportController::class, 'codes'])->name('codes');
        });

        // ── Module Comptabilité ────────────────────────────────────────────────
        // Accès à l'argent des propriétaires : garde le plan (check.feature) ET la
        // permission utilisateur (agency.can) — un collaborateur « Comptabilité = Aucun »
        // ne peut pas ouvrir ces écrans (le directeur passe toujours).
        Route::prefix('comptabilite')->name('comptabilite.')->middleware(['check.feature:comptabilite', 'agency.can:comptabilite.lire'])->group(function () {
            Route::get('/', [ComptabiliteController::class, 'index'])->name('index');
        });

        // Enregistrement d'une dépense agence (formulaire intégré au module Comptabilité)
        Route::resource('charges-agence', ChargeAgenceController::class)
            ->middleware(['check.feature:comptabilite', 'agency.can:comptabilite.modifier'])
            ->only(['store', 'destroy']);

        // Reporter toutes les charges fixes sur le mois courant (un clic, pas de ressaisie)
        Route::post('charges-agence/reporter', [ChargeAgenceController::class, 'reporter'])
            ->middleware(['check.feature:comptabilite', 'agency.can:comptabilite.modifier'])
            ->name('charges-agence.reporter');

        Route::prefix('reversements')->name('reversements.')->middleware(['check.feature:comptabilite', 'agency.can:comptabilite.lire'])->group(function () {
            Route::get('create',                                  [ReversementController::class, 'create'])->name('create')->middleware('agency.can:comptabilite.modifier');
            Route::post('/',                                      [ReversementController::class, 'store'])->name('store')->middleware('agency.can:comptabilite.modifier');
            Route::get('proprietaire/{proprietaire}',             [ReversementController::class, 'compteMandant'])->name('compte-mandant');
            Route::get('proprietaire/{proprietaire}/releve-pdf', [ReversementController::class, 'relevePdf'])->name('releve-pdf');
        });

        // Module fiscal (désactivé via FEATURE_FISCALITE=false dans .env)
        if (config('features.fiscalite')) {
            // Dashboard + simulation fiscal
            Route::get('fiscal',            [FiscalDashboardController::class, 'dashboard'])->name('fiscal.dashboard')->middleware(['check.feature:fiscalite', 'agency.can:fiscal.lire']);
            Route::get('fiscal/simulation', [FiscalDashboardController::class, 'simuler'])->name('fiscal.simulation')->middleware(['check.feature:fiscalite', 'agency.can:fiscal.lire']);

            Route::prefix('bilans-fiscaux')->name('bilans-fiscaux.')->middleware(['check.feature:bilans_fiscaux', 'agency.can:fiscal.lire'])->group(function () {
                Route::get('/',                               [BilanFiscalController::class, 'index'])->name('index');
                Route::post('{proprietaire}/calculate',       [BilanFiscalController::class, 'calculate'])->name('calculate');
                Route::get('{proprietaire}',                  [BilanFiscalController::class, 'show'])->name('show');
                Route::get('{proprietaire}/pdf',              [BilanFiscalController::class, 'exportPdf'])->name('pdf');
                Route::get('{proprietaire}/fiche-transparente', [BilanFiscalController::class, 'ficheTransparente'])->name('fiche-transparente');
                Route::get('{proprietaire}/attestation-brs',  [BilanFiscalController::class, 'attestationBrs'])->name('attestation-brs');
            });

            Route::prefix('etats-trimestriels')->name('etats-trimestriels.')->middleware(['check.feature:fiscalite', 'agency.can:fiscal.lire'])->group(function () {
                Route::get('/',                                [EtatTrimestrielController::class, 'index'])->name('index');
                Route::get('{annee}/{trimestre}',              [EtatTrimestrielController::class, 'show'])->name('show');
                Route::get('{annee}/{trimestre}/pdf',          [EtatTrimestrielController::class, 'exportPdf'])->name('pdf');
                Route::get('{annee}/{trimestre}/csv',          [EtatTrimestrielController::class, 'exportCsv'])->name('csv');
            });

            Route::prefix('tva-agence')->name('tva-agence.')->middleware(['check.feature:fiscalite', 'agency.can:fiscal.lire'])->group(function () {
                Route::get('/',                              [TvaAgenceController::class, 'index'])->name('index');
                Route::get('{annee}/{mois}',                 [TvaAgenceController::class, 'show'])->name('show');
                Route::put('{annee}/{mois}',                 [TvaAgenceController::class, 'update'])->name('update')->middleware('agency.can:fiscal.modifier');
                Route::post('{annee}/{mois}/valider',        [TvaAgenceController::class, 'valider'])->name('valider')->middleware('agency.can:fiscal.modifier');
                Route::post('{annee}/{mois}/deposee',        [TvaAgenceController::class, 'marquerDeposee'])->name('deposee')->middleware('agency.can:fiscal.modifier');
                Route::get('{annee}/{mois}/pdf',             [TvaAgenceController::class, 'exportPdf'])->name('pdf');
                Route::post('{annee}/{mois}/recalculer',     [TvaAgenceController::class, 'recalculer'])->name('recalculer')->middleware('agency.can:fiscal.modifier');
            });

            Route::get('echeances-fiscales', [EcheancesFiscalesController::class, 'index'])->name('echeances-fiscales.index')->middleware(['check.feature:fiscalite', 'agency.can:fiscal.lire']);
        }

        // Rapports
        Route::get('rapports/financier',            [RapportController::class, 'financier'])->name('rapports.financier')->middleware(['check.feature:rapports_pdf', 'agency.can:rapports.lire']);
        Route::get('rapports/financier/export-pdf', [RapportController::class, 'exportPdf'])->name('rapports.financier.export-pdf')->middleware(['check.feature:rapports_pdf', 'agency.can:rapports.lire', 'throttle:10,1']);

        // Portefeuille Bailleurs (Niveau 5) — relevés financiers des propriétaires
        Route::get('bailleurs',                          [BailleurController::class, 'index'])->name('bailleurs.index')->middleware('agency.can:comptabilite.lire');
        Route::get('bailleurs/{userId}',                 [BailleurController::class, 'show'])->name('bailleurs.show')->middleware('agency.can:comptabilite.lire');
        Route::get('bailleurs/{userId}/export-pdf',      [BailleurController::class, 'exportPdf'])->name('bailleurs.export-pdf')->middleware(['check.feature:releve_bailleur_pdf', 'agency.can:comptabilite.lire', 'throttle:10,1']);
        Route::get('bailleurs/{userId}/releve-pdf',      [BailleurController::class, 'relevePdf'])->name('bailleurs.releve-pdf')->middleware(['check.feature:releve_bailleur_pdf', 'agency.can:comptabilite.lire', 'throttle:10,1']);

        // Dépenses de gestion (avancées pour le compte d'un propriétaire)
        Route::post('comptabilite/proprietaires/{proprietaire}/depenses', [DepenseGestionController::class, 'storeForProprietaire'])->name('comptabilite.depenses.store')->middleware(['check.feature:comptabilite', 'agency.can:comptabilite.modifier']);
        Route::delete('comptabilite/depenses/{depense}',                  [DepenseGestionController::class, 'destroyDepense'])->name('comptabilite.depenses.destroy')->middleware(['check.feature:comptabilite', 'agency.can:comptabilite.modifier']);

        // Export CSV paiements
        Route::get('paiements/export-csv', [PaiementController::class, 'exportCsv'])->name('paiements.export-csv')->middleware(['check.feature:export_csv', 'throttle:10,1']);

        // Contrat de bail PDF (bail simple supprimé — bail formel uniquement)
        Route::get('contrats/{contrat}/bail-formel-pdf', [ContratController::class, 'bailFormelPdf'])->name('contrats.bail-formel-pdf')->middleware('check.feature:contrat_formel_pdf');
    });

    // ── Staff agence — lecture (admin + superadmin) ───────────────────────
    // Déclaré APRÈS isAdmin pour éviter que {contrat}/{paiement} ne capturent
    // les routes spécifiques (create, store…) déclarées ci-dessus.
    Route::middleware('can:isStaff')->prefix('admin')->name('admin.')->group(function () {

        // Biens — routes statiques avant la resource pour éviter que {bien} capture "create"
        Route::get('biens/search-disponibles', [BienController::class, 'searchDisponibles'])->name('biens.search-disponibles');
        Route::get('biens/create',        [BienController::class, 'create'])->name('biens.create')->middleware('agency.can:biens.creer');
        Route::post('biens',              [BienController::class, 'store'])->name('biens.store')->middleware('agency.can:biens.creer');
        Route::get('biens/{bien}/edit',   [BienController::class, 'edit'])->name('biens.edit')->middleware('agency.can:biens.modifier');
        Route::put('biens/{bien}',        [BienController::class, 'update'])->name('biens.update')->middleware('agency.can:biens.modifier');
        Route::patch('biens/{bien}',      [BienController::class, 'update'])->name('biens.update.patch')->middleware('agency.can:biens.modifier');
        Route::delete('biens/{bien}',     [BienController::class, 'destroy'])->name('biens.destroy')->middleware('agency.can:biens.supprimer');
        Route::post('biens/{bien}/photos',                     [BienPhotoController::class, 'store'])->name('biens.photos.store')->middleware('agency.can:biens.modifier');
        Route::delete('biens/{bien}/photos/{photo}',           [BienPhotoController::class, 'destroy'])->name('biens.photos.destroy')->middleware('agency.can:biens.modifier');
        Route::patch('biens/{bien}/photos/{photo}/principale', [BienPhotoController::class, 'setPrincipale'])->name('biens.photos.principale')->middleware('agency.can:biens.modifier');
        Route::resource('biens', BienController::class)->except(['create', 'store', 'edit', 'update', 'destroy'])->middleware('agency.can:biens.lire');

        // Immeubles — routes statiques avant la resource pour éviter que {immeuble} capture "create"
        Route::get('immeubles/create',         [\App\Http\Controllers\ImmeubleController::class, 'create'])->name('immeubles.create')->middleware(['check.feature:immeubles', 'agency.can:immeubles.creer']);
        Route::post('immeubles',               [\App\Http\Controllers\ImmeubleController::class, 'store'])->name('immeubles.store')->middleware(['check.feature:immeubles', 'agency.can:immeubles.creer']);
        Route::get('immeubles/{immeuble}/edit',[\App\Http\Controllers\ImmeubleController::class, 'edit'])->name('immeubles.edit')->middleware(['check.feature:immeubles', 'agency.can:immeubles.modifier']);
        Route::put('immeubles/{immeuble}',     [\App\Http\Controllers\ImmeubleController::class, 'update'])->name('immeubles.update')->middleware(['check.feature:immeubles', 'agency.can:immeubles.modifier']);
        Route::patch('immeubles/{immeuble}',   [\App\Http\Controllers\ImmeubleController::class, 'update'])->name('immeubles.update.patch')->middleware(['check.feature:immeubles', 'agency.can:immeubles.modifier']);
        Route::delete('immeubles/{immeuble}',  [\App\Http\Controllers\ImmeubleController::class, 'destroy'])->name('immeubles.destroy')->middleware(['check.feature:immeubles', 'agency.can:immeubles.modifier']);
        Route::resource('immeubles', ImmeubleController::class)->except(['create', 'store', 'edit', 'update', 'destroy'])->middleware(['check.feature:immeubles', 'agency.can:immeubles.lire']);

        // Contrats — lecture
        Route::get('contrats',           [ContratController::class, 'index'])->name('contrats.index')->middleware('agency.can:contrats.lire');
        Route::get('contrats/{contrat}', [ContratController::class, 'show'])->name('contrats.show')->middleware('agency.can:contrats.lire');

        // Paiements — lecture + PDF
        Route::get('paiements',                [PaiementController::class, 'index'])->name('paiements.index')->middleware('agency.can:paiements.lire');
        Route::get('paiements/{paiement}',     [PaiementController::class, 'show'])->name('paiements.show')->middleware('agency.can:paiements.lire');
        Route::get('paiements/{paiement}/pdf', [PaiementController::class, 'downloadPDF'])->name('paiements.pdf')->middleware('agency.can:paiements.lire');

        // Impayés — lecture
        Route::get('impayes', [ImpayeController::class, 'index'])->name('impayes.index')->middleware('agency.can:impayes.lire');
    });

    // ── Propriétaire ───────────────────────────────────────────────────────
    Route::middleware('isProprietaire')->prefix('proprietaire')->name('proprietaire.')->group(function () {
        Route::get('dashboard',                    ProprietaireDashboardController::class)->name('dashboard');
        Route::get('mes-paiements/{paiement}/pdf', [PaiementController::class,  'downloadPDF'])->name('paiements.pdf');
        Route::get('releve-pdf', function () {
            return app(\App\Http\Controllers\BailleurController::class)
                ->relevePdf(\Illuminate\Support\Facades\Auth::id());
        })->name('releve-pdf');
    });

    // ── Locataire ──────────────────────────────────────────────────────────
    Route::middleware('isLocataire')->prefix('locataire')->name('locataire.')->group(function () {
        Route::get('dashboard',                    LocataireDashboardController::class)->name('dashboard');
        Route::get('mes-paiements',                [PaiementController::class,  'mesPaiements'])->name('paiements');
        Route::get('mes-paiements/{paiement}/pdf', [PaiementController::class,  'downloadPDF'])->name('paiements.pdf');
        Route::get('mon-contrat',                  [ContratController::class,   'monContrat'])->name('mon-contrat');
        Route::get('mon-contrat/{contrat}',        [ContratController::class,   'show'])->name('contrat.show');
    });

});

// ── Réception des rapports CSP (Report-Only) ──────────────────────────────
// Exempté du CSRF car le navigateur envoie du JSON brut sans token.
Route::post('/csp-report', [\App\Http\Controllers\CspReportController::class, 'store'])
    ->middleware('throttle:60,1');

require __DIR__ . '/auth.php';