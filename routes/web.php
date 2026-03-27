<?php

use App\Http\Controllers\Auth\AgencyRegistrationController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use Illuminate\Support\Facades\Route;

// ── Page d'accueil → redirection auto ────────────────────────────────────
Route::get('/', fn() => redirect()->route('redirect.home'));

// ── Inscription d'une nouvelle agence (publique, sans auth) ───────────────
Route::middleware('guest')->group(function () {
    Route::get('/register/agency', [AgencyRegistrationController::class, 'create'])
         ->name('agency.register');
    Route::post('/register/agency', [AgencyRegistrationController::class, 'store'])
         ->name('agency.register.store');
});

// ── Routes authentifiées ──────────────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {

    // ── Redirection auto après login ──────────────────────────────────────
    Route::get('/home', [RedirectController::class, 'index'])
         ->name('redirect.home');

    // ── Profil (Breeze) ───────────────────────────────────────────────────
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Dashboard SuperAdmin ──────────────────────────────────────────────
    Route::middleware('isSuperAdmin')
         ->prefix('superadmin')
         ->name('superadmin.')
         ->group(function () {
            Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])
                 ->name('dashboard');
            Route::patch('/agencies/{agency}/toggle', [SuperAdminController::class, 'toggleActif'])
                 ->name('agencies.toggle');
            Route::get('/agencies/{agency}', [SuperAdminController::class, 'showAgency'])
                 ->name('agencies.show');
    });

    // ── Dashboard Admin ───────────────────────────────────────────────────
    Route::middleware('isAdmin')
         ->prefix('admin')
         ->name('admin.')
         ->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'admin'])
                 ->name('dashboard');

            Route::resource('paiements', PaiementController::class);
            Route::get('paiements/{paiement}/pdf', [PaiementController::class, 'downloadPDF'])
                 ->name('paiements.pdf');
            Route::patch('paiements/{paiement}/annuler', [PaiementController::class, 'annuler'])
                 ->name('paiements.annuler');

            Route::resource('contrats', \App\Http\Controllers\ContratController::class)
                 ->only(['index', 'create', 'store', 'show', 'destroy']);

            Route::prefix('users')->name('users.')->group(function () {
                Route::get('proprietaires', [\App\Http\Controllers\UserController::class, 'proprietaires'])
                     ->name('proprietaires');
                Route::get('locataires', [\App\Http\Controllers\UserController::class, 'locataires'])
                     ->name('locataires');
                Route::get('{user}', [\App\Http\Controllers\UserController::class, 'show'])
                     ->name('show');
                Route::get('create/{role}', [\App\Http\Controllers\UserController::class, 'create'])
                     ->name('create');
                Route::post('store', [\App\Http\Controllers\UserController::class, 'store'])
                     ->name('store');
                Route::delete('{user}', [\App\Http\Controllers\UserController::class, 'destroy'])
                     ->name('destroy');
            });

            Route::get('rapports/financier', [\App\Http\Controllers\RapportController::class, 'financier'])
                 ->name('rapports.financier');

            Route::get('impayes', [\App\Http\Controllers\ImpayeController::class, 'index'])
                 ->name('impayes.index');
            Route::post('impayes/{contrat}/relance', [\App\Http\Controllers\ImpayeController::class, 'relance'])
                 ->name('impayes.relance');
    });

    // ── Dashboard Propriétaire ────────────────────────────────────────────
    Route::middleware('can:isProprietaire')
         ->prefix('proprietaire')
         ->name('proprietaire.')
         ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'proprietaire'])
                 ->name('dashboard');
            Route::get('mes-paiements/{paiement}/pdf', [PaiementController::class, 'downloadPDF'])
                 ->name('paiements.pdf');
    });

    // ── Dashboard Locataire ───────────────────────────────────────────────
    Route::middleware('can:isLocataire')
         ->prefix('locataire')
         ->name('locataire.')
         ->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'locataire'])
                 ->name('dashboard');
            Route::get('mes-paiements', [PaiementController::class, 'mesPaiements'])
                 ->name('paiements');
            Route::get('mes-paiements/{paiement}/pdf', [PaiementController::class, 'downloadPDF'])
                 ->name('paiements.pdf');
    });

    // ── Biens (admin + proprio) ───────────────────────────────────────────
    Route::middleware('can:isStaff')
         ->resource('biens', BienController::class);

    // ── Photos des biens ──────────────────────────────────────────────────
    Route::post('biens/{bien}/photos', [\App\Http\Controllers\BienPhotoController::class, 'store'])
         ->name('biens.photos.store');
    Route::delete('biens/{bien}/photos/{photo}', [\App\Http\Controllers\BienPhotoController::class, 'destroy'])
         ->name('biens.photos.destroy');
    Route::patch('biens/{bien}/photos/{photo}/principale', [\App\Http\Controllers\BienPhotoController::class, 'setPrincipale'])
         ->name('biens.photos.principale');
});

require __DIR__ . '/auth.php';