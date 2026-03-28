<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    // ── Page de choix d'abonnement ────────────────────────────────────────

    public function index(): View
    {
        $user         = Auth::user();
        $agency       = $user->agency;
        $subscription = $agency->subscription;

        // Historique des paiements de cette agence
        $historique = SubscriptionPayment::where('agency_id', $agency->id)
            ->orderByDesc('created_at')
            ->get();

        return view('subscription.index', compact('agency', 'subscription', 'historique'));
    }

    // ── Simuler un paiement (en attendant PayDunya) ───────────────────────

    public function simulerPaiement(Request $request): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:mensuel,trimestriel,semestriel,annuel'],
        ], [
            'plan.required' => 'Veuillez choisir un plan.',
            'plan.in'       => 'Plan invalide.',
        ]);

        $user         = Auth::user();
        $agency       = $user->agency;
        $subscription = $agency->subscription;

        if (! $subscription) {
            $subscription = Subscription::create([
                'agency_id'        => $agency->id,
                'statut'           => 'essai',
                'date_debut_essai' => now(),
                'date_fin_essai'   => now()->addDays(30),
            ]);
        }

        // Active l'abonnement et enregistre le paiement
        $subscription->activer(
            $request->plan,
            'SIMULATION-' . strtoupper($request->plan) . '-' . now()->format('YmdHis'),
            'manuel'
        );

        $labels = Subscription::LABELS;

        return redirect()
            ->route('admin.dashboard')
            ->with('success', "🎉 Abonnement {$labels[$request->plan]} activé avec succès !");
    }
}