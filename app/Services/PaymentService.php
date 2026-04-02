<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private const PAYDUNYA_CHECKOUT_URL_TEST = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/create';
    private const PAYDUNYA_CHECKOUT_URL_LIVE = 'https://app.paydunya.com/api/v1/checkout-invoice/create';
    private const PAYDUNYA_CONFIRM_URL_TEST  = 'https://app.paydunya.com/sandbox-api/v1/checkout-invoice/confirm/';
    private const PAYDUNYA_CONFIRM_URL_LIVE  = 'https://app.paydunya.com/api/v1/checkout-invoice/confirm/';

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

    public function initierPaydunya(Agency $agency, string $plan): array
    {
        $montant = Subscription::TARIFS[$plan];
        $label   = Subscription::LABELS[$plan];

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
                'total_amount' => $montant,
                'description'  => "Abonnement BimoTech Immo {$label} — {$agency->name}",
            ],
            'store' => [
                'name'           => 'BimoTech Immo',
                'tagline'        => 'Gestion immobilière au Sénégal',
                'postal_address' => 'Dakar, Sénégal',
                'phone_number'   => '+221 33 000 00 00',
                'logo_url'       => config('app.url') . '/images/logo.png',
                'website_url'    => config('app.url'),
            ],
            'actions' => [
                'cancel_url'   => route('subscription.echec'),
                'return_url'   => route('subscription.succes'),
                'callback_url' => route('subscription.callback'),
            ],
            // Le plan voyage avec PayDunya — on ne dépend plus de la session
            'custom_data' => [
                'agency_id' => $agency->id,
                'plan'      => $plan,
            ],
        ];

        try {
            $apiUrl   = $this->mode === 'live'
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

    // ✅ CORRECTION C1 : on vérifie que c'est bien PayDunya qui appelle
    // Avant : aucune vérification → n'importe qui pouvait activer un abonnement gratuitement
    // Après : on compare le Master Key reçu dans le header avec celui de ton .env
    public function traiterCallbackIPN(array $payload): array
    {
        try {
            // Étape 1 : vérification de la signature PayDunya
            $incomingMasterKey = request()->header('PAYDUNYA-MASTER-KEY', '');

            if (! hash_equals($this->masterKey, $incomingMasterKey)) {
                Log::warning('IPN PayDunya: Master Key invalide — requête rejetée', [
                    'ip' => request()->ip(),
                ]);
                return ['success' => false, 'message' => 'Signature invalide.'];
            }

            // Étape 2 : extraction des données
            $token    = $payload['data']['invoice']['token'] ?? null;
            $agencyId = $payload['custom_data']['agency_id'] ?? null;
            $plan     = $payload['custom_data']['plan']      ?? null;
            $statut   = $payload['data']['invoice']['status'] ?? null;

            if (! $token) {
                return ['success' => false, 'message' => 'Token manquant dans le callback.'];
            }

            if (! $agencyId || ! $plan) {
                return ['success' => false, 'message' => 'Données custom manquantes.'];
            }

            if (! array_key_exists($plan, Subscription::TARIFS)) {
                return ['success' => false, 'message' => "Plan invalide : {$plan}"];
            }

            // Étape 3 : vérification du statut du paiement
            if ($statut !== 'completed') {
                Log::info('Callback PayDunya — paiement non complété', [
                    'agency_id' => $agencyId,
                    'statut'    => $statut,
                    'token'     => $token,
                ]);
                return ['success' => false, 'message' => "Statut : {$statut}"];
            }

            // Étape 4 : idempotence — si déjà traité, on ne fait rien
            if (SubscriptionPayment::where('reference', $token)->exists()) {
                Log::info('Callback PayDunya — token déjà traité', ['token' => $token]);
                return ['success' => true, 'message' => 'Déjà traité.'];
            }

            // Étape 5 : activation dans une transaction atomique avec verrou
            $agency = Agency::find($agencyId);
            if (! $agency) {
                return ['success' => false, 'message' => "Agence #{$agencyId} introuvable."];
            }

            DB::transaction(function () use ($agency, $plan, $token) {
                $subscription = Subscription::where('agency_id', $agency->id)
                    ->lockForUpdate() // verrou → empêche la double activation simultanée
                    ->first()
                    ?? Subscription::firstOrCreate(['agency_id' => $agency->id]);

                if (! SubscriptionPayment::where('reference', $token)->exists()) {
                    $subscription->activer($plan, $token, 'paydunya');
                }
            });

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
            return ['success' => false, 'message' => 'Erreur interne.'];
        }
    }

    public function verifierStatutFacture(string $token): ?array
    {
        try {
            $baseUrl = $this->mode === 'live'
                ? self::PAYDUNYA_CONFIRM_URL_LIVE
                : self::PAYDUNYA_CONFIRM_URL_TEST;

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