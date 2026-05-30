<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Services\PaymentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    use AuthorizesRequests;

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
        $mode   = config('services.paytech.mode', 'simulation');

        return view('subscription.index', compact(
            'agency', 'subscription', 'historique',
            'tarifs', 'labels', 'durees', 'mode'
        ));
    }

    public function initierPaiement(Request $request): RedirectResponse
    {
        $this->authorize('isAdmin');

        $request->validate([
            'plan'        => ['required', 'in:mensuel,annuel'],
            'plan_niveau' => ['required', 'in:starter,pro,agence'],
        ], [
            'plan.required'        => 'Veuillez choisir un cycle de facturation.',
            'plan.in'              => 'Cycle de facturation invalide.',
            'plan_niveau.required' => 'Veuillez choisir un niveau de plan.',
            'plan_niveau.in'       => 'Niveau de plan invalide.',
        ]);

        $agency     = Auth::user()->agency;
        $plan       = $request->plan;
        $planNiveau = $request->plan_niveau;

        // ── Vérification quota avant tout paiement ────────────────────────
        $limiteNouveau = match ($planNiveau) {
            'starter' => 15,
            'pro'     => 50,
            'agence'  => null,
            default   => 50,
        };

        if ($limiteNouveau !== null) {
            $nbUnites = $agency->nbUnitesActives();

            if ($nbUnites > $limiteNouveau) {
                [$planSuggere, $limiteSuggere] = match ($planNiveau) {
                    'starter' => ['Pro', '50 unités'],
                    default   => ['Agence', 'illimité'],
                };

                return back()->withErrors([
                    'general' => "Impossible de souscrire au plan " . ucfirst($planNiveau) . " : "
                        . "vous gérez {$nbUnites} biens alors que ce plan n'en autorise que {$limiteNouveau}. "
                        . "Archivez les biens excédentaires ou choisissez le plan {$planSuggere} ({$limiteSuggere}).",
                ]);
            }
        }

        session([
            'subscription_plan_pending'        => $plan,
            'subscription_plan_niveau_pending' => $planNiveau,
        ]);

        $resultat = $this->paymentService->initierPaiement($agency, $plan, $planNiveau);

        if (! $resultat['success']) {
            return back()->withErrors(['general' => $resultat['message']]);
        }

        if ($resultat['mode'] !== 'simulation' && $resultat['redirect_url']) {
            $redirectUrl = $resultat['redirect_url'];

            // Sécurité : whitelist domaine PayTech pour éviter un open redirect.
            // Un compromis de l'API key ne pourrait pas rediriger vers un site tiers.
            if (! str_starts_with($redirectUrl, 'https://paytech.sn/')) {
                Log::warning('PayTech — URL de redirection hors whitelist (open redirect bloqué)', [
                    'redirect_url' => $redirectUrl,
                    'agency_id'    => Auth::user()->agency_id,
                ]);
                return back()->withErrors(['general' => 'Erreur de paiement : réponse inattendue du service de paiement.']);
            }

            return redirect()->away($redirectUrl);
        }

        $labels = Subscription::LABELS;
        return redirect()
            ->route('admin.dashboard')
            ->with('success', "Abonnement {$labels[$plan]} activé avec succès !");
    }

    public function callbackPaytech(Request $request): JsonResponse
    {
        // ── Vérification de la signature PayTech ───────────────────────────
        // En mode simulation : aucun appel réel, on passe directement.
        // En test/prod : on vérifie la signature IPN pour s'assurer que
        // l'appel provient bien de PayTech et non d'un attaquant qui
        // forgerait un faux callback pour activer un abonnement sans payer.
        //
        // PayTech supporte deux méthodes de vérification :
        //  1. HMAC-SHA256(amount|ref_command|api_key, api_secret) — recommandée
        //  2. SHA256(api_key) et SHA256(api_secret) — fallback
        $mode = config('services.paytech.mode', 'simulation');

        if ($mode !== 'simulation') {
            // Défense en profondeur : vérification IP avant la signature.
            // Configurer PAYTECH_IPS dans .env avec la liste séparée par virgules fournie par PayTech.
            // Si la variable est absente, la vérification est ignorée (rétrocompat).
            $allowedIps = array_filter(explode(',', env('PAYTECH_IPS', '')));
            if (! empty($allowedIps) && ! in_array($request->ip(), $allowedIps)) {
                Log::warning('Webhook PayTech — IP source non autorisée (rejetée avant vérification signature)', [
                    'ip'   => $request->ip(),
                    'mode' => $mode,
                ]);
                return response()->json(['success' => false, 'message' => 'IP non autorisée'], 403);
            }

            if (! $this->paymentService->verifierSignatureIPN($request->all())) {
                Log::warning('Webhook PayTech IPN — signature invalide', [
                    'ip'          => $request->ip(),
                    'mode'        => $mode,
                    'ref_command' => $request->input('ref_command'),
                ]);

                return response()->json(['success' => false, 'message' => 'Signature invalide'], 403);
            }
        }

        $payload = $request->all();
        Log::info('Callback PayTech reçu et vérifié', [
            'mode'        => $mode,
            'ip'          => $request->ip(),
            'type_event'  => $request->input('type_event'),
            'ref_command' => $request->input('ref_command'),
        ]);

        $resultat = $this->paymentService->traiterCallbackIPN($payload);

        return response()->json([
            'success' => $resultat['success'],
            'message' => $resultat['message'],
        ], $resultat['success'] ? 200 : 422);
    }

    // ✅ Verrou DB + idempotence pour éviter les doubles activations.
    // PayTech redirige vers success_url?ref={ref_command} après paiement.
    // On utilise ref_command (pas un token) pour identifier le paiement.
    public function succes(Request $request): View|RedirectResponse
    {
        $ref    = $request->query('ref');
        $agency = Auth::user()->agency;

        // Un utilisateur sans agence ne devrait jamais atteindre cette page,
        // mais on guard explicitement pour satisfaire l'analyse statique.
        if (! $agency) {
            return redirect()->route('subscription.index');
        }

        if (! $ref) {
            return redirect()->route('subscription.index')
                ->with('info', 'Votre paiement est en cours de traitement.');
        }

        try {
            $planSession       = session('subscription_plan_pending');
            $planNiveauSession = session('subscription_plan_niveau_pending', 'pro');

            if (! $planSession || ! array_key_exists($planSession, Subscription::LABELS)) {
                // Si la session a expiré, on vérifie via l'IPN déjà traité
                if (SubscriptionPayment::where('reference', $ref)->where('statut', 'payé')->exists()) {
                    session()->forget(['subscription_plan_pending', 'subscription_plan_niveau_pending', 'subscription_agency_id']);
                    return view('subscription.succes', compact('agency'));
                }

                return redirect()->route('subscription.index')
                    ->with('info', 'Votre paiement est en cours de vérification. Veuillez patienter.');
            }

            // Activation avec verrou — impossible de l'activer deux fois en même temps
            DB::transaction(function () use ($agency, $planSession, $planNiveauSession, $ref) {
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

                if (! SubscriptionPayment::where('reference', $ref)->exists()) {
                    $subscription->activer($planSession, $ref, 'paytech', $planNiveauSession);
                }
            });

            session()->forget(['subscription_plan_pending', 'subscription_plan_niveau_pending', 'subscription_agency_id']);

            return view('subscription.succes', compact('agency'));

        } catch (\Throwable $e) {
            Log::error('Erreur retour PayTech succes()', [
                'ref'       => $ref,
                'agency_id' => $agency->id,
                'error'     => $e->getMessage(),
            ]);
            return redirect()->route('subscription.index')
                ->with('info', 'Votre paiement est en cours de vérification.');
        }
    }

    public function echec(): View
    {
        session()->forget(['subscription_plan_pending', 'subscription_plan_niveau_pending', 'subscription_agency_id']);
        return view('subscription.echec');
    }
}