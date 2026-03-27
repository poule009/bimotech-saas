<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminController extends Controller
{
    // ── Dashboard global ──────────────────────────────────────────────────

    public function dashboard(): View
    {
        // withoutGlobalScopes() est nécessaire sur Bien, Contrat, Paiement
        // car ces modèles ont le AgencyScope actif
        $stats = [
            'nb_agences'        => Agency::count(),
            'nb_agences_actives'=> Agency::where('actif', true)->count(),
            'nb_users'          => User::withoutGlobalScopes()->count(),
            'nb_biens'          => Bien::withoutGlobalScopes()->count(),
            'nb_contrats'       => Contrat::withoutGlobalScopes()
                                    ->where('statut', 'actif')->count(),
            'total_loyers'      => Paiement::withoutGlobalScopes()
                                    ->where('statut', 'valide')
                                    ->sum('montant_encaisse'),
            'total_commissions' => Paiement::withoutGlobalScopes()
                                    ->where('statut', 'valide')
                                    ->sum('commission_ttc'),
        ];

        // Récupère toutes les agences avec leurs stats agrégées
        $agences = Agency::withCount([
                'users',
                'biens',
                'contrats' => fn($q) => $q->where('statut', 'actif'),
            ])
            ->with('users:id,agency_id,role')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($agency) {
                // Calcul du total des loyers encaissés par agence
                $agency->total_loyers = Paiement::withoutGlobalScopes()
                    ->where('agency_id', $agency->id)
                    ->where('statut', 'valide')
                    ->sum('montant_encaisse');

                $agency->total_commissions = Paiement::withoutGlobalScopes()
                    ->where('agency_id', $agency->id)
                    ->where('statut', 'valide')
                    ->sum('commission_ttc');

                $agency->nb_admins = $agency->users
                    ->where('role', 'admin')->count();

                return $agency;
            });

        return view('superadmin.dashboard', compact('stats', 'agences'));
    }

    // ── Activer / Désactiver une agence ───────────────────────────────────

    public function toggleActif(Agency $agency): RedirectResponse
    {
        $agency->update(['actif' => ! $agency->actif]);

        $statut  = $agency->actif ? 'activée' : 'désactivée';
        $message = "L'agence {$agency->name} a été {$statut} avec succès.";

        return redirect()
            ->route('superadmin.dashboard')
            ->with('success', $message);
    }

    // ── Détail d'une agence ───────────────────────────────────────────────

    public function showAgency(Agency $agency): View
    {
        $users = User::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->orderBy('role')
            ->get();

        $biens = Bien::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->with('proprietaire')
            ->get();

        $stats = [
            'nb_users'          => $users->count(),
            'nb_proprietaires'  => $users->where('role', 'proprietaire')->count(),
            'nb_locataires'     => $users->where('role', 'locataire')->count(),
            'nb_biens'          => $biens->count(),
            'nb_biens_loues'    => $biens->where('statut', 'loue')->count(),
            'nb_contrats'       => Contrat::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'actif')->count(),
            'total_loyers'      => Paiement::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'valide')
                                    ->sum('montant_encaisse'),
            'total_commissions' => Paiement::withoutGlobalScopes()
                                    ->where('agency_id', $agency->id)
                                    ->where('statut', 'valide')
                                    ->sum('commission_ttc'),
        ];

        return view('superadmin.agency-detail', compact('agency', 'users', 'biens', 'stats'));
    }
}