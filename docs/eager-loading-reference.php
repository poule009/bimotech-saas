<?php

use App\Models\ActivityLog;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Support\Facades\Auth;

/**
 * GUIDE EAGER LOADING — BimoTech Immo
 * =====================================
 * Ce fichier documente les with() à ajouter dans chaque contrôleur.
 * Copie chaque bloc dans le bon contrôleur.
 *
 * Règle : toute relation utilisée dans une vue DOIT être chargée ici.
 * Utilise php artisan debugbar ou Telescope pour traquer les N+1 restants.
 */

// ─── BienController ────────────────────────────────────────────────────────

// index() — liste des biens avec propriétaire + locataire actif
$biens = Bien::with([
    'proprietaire:id,nom,prenom',          // Colonnes limitées → moins de mémoire
    'contratActif.locataire:id,nom,prenom',
])
->when($request->statut, fn($q, $s) => $q->where('statut', $s))
->when($request->type,   fn($q, $t) => $q->where('type', $t))
->latest()
->paginate(12);

// show() — détail complet d'un bien
$bien->load([
    'proprietaire',
    'contratActif.locataire',
    'contrats' => fn($q) => $q->latest()->limit(5),
    'paiements' => fn($q) => $q->latest()->with('quittance')->limit(10),
]);

// ─── ContratController ──────────────────────────────────────────────────────

// index()
$contrats = Contrat::with([
    'bien:id,titre,reference,quartier,ville',
    'locataire:id,nom,prenom',
    'bien.proprietaire:id,nom,prenom',
])
->latest()
->paginate(15);

// show()
$contrat->load([
    'bien.proprietaire',
    'locataire',
    'paiements' => fn($q) => $q->orderBy('date_echeance')->with('quittance'),
]);

// ─── ProprietaireDashboardController ────────────────────────────────────────

// index()
$proprietaire = Auth::user()->proprietaire;
$proprietaire->load([
    'biens' => fn($q) => $q->with([
        'contratActif.locataire:id,nom,prenom,telephone',
        'paiements' => fn($p) => $p->latest()->limit(3),
    ]),
]);

// ─── LocataireDashboardController ───────────────────────────────────────────

// index()
$locataire = Auth::user()->locataire;
$locataire->load([
    'contrats' => fn($q) => $q->where('statut', 'actif')->with([
        'bien:id,titre,adresse,quartier',
        'bien.agency:id,nom,telephone,whatsapp',
        'paiements' => fn($p) => $p->orderBy('date_echeance')->with('quittance'),
    ]),
]);

// ─── RapportController ──────────────────────────────────────────────────────

// index() — rapport financier mensuel
$paiements = Paiement::with([
    'contrat.bien:id,titre,reference',
    'contrat.locataire:id,nom,prenom',
    'contrat.bien.proprietaire:id,nom,prenom',
    'quittance:id,paiement_id,numero',
])
->where('mois_concerne', $periode)
->orderBy('date_echeance')
->get();

// ─── ActivityLogController ──────────────────────────────────────────────────

// index()
$logs = ActivityLog::with([
    'user:id,name,role',
    'agency:id,nom',   // Colonne superadmin uniquement
])
->latest()
->paginate(50);