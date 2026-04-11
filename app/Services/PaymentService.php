<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PaymentService — Gestion des paiements d'abonnement via PayDunya.
 *
 * Trois modes (config services.paydunya.mode) :
 *  - simulation : aucun appel API, abonnement activé directement (dev/test)
 *  - test       : sandbox PayDunya
 *  - live       : production PayDunya
 */
class PaymentService
{
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

    // ═══════════════════════════════════════════════════════════════════════
    // API PUBLIQUE
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Initie un paiement d'abonnement.
     *
     * @return array{success: bool, mode: string, message: string, redirect_url: ?string}
     */
    public function initierPaiement(Agency $agency, string $plan): array
    {
        if (! array_key_exists($plan, Subscription::TARIFS)) {
            return ['success' => false, 'mode' => $this->mode, 'message' => 'Plan invalide', 'redirect_url' => null];
        }

        if ($this->mode === 'simulation') {
            return $this->simulerPaiement($agency, $plan);
        }

        return $this->creerFacturePaydunya($agency, $plan);
    }

    /**
     * Traite un callback IPN (Instant Payment Notification) de PayDunya.
     *
     * @return array{success: bool, message: string}
     */
    public function traiterCallbackIPN(array $payload): array
    {
        try {
            $token  = $payload['data']['invoice']['token']  ?? null;
            $status = $payload['data']['invoice']['status'] ?? null;

            if (! $token || $status !== 'completed') {
                return ['success' => false, 'message' => 'Paiement non complété ou token absent'];
            }

            $agencyId = $payload['data']['invoice']['custom_data']['agency_id'] ?? null;
            $plan     = $payload['data']['invoice']['custom_data']['plan']      ?? null;

            if (! $agencyId || ! $plan) {
                return ['success' => false, 'message' => 'Données custom_data manquantes'];
            }

            DB::transaction(function () use ($agencyId, $plan, $token) {
                // Idempotence — on ne traite pas deux fois le même token
                if (SubscriptionPayment::where('reference', $token)->exists()) {
                    return;
                }

                $subscription = Subscription::where('agency_id', $agencyId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $subscription->activer($plan, $token, 'paydunya');
            });

            return ['success' => true, 'message' => 'Abonnement activé'];

        } catch (\Throwable $e) {
            Log::error('Erreur traitement IPN PayDunya', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
            return ['success' => false, 'message' => 'Erreur serveur interne'];
        }
    }

    /**
     * Vérifie le statut d'une facture PayDunya.
     * En simulation : retourne toujours un statut "completed".
     *
     * @return array{status: string, custom_data: array}|null
     */
    public function verifierStatutFacture(string $token): ?array
    {
        if ($this->mode === 'simulation') {
            return ['status' => 'completed', 'custom_data' => []];
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->get($this->baseUrl() . "/checkout-invoice/confirm/{$token}");

            return $response->successful() ? $response->json() : null;

        } catch (\Throwable $e) {
            Log::error('Erreur vérification facture PayDunya', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    // ═══════════════════════════════════════════════════════════════════════
    // PRIVÉ
    // ═══════════════════════════════════════════════════════════════════════

    private function simulerPaiement(Agency $agency, string $plan): array
    {
        DB::transaction(function () use ($agency, $plan) {
            $subscription = $agency->subscription;

            if (! $subscription) {
                $subscription = Subscription::create([
                    'agency_id'        => $agency->id,
                    'statut'           => 'essai',
                    'date_debut_essai' => now(),
                    'date_fin_essai'   => now()->addDays(30),
                ]);
            }

            $ref = 'SIM-' . strtoupper(substr(md5(uniqid()), 0, 12));
            $subscription->activer($plan, $ref, 'manuel');
        });

        return [
            'success'      => true,
            'mode'         => 'simulation',
            'message'      => 'Paiement simulé avec succès',
            'redirect_url' => null,
        ];
    }

    private function creerFacturePaydunya(Agency $agency, string $plan): array
    {
        try {
            $montant = Subscription::TARIFS[$plan];
            $labels  = Subscription::LABELS;

            $response = Http::withHeaders($this->headers())
                ->post($this->baseUrl() . '/checkout-invoice/create', [
                    'invoice' => [
                        'total_amount' => $montant,
                        'description'  => "Abonnement BIMO-Tech {$labels[$plan]} — {$agency->name}",
                    ],
                    'store' => [
                        'name' => 'BIMO-Tech Immobilier',
                    ],
                    'actions' => [
                        'callback_url' => route('subscription.callback'),
                        'return_url'   => route('subscription.succes'),
                        'cancel_url'   => route('subscription.echec'),
                    ],
                    'custom_data' => [
                        'agency_id' => $agency->id,
                        'plan'      => $plan,
                    ],
                ]);

            if ($response->successful() && ($response->json('response_code') === '00')) {
                return [
                    'success'      => true,
                    'mode'         => $this->mode,
                    'message'      => 'Facture PayDunya créée',
                    'redirect_url' => $response->json('response_text'),
                ];
            }

            return [
                'success'      => false,
                'mode'         => $this->mode,
                'message'      => 'Erreur PayDunya : ' . $response->json('response_text', 'Erreur inconnue'),
                'redirect_url' => null,
            ];

        } catch (\Throwable $e) {
            Log::error('Erreur création facture PayDunya', ['error' => $e->getMessage()]);
            return [
                'success'      => false,
                'mode'         => $this->mode,
                'message'      => 'Service de paiement temporairement indisponible',
                'redirect_url' => null,
            ];
        }
    }

    private function baseUrl(): string
    {
        return $this->mode === 'live'
            ? 'https://app.paydunya.com/api/v1'
            : 'https://app.paydunya.com/sandbox-api/v1';
    }

    private function headers(): array
    {
        return [
            'PAYDUNYA-MASTER-KEY'  => $this->masterKey,
            'PAYDUNYA-PRIVATE-KEY' => $this->privateKey,
            'PAYDUNYA-TOKEN'       => $this->token,
            'Content-Type'         => 'application/json',
        ];
    }
}
