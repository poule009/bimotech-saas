<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller implements HasMiddleware
{
    use AuthorizesRequests;

    /** Catégories de filtre (chips) → types de modèles concernés. */
    private const CATEGORIES = [
        'paiements' => [Paiement::class],
        'contrats' => [Contrat::class],
        'biens' => [Bien::class],
        'personnes' => [User::class, Locataire::class, Proprietaire::class],
    ];

    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:logs_activite'),
        ];
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user && in_array($user->role, ['superadmin', 'admin']), 403);

        // Le journal N'AFFICHE PAS les consultations ('viewed') — réservées au futur
        // module « Mon équipe ». La table sait les stocker, l'écran les exclut.
        $query = ActivityLog::where('action', '!=', 'viewed')->latest();

        if ($user->role === 'admin') {
            $query->where('agency_id', $user->agency_id);
        }

        // Super Admin à accès restreint (module Équipe interne) : le journal est
        // borné à ses agences apportées — jamais l'activité des autres agences ni
        // les actions de l'admin principal (asymétrie de visibilité).
        if ($user->role === 'superadmin') {
            $perimAgencyIds = app(\App\Support\SuperAdminContext::class)->perimetreAgencyIds();
            if ($perimAgencyIds !== null) {
                // Ses agences uniquement, et sans les traces d'impersonation (support
                // de l'admin principal) : le collaborateur ne voit que ses propres
                // sessions, dans l'écran Support / Debug.
                $query->whereIn('agency_id', $perimAgencyIds)
                    ->whereNotIn('action', ['impersonate', 'impersonate_revoked']);
            }
        }

        // Super Admin : restreindre le journal à une agence (« Voir le journal » depuis la fiche).
        if ($user->role === 'superadmin' && $request->filled('agency')) {
            $query->where('agency_id', (int) $request->input('agency'));
        }

        // Recherche (description) — wildcards LIKE échappés
        if ($request->filled('q')) {
            $q = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->q);
            $query->where('description', 'like', '%'.$q.'%');
        }

        // Filtre par catégorie d'entité
        $categorie = $request->get('categorie');
        if ($categorie && isset(self::CATEGORIES[$categorie])) {
            $query->whereIn('model_type', self::CATEGORIES[$categorie]);
        }

        // Filtre par type d'action (create / update / delete…)
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        // Filtre « Sensibles uniquement »
        if ($request->boolean('sensibles')) {
            $query->where('is_sensitive', true);
        }

        $logs = $query
            ->with(['user:id,name', 'agency:id,name'])
            ->paginate(30)
            ->withQueryString();

        return view('activity-logs.index', [
            'logs' => $logs,
            'categorie' => $categorie,
            'sensibles' => $request->boolean('sensibles'),
            'q' => (string) $request->get('q'),
        ]);
    }
}
