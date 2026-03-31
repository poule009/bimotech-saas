<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

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

        $tarifs  = Subscription::TARIFS;
        $labels  = Subscription::LABELS;
        $durees  = Subscription::DUREES_MOIS;
        $mode    = config('services.paydunya.mode', 'simulation');

        return view('subscription.index', compact(
            'agency', 'subscription', 'historique',
            'tarifs', 'labels', 'durees', 'mode'
        ));
    }

    // ── Initier un paiement (PayDunya ou simulation) ──────────────────────

    public function initierPaiement(Request $request): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:mensuel,trimestriel,semestriel,annuel'],
        ], [
            'plan.required' => 'Veuillez choisir un plan.',
            'plan.in'       => 'Plan invalide.',
        ]);

        $agency = Auth::user()->agency;
        $plan   = $request->plan;

        $resultat = $this->paymentService->initierPaiement($agency, $plan);

        if (! $resultat['success']) {
            return back()->withErrors(['general' => $resultat['message']]);
        }

        // Mode PayDunya : rediriger vers la page de paiement externe
        if ($resultat['mode'] !== 'simulation' && $resultat['redirect_url']) {
            return redirect()->away($resultat['redirect_url']);
        }

        // Mode simulation : abonnement activé directement
        $labels = Subscription::LABELS;
        return redirect()
            ->route('admin.dashboard')
            ->with('success', "🎉 Abonnement {$labels[$plan]} activé avec succès !");
    }

    // ── Callback IPN PayDunya (webhook — sans auth) ───────────────────────

    public function callbackPaydunya(Request $request): JsonResponse
    {
        $payload  = $request->all();

        Log::info('Callback PayDunya reçu', ['payload' => $payload]);

        $resultat = $this->paymentService->traiterCallbackIPN($payload);

        return response()->json([
            'success' => $resultat['success'],
            'message' => $resultat['message'],
        ], $resultat['success'] ? 200 : 422);
    }

    // ── Page de retour succès PayDunya ────────────────────────────────────

    public function succes(Request $request): View|RedirectResponse
    {
        $token  = $request->query('token');
        $agency = Auth::user()->agency;

        // Vérifier le statut de la facture si un token est présent
        if ($token) {
            $statut = $this->paymentService->verifierStatutFacture($token);

            if (isset($statut['status']) && $statut['status'] === 'completed') {
                $plan = session('subscription_plan_pending');

                if ($plan && $agency->subscription) {
                    // Idempotence : vérifier si déjà activé via IPN
                    $dejaTraite = SubscriptionPayment::where('reference', $token)->exists();
                    if (! $dejaTraite && $plan) {
                        $agency->subscription->activer($plan, $token, 'paydunya');
                    }
                }

                session()->forget(['subscription_plan_pending', 'subscription_agency_id']);

                return view('subscription.succes', compact('agency'));
            }
        }

        return redirect()->route('subscription.index')
            ->with('info', 'Votre paiement est en cours de traitement.');
    }

    // ── Page de retour échec PayDunya ─────────────────────────────────────

    public function echec(): View
    {
        session()->forget(['subscription_plan_pending', 'subscription_agency_id']);
        return view('subscription.echec');
    }
}
