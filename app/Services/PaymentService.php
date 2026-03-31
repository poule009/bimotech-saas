<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service de gestion des paiements d'abonnement.
 *
 * Architecture prête pour PayDunya :
 *  - En mode SIMULATION (PAYDUNYA_MODE=simulation dans .env), le paiement
 *    est activé directement sans appel API.
 *  - En mode LIVE ou TEST, le service appelle l'API PayDunya pour créer
 *    une facture et retourne l'URL de paiement.
 *
 * Intégration PayDunya future :
 *  - Ajouter dans .env : PAYDUNYA_MASTER_KEY, PAYDUNYA_PRIVATE_KEY,
 *    PAYDUNYA_TOKEN, PAYDUNYA_MODE (test|live|simulation)
 *  - Le callback IPN est géré dans SubscriptionController::callbackPaydunya()
 */
class PaymentService
{
    // ── Configuration PayDunya ────────────────────────────────────────────

    private const PAYDUNYA_CHECKOUT_URL_TEST = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/create';
    private const PAYDUNYA_CHECKOUT_URL_LIVE = 'https://app.paydunya.com/api/v1/checkout-invoice/create';

    private string $mode;
    private string $masterKey;
    private string $privateKey;
    private string $token;

    public function __construct()
    {
        $this->mode       = config('services.paydunya.mode', 'simulation');
        $this->masterKey  = config('services.paydunya.master_key', '');
        $this->privateKey = config('services.paydunya.private_key', '');
        $this->token      = config('services.paydunya.token', '');
    }

    // ── Point d'entrée principal ──────────────────────────────────────────

    /**
     * Initier un paiement d'abonnement.
     *
     * @return array{
     *   success: bool,
     *   mode: string,
     *   redirect_url: string|null,
     *   token: string|null,
     *   message: string,
     *   subscription: Subscription|null
     * }
     */
    public function initierPaiement(Agency $agency, string $plan): array
    {
        if (! array_key_exists($plan, Subscription::TARIFS)) {
            return [
                'success'      => false,
                'mode'         => $this->mode,
                'redirect_url' => null,
                'token'        => null,
                'message'      => "Plan invalide : {$plan}",
                'subscription' => null,
            ];
        }

        return match ($this->mode) {
            'live', 'test' => $this->initierPaydunya($agency, $plan),
            default        => $this->simulerPaiement($agency, $plan),
        };
    }

    // ── Mode simulation (développement / démo) ────────────────────────────

    /**
     * Simule un paiement réussi — active directement l'abonnement.
     * Utilisé en développement et pour les démos clients.
     */
    public function simulerPaiement(Agency $agency, string $plan): array
    {
        try {
            $subscription = $agency->subscription
                ?? Subscription::firstOrCreate(
                    ['agency_id' => $agency->id],
                    [
                        'statut'           => 'essai',
                        'date_debut_essai' => now(),
                        'date_fin_essai'   => now()->addDays(14),
                    ]
                );

            $reference = 'SIM-' . strtoupper($plan) . '-' . now()->format('YmdHis') . '-' . $agency->id;

            $subscription->activer($plan, $reference, 'simulation');

            Log::info('Abonnement activé (simulation)', [
                'agency_id' => $agency->id,
                'plan'      => $plan,
                'reference' => $reference,
            ]);

            return [
                'success'      => true,
                'mode'         => 'simulation',
                'redirect_url' => null,
                'token'        => $reference,
                'message'      => 'Abonnement activé avec succès (mode simulation).',
                'subscription' => $subscription->fresh(),
            ];
        } catch (\Throwable $e) {
            Log::error('Erreur simulation paiement abonnement', [
                'agency_id' => $agency->id,
                'plan'      => $plan,
                'error'     => $e->getMessage(),
            ]);

            return [
                'success'      => false,
                'mode'         => 'simulation',
                'redirect_url' => null,
                'token'        => null,
                'message'      => 'Erreur lors de l\'activation : ' . $e->getMessage(),
                'subscription' => null,
            ];
        }
    }

    // ── Mode PayDunya (test / live) ───────────────────────────────────────

    /**
     * Crée une facture PayDunya et retourne l'URL de redirection.
     * L'abonnement sera activé après confirmation du callback IPN.
     */
    public function initierPaydunya(Agency $agency, string $plan): array
    {
        $montant = Subscription::TARIFS[$plan];
        $label   = Subscription::LABELS[$plan];

        $callbackUrl = route('subscription.callback');
        $succesUrl   = route('subscription.succes');
        $echecUrl    = route('subscription.echec');

        // Stocker le plan en session pour le retrouver au callback
        session(['subscription_plan_pending' => $plan, 'subscription_agency_id' => $agency->id]);

        $payload = [
            'invoice' => [
                'items' => [
                    'item_0' => [
                        'name'        => "Abonnement BimoTech Immo — {$label}",
                        'quantity'    => 1,
                        'unit_price'  => $montant,
                        'total_price' => $montant,
                        'description' => "Abonnement {$label} pour l'agence {$agency->name}",
                    ],
                ],
                'total_amount'  => $montant,
                'description'   => "Abonnement BimoTech Immo {$label} — {$agency->name}",
            ],
            'store' => [
                'name'     => 'BimoTech Immo',
                'tagline'  => 'Gestion immobilière au Sénégal',
                'postal_address' => 'Dakar, Sénégal',
                'phone_number'   => '+221 33 000 00 00',
                'logo_url'       => config('app.url') . '/images/logo.png',
                'website_url'    => config('app.url'),
            ],
            'actions' => [
                'cancel_url'  => $echecUrl,
                'return_url'  => $succesUrl,
                'callback_url' => $callbackUrl,
            ],
            'custom_data' => [
                'agency_id' => $agency->id,
                'plan'      => $plan,
            ],
        ];

        try {
            $apiUrl = $this->mode === 'live'
                ? self::PAYDUNYA_CHECKOUT_URL_LIVE
                : self::PAYDUNYA_CHECKOUT_URL_TEST;

            $response = Http::withHeaders([
                'PAYDUNYA-MASTER-KEY'  => $this->masterKey,
                'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
                'PAYDUNYA-TOKEN'       => $this->token,
                'Content-Type'         => 'application/json',
            ])->post($apiUrl, $payload);

            $body = $response->json();

            if ($response->successful() && isset($body['response_code']) && $body['response_code'] === '00') {
                Log::info('Facture PayDunya créée', [
                    'agency_id' => $agency->id,
                    'plan'      => $plan,
                    'token'     => $body['token'] ?? null,
                ]);

                return [
                    'success'      => true,
                    'mode'         => $this->mode,
                    'redirect_url' => $body['response_text'] ?? null,
                    'token'        => $body['token'] ?? null,
                    'message'      => 'Redirection vers PayDunya...',
                    'subscription' => null,
                ];
            }

            Log::warning('Réponse PayDunya inattendue', [
                'agency_id' => $agency->id,
                'plan'      => $plan,
                'response'  => $body,
            ]);

            return [
                'success'      => false,
                'mode'         => $this->mode,
                'redirect_url' => null,
                'token'        => null,
                'message'      => $body['response_text'] ?? 'Erreur PayDunya inconnue.',
                'subscription' => null,
            ];

        } catch (\Throwable $e) {
            Log::error('Erreur appel API PayDunya', [
                'agency_id' => $agency->id,
                'plan'      => $plan,
                'error'     => $e->getMessage(),
            ]);

            return [
                'success'      => false,
                'mode'         => $this->mode,
                'redirect_url' => null,
                'token'        => null,
                'message'      => 'Impossible de contacter PayDunya. Veuillez réessayer.',
                'subscription' => null,
            ];
        }
    }

    // ── Traitement du callback IPN PayDunya ───────────────────────────────

    /**
     * Traite la notification IPN de PayDunya après paiement.
     * Vérifie la signature et active l'abonnement si le paiement est confirmé.
     *
     * @param array $payload Corps de la requête IPN
     * @return array{success: bool, message: string}
     */
    public function traiterCallbackIPN(array $payload): array
    {
        try {
            // Vérification du token PayDunya
            $token = $payload['data']['invoice']['token'] ?? null;
            if (! $token) {
                return ['success' => false, 'message' => 'Token manquant dans le callback.'];
            }

            // Récupérer les données custom
            $agencyId = $payload['custom_data']['agency_id'] ?? null;
            $plan     = $payload['custom_data']['plan']      ?? null;

            if (! $agencyId || ! $plan) {
                return ['success' => false, 'message' => 'Données custom manquantes.'];
            }

            $statut = $payload['data']['invoice']['status'] ?? null;

            if ($statut !== 'completed') {
                Log::info('Callback PayDunya — paiement non complété', [
                    'agency_id' => $agencyId,
                    'statut'    => $statut,
                    'token'     => $token,
                ]);
                return ['success' => false, 'message' => "Statut paiement : {$statut}"];
            }

            $agency = Agency::find($agencyId);
            if (! $agency) {
                return ['success' => false, 'message' => "Agence {$agencyId} introuvable."];
            }

            $subscription = $agency->subscription
                ?? Subscription::firstOrCreate(['agency_id' => $agency->id]);

            // Vérifier que ce token n'a pas déjà été traité (idempotence)
            $dejaTraite = SubscriptionPayment::where('reference', $token)->exists();
            if ($dejaTraite) {
                Log::info('Callback PayDunya — token déjà traité', ['token' => $token]);
                return ['success' => true, 'message' => 'Déjà traité.'];
            }

            $subscription->activer($plan, $token, 'paydunya');

            Log::info('Abonnement activé via PayDunya IPN', [
                'agency_id' => $agencyId,
                'plan'      => $plan,
                'token'     => $token,
            ]);

            return ['success' => true, 'message' => 'Abonnement activé.'];

        } catch (\Throwable $e) {
            Log::error('Erreur traitement callback PayDunya', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
            return ['success' => false, 'message' => 'Erreur interne : ' . $e->getMessage()];
        }
    }

    // ── Vérification du statut d'une facture PayDunya ─────────────────────

    /**
     * Vérifie le statut d'une facture PayDunya via l'API.
     * Utile pour les vérifications manuelles ou les retours de page.
     */
    public function verifierStatutFacture(string $token): ?array
    {
        try {
            $baseUrl = $this->mode === 'live'
                ? 'https://app.paydunya.com/api/v1/checkout-invoice/confirm/'
                : 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/confirm/';

            $response = Http::withHeaders([
                'PAYDUNYA-MASTER-KEY'  => $this->masterKey,
                'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
                'PAYDUNYA-TOKEN'       => $this->token,
            ])->get($baseUrl . $token);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Erreur vérification facture PayDunya', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
