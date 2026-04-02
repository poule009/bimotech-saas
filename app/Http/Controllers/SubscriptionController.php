<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService)
    {
    }

    public function index(): View
    {
        $user         = Auth::user();
        $agency       = $user->agency;
        $subscription = $agency->subscription;

        $historique = SubscriptionPayment::where('agency_id', $agency->id)
            ->orderByDesc('created_at')
            ->get();

        $tarifs = Subscription::TARIFS;
        $labels = Subscription::LABELS;
        $durees = Subscription::DUREES_MOIS;
        $mode   = config('services.paydunya.mode', 'simulation');

        return view('subscription.index', compact(
            'agency', 'subscription', 'historique',
            'tarifs', 'labels', 'durees', 'mode'
        ));
    }

    public function initierPaiement(Request $request): RedirectResponse
    {
        $request->validate([
            'plan' => ['required', 'in:mensuel,trimestriel,semestriel,annuel'],
        ], [
            'plan.required' => 'Veuillez choisir un plan.',
            'plan.in'       => 'Plan invalide.',
        ]);

        $agency   = Auth::user()->agency;
        $plan     = $request->plan;
        $resultat = $this->paymentService->initierPaiement($agency, $plan);

        if (! $resultat['success']) {
            return back()->withErrors(['general' => $resultat['message']]);
        }

        if ($resultat['mode'] !== 'simulation' && $resultat['redirect_url']) {
            return redirect()->away($resultat['redirect_url']);
        }

        $labels = Subscription::LABELS;
        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Abonnement {$labels[$plan]} activé avec succès !");
    }

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

    // ✅ CORRECTION C2 : verrou DB + plan depuis PayDunya (pas la session)
    // Avant : deux requêtes simultanées pouvaient activer 2 fois
    // Avant : si la session expirait, le plan était perdu
    // Après : lockForUpdate() + plan lu depuis l'API PayDunya
    public function succes(Request $request): View|RedirectResponse
    {
        $token  = $request->query('token');
        $agency = Auth::user()->agency;

        if (! $token) {
            return redirect()->route('subscription.index')
                ->with('info', 'Votre paiement est en cours de traitement.');
        }

        try {
            // On demande à PayDunya si le paiement est vraiment complété
            $statut = $this->paymentService->verifierStatutFacture($token);

            if (! $statut || ($statut['status'] ?? '') !== 'completed') {
                return redirect()->route('subscription.index')
                    ->with('info', 'Votre paiement est en cours de traitement.');
            }

            // Le plan vient de PayDunya, pas de la session
            $planPaydunya = $statut['custom_data']['plan'] ?? null;
            $planSession  = session('subscription_plan_pending');

            // Alerte si les deux ne correspondent pas
            if ($planSession && $planPaydunya && $planSession !== $planPaydunya) {
                Log::critical('Incohérence plan session vs PayDunya', [
                    'session'   => $planSession,
                    'paydunya'  => $planPaydunya,
                    'token'     => $token,
                    'agency_id' => $agency->id,
                ]);
            }

            $plan = $planPaydunya ?? $planSession;

            if (! $plan || ! array_key_exists($plan, Subscription::TARIFS)) {
                return redirect()->route('subscription.index')
                    ->with('error', 'Plan invalide. Contactez le support.');
            }

            // Activation avec verrou — impossible de l'activer deux fois en même temps
            DB::transaction(function () use ($agency, $plan, $token) {
                $subscription = Subscription::where('agency_id', $agency->id)
                    ->lockForUpdate()
                    ->first();

                if (! $subscription) {
                    $subscription = Subscription::create([
                        'agency_id'        => $agency->id,
                        'statut'           => 'essai',
                        'date_debut_essai' => now(),
                        'date_fin_essai'   => now()->addDays(30),
                    ]);
                }

                if (! SubscriptionPayment::where('reference', $token)->exists()) {
                    $subscription->activer($plan, $token, 'paydunya');
                }
            });

            session()->forget(['subscription_plan_pending', 'subscription_agency_id']);

            return view('subscription.succes', compact('agency'));

        } catch (\Throwable $e) {
            Log::error('Erreur retour PayDunya succes()', [
                'token'     => $token,
                'agency_id' => $agency->id,
                'error'     => $e->getMessage(),
            ]);
            return redirect()->route('subscription.index')
                ->with('info', 'Votre paiement est en cours de vérification.');
        }
    }

    public function echec(): View
    {
        session()->forget(['subscription_plan_pending', 'subscription_agency_id']);
        return view('subscription.echec');
    }
}