<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PaymentService — Gestion des paiements d'abonnement via PayTech.
 *
 * Documentation API : https://docs.intech.sn/doc_paytech.php
 *
 * Trois modes (config services.paytech.mode) :
 *  - simulation : aucun appel API, abonnement activé directement (dev/test local)
 *  - test       : sandbox PayTech (débite 100-150 XOF aléatoires, pas le vrai montant)
 *  - prod       : production PayTech (débite le montant exact)
 */
class PaymentService
{
    private const BASE_URL = 'https://paytech.sn/api';

    private string $mode;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $this->mode      = config('services.paytech.mode', 'simulation');
        $this->apiKey    = config('services.paytech.api_key', '');
        $this->apiSecret = config('services.paytech.api_secret', '');
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

        return $this->creerFacturePaytech($agency, $plan);
    }

    /**
     * Traite un callback IPN (Instant Payment Notification) de PayTech.
     *
     * PayTech envoie un POST avec les champs suivants :
     *  - type_event      : 'sale_complete' | 'sale_canceled'
     *  - ref_command     : référence unique de la commande
     *  - item_price      : montant
     *  - custom_field    : données custom encodées en base64 (JSON)
     *  - api_key_sha256  : SHA256 de l'API key (vérification méthode 2)
     *  - api_secret_sha256 : SHA256 de l'API secret
     *  - hmac_compute    : HMAC-SHA256(amount|ref_command|api_key, api_secret)
     *
     * @return array{success: bool, message: string}
     */
    public function traiterCallbackIPN(array $payload): array
    {
        try {
            $typeEvent = $payload['type_event'] ?? null;

            if ($typeEvent !== 'sale_complete') {
                Log::info('IPN PayTech ignoré — événement non traité', ['type_event' => $typeEvent]);
                return ['success' => false, 'message' => 'Événement non traité'];
            }

            $refCommand  = $payload['ref_command']  ?? null;
            $customField = $payload['custom_field'] ?? null;

            if (! $refCommand) {
                return ['success' => false, 'message' => 'ref_command manquante'];
            }

            // Décoder les données custom (base64 + JSON)
            $customData = [];
            if ($customField) {
                $decoded = base64_decode($customField, true);
                if ($decoded !== false) {
                    $customData = json_decode($decoded, true) ?? [];
                }
            }

            $agencyId = $customData['agency_id'] ?? null;
            $plan     = $customData['plan']      ?? null;

            if (! $agencyId || ! $plan) {
                Log::error('IPN PayTech — custom_data manquantes', ['payload' => $payload]);
                return ['success' => false, 'message' => 'Données custom_data manquantes'];
            }

            if (! array_key_exists($plan, Subscription::TARIFS)) {
                Log::error('IPN PayTech — plan invalide', ['plan' => $plan, 'ref' => $refCommand]);
                return ['success' => false, 'message' => 'Plan invalide'];
            }

            DB::transaction(function () use ($agencyId, $plan, $refCommand) {
                // Idempotence — on ne traite pas deux fois la même commande
                if (SubscriptionPayment::where('reference', $refCommand)->exists()) {
                    return;
                }

                $subscription = Subscription::where('agency_id', $agencyId)
                    ->lockForUpdate()
                    ->firstOrFail();

                $subscription->activer($plan, $refCommand, 'paytech');
            });

            return ['success' => true, 'message' => 'Abonnement activé'];

        } catch (\Throwable $e) {
            Log::error('Erreur traitement IPN PayTech', [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
            return ['success' => false, 'message' => 'Erreur serveur interne'];
        }
    }

    /**
     * Vérifie le statut d'une facture PayTech via son token.
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
                ->get(self::BASE_URL . "/payment/get-status?token_payment={$token}");

            if (! $response->successful()) {
                return null;
            }

            $data   = $response->json();
            $status = $data['success'] === 1 ? 'completed' : 'pending';

            return [
                'status'      => $status,
                'custom_data' => $data['custom_field'] ?? [],
            ];

        } catch (\Throwable $e) {
            Log::error('Erreur vérification statut PayTech', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Vérifie l'authenticité d'un webhook IPN PayTech.
     *
     * Deux méthodes de vérification supportées par PayTech :
     *
     * Méthode 1 (HMAC) — recommandée et obligatoire en production :
     *   HMAC-SHA256(amount|ref_command|api_key, api_secret) == hmac_compute
     *
     * Méthode 2 (SHA256 simple) — fallback accepté uniquement en mode test :
     *   SHA256(api_key) == api_key_sha256
     *   SHA256(api_secret) == api_secret_sha256
     *
     * En production, le fallback SHA256 est rejeté : un attaquant connaissant
     * les hashes pourrait forger un webhook sans détenir le vrai secret HMAC.
     */
    public function verifierSignatureIPN(array $payload): bool
    {
        // Méthode 1 — HMAC (si le champ est présent)
        if (! empty($payload['hmac_compute'])) {
            $amount     = $payload['item_price']  ?? '';
            $refCommand = $payload['ref_command'] ?? '';
            $message    = "{$amount}|{$refCommand}|{$this->apiKey}";
            $expected   = hash_hmac('sha256', $message, $this->apiSecret);

            return hash_equals($expected, $payload['hmac_compute']);
        }

        // Méthode 2 — SHA256 (fallback, refusé en production)
        if (! empty($payload['api_key_sha256']) && ! empty($payload['api_secret_sha256'])) {
            if ($this->mode === 'prod') {
                Log::warning('IPN PayTech — fallback SHA256 rejeté en production (HMAC requis)', [
                    'ip'          => request()->ip(),
                    'ref_command' => $payload['ref_command'] ?? null,
                ]);
                return false;
            }

            $keyValid    = hash_equals(hash('sha256', $this->apiKey),    $payload['api_key_sha256']);
            $secretValid = hash_equals(hash('sha256', $this->apiSecret), $payload['api_secret_sha256']);

            return $keyValid && $secretValid;
        }

        return false;
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

    private function creerFacturePaytech(Agency $agency, string $plan): array
    {
        try {
            $montant    = Subscription::TARIFS[$plan];
            $labels     = Subscription::LABELS;
            $refCommand = 'BIMO-' . $agency->id . '-' . strtoupper(substr(md5(uniqid()), 0, 8));

            // Les données custom sont encodées en base64 (JSON) pour PayTech
            $customField = base64_encode(json_encode([
                'agency_id' => $agency->id,
                'plan'      => $plan,
            ]));

            $response = Http::withHeaders($this->headers())
                ->asForm()
                ->post(self::BASE_URL . '/payment/request-payment', [
                    'item_name'    => "Abonnement BIMO-Tech {$labels[$plan]}",
                    'item_price'   => $montant,
                    'currency'     => 'XOF',
                    'ref_command'  => $refCommand,
                    'command_name' => "Abonnement {$labels[$plan]} — {$agency->name}",
                    'env'          => $this->mode === 'prod' ? 'prod' : 'test',
                    'ipn_url'      => route('subscription.callback'),
                    'success_url'  => route('subscription.succes') . '?ref=' . $refCommand,
                    'cancel_url'   => route('subscription.echec'),
                    'custom_field' => $customField,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? 0) === 1) {
                $redirectUrl = $data['redirect_url'] ?? $data['redirectUrl'] ?? null;

                return [
                    'success'      => true,
                    'mode'         => $this->mode,
                    'message'      => 'Facture PayTech créée',
                    'redirect_url' => $redirectUrl,
                    'token'        => $data['token'] ?? null,
                ];
            }

            Log::error('Erreur création facture PayTech', ['response' => $data]);

            return [
                'success'      => false,
                'mode'         => $this->mode,
                'message'      => 'Erreur PayTech : ' . ($data['message'] ?? 'Erreur inconnue'),
                'redirect_url' => null,
            ];

        } catch (\Throwable $e) {
            Log::error('Exception création facture PayTech', ['error' => $e->getMessage()]);
            return [
                'success'      => false,
                'mode'         => $this->mode,
                'message'      => 'Service de paiement temporairement indisponible',
                'redirect_url' => null,
            ];
        }
    }

    private function headers(): array
    {
        return [
            'API_KEY'      => $this->apiKey,
            'API_SECRET'   => $this->apiSecret,
            'Content-Type' => 'application/json',
        ];
    }
}
