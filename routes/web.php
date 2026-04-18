<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AgencySettingsController;
use App\Http\Controllers\Admin\BilanFiscalController;
use App\Http\Controllers\Auth\AgencyRegistrationController;
use App\Http\Controllers\BailleurController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\BienPhotoController;
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
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ── Health check (monitoring, load balancer) ───────────────────────────────
// Accessible sans authentification. Retourne l'état de la DB, du cache et des queues.
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

// ── Pages publiques ────────────────────────────────────────────────────────
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/contact',          fn() => view('contact'))->name('contact');
Route::get('/demo',             fn() => view('demo'))->name('demo');
Route::get('/faq',              fn() => view('faq'))->name('faq');
Route::get('/mentions-legales', fn() => view('mentions-legales'))->name('mentions-legales');
Route::get('/confidentialite',  fn() => view('confidentialite'))->name('confidentialite');
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
Route::post('/demo',    [DemoController::class,    'send'])->name('demo.send');

// ── Inscription agence (invités) ───────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register/agency',  [AgencyRegistrationController::class, 'create'])->name('agency.register');
    Route::post('/register/agency', [AgencyRegistrationController::class, 'store'])->name('agency.register.store');
});

// ── Zone authentifiée ──────────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/home',      [RedirectController::class, 'index'])->name('redirect.home');
    Route::get('/dashboard', [RedirectController::class, 'index'])->name('dashboard');

    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Abonnements ────────────────────────────────────────────────────────
    Route::prefix('subscription')->name('subscription.')->group(function () {
        Route::get('/',         [SubscriptionController::class, 'index'])->name('index');
        Route::post('initier',  [SubscriptionController::class, 'initierPaiement'])->name('initier');
        Route::post('callback', [SubscriptionController::class, 'callbackPaytech'])
            ->name('callback')
            ->withoutMiddleware(['auth', 'verified'])
            ->middleware('throttle:10,1'); // 10 req/min — PayTech légitime n'en envoie pas plus
        Route::get('succes', [SubscriptionController::class, 'succes'])->name('succes');
        Route::get('echec',  [SubscriptionController::class, 'echec'])->name('echec');
    });

    // ── SuperAdmin ─────────────────────────────────────────────────────────
    Route::middleware('isSuperAdmin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('dashboard',                     [SuperAdminController::class, 'dashboard'])->name('dashboard');
        Route::get('subscriptions',                 [SuperAdminController::class, 'subscriptions'])->name('subscriptions');
        Route::get('activity-logs',                 [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('agencies/create',               [SuperAdminController::class, 'createAgency'])->name('agencies.create');
        Route::post('agencies',                     [SuperAdminController::class, 'storeAgency'])->name('agencies.store');
        Route::patch('agencies/{agency}/toggle',    [SuperAdminController::class, 'toggleActif'])->name('agencies.toggle');
        Route::post('agencies/{agency}/abonnement', [SuperAdminController::class, 'activerAbonnement'])->name('agencies.abonnement.activer');
        Route::post('agencies/{agency}/essai',      [SuperAdminController::class, 'reinitialiserEssai'])->name('agencies.essai.reinitialiser');
        Route::get('agencies/{agency}',             [SuperAdminController::class, 'showAgency'])->name('agencies.show');
    });

    // ── Routes accessibles admin ET propriétaires (isStaff) ───────────────
    // ── Admin agence — écriture uniquement ────────────────────────────────
    // IMPORTANT : ce groupe doit être déclaré AVANT le groupe isStaff
    // pour que les routes spécifiques (create, store…) soient enregistrées
    // avant les routes paramétrées ({contrat}, {paiement}…).
    Route::middleware('isAdmin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('dashboard', AdminDashboardController::class)->name('dashboard');

        // Paramètres agence
        Route::get('agency/settings',   [AgencySettingsController::class, 'edit'])->name('agency.settings');
        Route::patch('agency/settings', [AgencySettingsController::class, 'update'])->name('agency.settings.update');
        Route::delete('agency/logo',    [AgencySettingsController::class, 'deleteLogo'])->name('agency.logo.delete');

        // Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Paiements — écriture
        Route::get('paiements/dernier-periode/{contrat}', [PaiementController::class, 'dernierePeriode'])->name('paiements.dernier-periode');
        Route::get('paiements/fiscal-preview/{contrat}',  [PaiementController::class, 'fiscalPreview'])->name('paiements.fiscal-preview');
        Route::get('paiements/create',               [PaiementController::class, 'create'])->name('paiements.create');
        Route::post('paiements',                     [PaiementController::class, 'store'])->name('paiements.store');
        Route::patch('paiements/{paiement}/annuler', [PaiementController::class, 'annuler'])->name('paiements.annuler');

        // Contrats — écriture
        Route::post('contrats/locataire-rapide', [ContratController::class, 'storeLocataireRapide'])->name('contrats.locataire-rapide');
        Route::get('contrats/create',            [ContratController::class, 'create'])->name('contrats.create');
        Route::post('contrats',                  [ContratController::class, 'store'])->name('contrats.store');
        Route::get('contrats/{contrat}/edit',    [ContratController::class, 'edit'])->name('contrats.edit');
        Route::put('contrats/{contrat}',         [ContratController::class, 'update'])->name('contrats.update');
        Route::delete('contrats/{contrat}',      [ContratController::class, 'destroy'])->name('contrats.destroy');

        // Impayés — écriture (relance)
        Route::post('impayes/{contrat}/relance', [ImpayeController::class, 'relance'])->name('impayes.relance');

        // Utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('proprietaires',  [UserController::class, 'proprietaires'])->name('proprietaires');
            Route::get('locataires',     [UserController::class, 'locataires'])->name('locataires');
            Route::get('create/{role}',  [UserController::class, 'create'])->name('create');
            Route::post('store',         [UserController::class, 'store'])->name('store');
            Route::get('{user}',         [UserController::class, 'show'])->name('show');
            Route::get('{user}/edit',    [UserController::class, 'edit'])->name('edit');
            Route::patch('{user}',       [UserController::class, 'update'])->name('update');
            Route::delete('{user}',      [UserController::class, 'destroy'])->name('destroy');
        });

        // Bilans fiscaux
        Route::prefix('bilans-fiscaux')->name('bilans-fiscaux.')->group(function () {
            Route::get('/',                         [BilanFiscalController::class, 'index'])->name('index');
            Route::post('{proprietaire}/calculate', [BilanFiscalController::class, 'calculate'])->name('calculate');
            Route::get('{proprietaire}',            [BilanFiscalController::class, 'show'])->name('show');
            Route::get('{proprietaire}/pdf',        [BilanFiscalController::class, 'exportPdf'])->name('pdf');
        });

        // Rapports
        Route::get('rapports/financier',            [RapportController::class, 'financier'])->name('rapports.financier');
        Route::get('rapports/financier/export-pdf', [RapportController::class, 'exportPdf'])->name('rapports.financier.export-pdf');

        // Portefeuille Bailleurs (Niveau 5)
        Route::get('bailleurs',                     [BailleurController::class, 'index'])->name('bailleurs.index');
        Route::get('bailleurs/{userId}',             [BailleurController::class, 'show'])->name('bailleurs.show');
        Route::get('bailleurs/{userId}/export-pdf', [BailleurController::class, 'exportPdf'])->name('bailleurs.export-pdf');
    });

    // ── Staff agence — lecture (admin + superadmin) ───────────────────────
    // Déclaré APRÈS isAdmin pour éviter que {contrat}/{paiement} ne capturent
    // les routes spécifiques (create, store…) déclarées ci-dessus.
    Route::middleware('can:isStaff')->prefix('admin')->name('admin.')->group(function () {

        // Biens
        Route::get('biens/wizard', [BienController::class, 'wizard'])->name('biens.wizard');
        Route::resource('biens', BienController::class);
        Route::post('biens/{bien}/photos',                     [BienPhotoController::class, 'store'])->name('biens.photos.store');
        Route::delete('biens/{bien}/photos/{photo}',           [BienPhotoController::class, 'destroy'])->name('biens.photos.destroy');
        Route::patch('biens/{bien}/photos/{photo}/principale', [BienPhotoController::class, 'setPrincipale'])->name('biens.photos.principale');

        // Contrats — lecture
        Route::get('contrats',           [ContratController::class, 'index'])->name('contrats.index');
        Route::get('contrats/{contrat}', [ContratController::class, 'show'])->name('contrats.show');

        // Paiements — lecture + PDF
        Route::get('paiements',                [PaiementController::class, 'index'])->name('paiements.index');
        Route::get('paiements/{paiement}',     [PaiementController::class, 'show'])->name('paiements.show');
        Route::get('paiements/{paiement}/pdf', [PaiementController::class, 'downloadPDF'])->name('paiements.pdf');

        // Impayés — lecture
        Route::get('impayes', [ImpayeController::class, 'index'])->name('impayes.index');
    });

    // ── Propriétaire ───────────────────────────────────────────────────────
    Route::middleware('isProprietaire')->prefix('proprietaire')->name('proprietaire.')->group(function () {
        Route::get('dashboard',                    ProprietaireDashboardController::class)->name('dashboard');
        Route::get('mes-paiements/{paiement}/pdf', [PaiementController::class,  'downloadPDF'])->name('paiements.pdf');
    });

    // ── Locataire ──────────────────────────────────────────────────────────
    Route::middleware('isLocataire')->prefix('locataire')->name('locataire.')->group(function () {
        Route::get('dashboard',                    LocataireDashboardController::class)->name('dashboard');
        Route::get('mes-paiements',                [PaiementController::class,  'mesPaiements'])->name('paiements');
        Route::get('mes-paiements/{paiement}/pdf', [PaiementController::class,  'downloadPDF'])->name('paiements.pdf');
        Route::get('mon-contrat/{contrat}',        [ContratController::class,   'show'])->name('contrat.show');
    });

});

require __DIR__ . '/auth.php';