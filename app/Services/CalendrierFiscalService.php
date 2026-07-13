<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;

/**
 * CalendrierFiscalService — Agrégation des échéances fiscales à venir.
 *
 * Module d'AGRÉGATION : ne calcule AUCUNE nouvelle règle fiscale, il relie les
 * données déjà produites par les modules TVA, BRS, Droits d'enregistrement, IRPP,
 * CGF, CFPB, TEOM + les 3 échéances agence (IS, CEL-VL, CEL-VA, sans montant).
 *
 * Répond à : « Qu'est-ce qui est dû, par qui, à quelle date, dans l'horizon donné ? »
 * croisé sur tous les Propriétaires / Biens / Contrats de l'agence.
 *
 * Sortie : liste d'items { type, libelle, proprietaire, proprietaire_id,
 *   date_limite (Y-m-d|null), montant (int|null), statut_calcul (string|null) }
 * triée par date croissante (dates nulles en dernier).
 */
class CalendrierFiscalService
{
    /** Lectures scope-proof (indépendant du contexte Auth). */
    public function echeancesAVenir(int $agencyId, int $horizonJours = 30, ?Carbon $reference = null): array
    {
        $ref   = ($reference ?? now())->copy()->startOfDay();
        $fin   = $ref->copy()->addDays($horizonJours)->endOfDay();
        $annee = (int) $ref->year;

        $items = [];

        // ── Échéances liées aux Propriétaires ────────────────────────────────
        $proprietaires = User::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->get(['id', 'name', 'agency_id']);

        foreach ($proprietaires as $prop) {
            $profil = Proprietaire::withoutGlobalScopes()->where('user_id', $prop->id)->first();

            $assujettiTva = (bool) ($profil?->assujetti_tva ?? false);
            $estMorale    = (bool) ($profil?->est_personne_morale_is ?? false);
            $brsDispense  = (bool) ($profil?->brs_dispense ?? false);
            $cgfCouvre    = $profil ? $profil->cgfCouvre($annee) : false;

            // TVA — déclaration mensuelle (15 du mois), montant déjà calculé (module TVA).
            if ($assujettiTva) {
                $date = $this->prochainJourFixe(15, $ref);
                $items[] = $this->item(
                    'tva',
                    'TVA — déclaration mensuelle',
                    $prop,
                    $date,
                    $this->sommeMois($prop->id, $agencyId, $date->copy()->subMonthNoOverflow(), ['tva_loyer', 'tva_charges']),
                    'confirme'
                );
            }

            // BRS — mensuelle + récap annuel (bailleur personne physique non dispensé).
            if (! $estMorale && ! $brsDispense) {
                $dateMens = $this->prochainJourFixe(15, $ref);
                $items[] = $this->item(
                    'brs_mensuel',
                    'BRS — déclaration mensuelle',
                    $prop,
                    $dateMens,
                    $this->sommeMois($prop->id, $agencyId, $dateMens->copy()->subMonthNoOverflow(), ['brs_amount']),
                    'confirme'
                );

                $dateAnn = $this->prochaineDate(1, 31, $ref); // 31 janvier
                $items[] = $this->item(
                    'brs_annuel',
                    'BRS — récapitulatif annuel',
                    $prop,
                    $dateAnn,
                    $this->sommeAnnee($prop->id, $agencyId, (int) $dateAnn->copy()->subYear()->year, ['brs_amount']),
                    'confirme'
                );
            }

            if ($cgfCouvre) {
                // CGF — déclaration (1er février) + versement(s) selon le mode.
                $items[] = $this->item(
                    'cgf_declaration',
                    'CGF — déclaration',
                    $prop,
                    $this->prochaineDate(2, 1, $ref),
                    (int) ($profil->cgf_montant ?? 0),
                    'confirme'
                );

                foreach (($profil->cgf_echeances ?? []) as $ech) {
                    if (empty($ech['date'])) {
                        continue;
                    }
                    $items[] = $this->item(
                        'cgf_versement',
                        'CGF — versement (' . ($ech['libelle'] ?? 'échéance') . ')',
                        $prop,
                        Carbon::parse($ech['date'])->startOfDay(),
                        (int) round($ech['montant'] ?? 0),
                        'confirme'
                    );
                }
            } else {
                // Hors CGF : IRPP foncier (Particulier) + CFPB+TEOM par bien loué.
                if (! $estMorale) {
                    $irpp = FiscalService::estimerIrppFoncier($prop->id, $annee, $agencyId);
                    $items[] = $this->item(
                        'irpp',
                        'IRPP foncier — déclaration',
                        $prop,
                        $this->prochaineDate(3, 1, $ref), // 1er mars
                        (int) round($irpp['montant_estime'] ?? 0),
                        $irpp['statut_calcul'] ?? 'perimetre_partiel'
                    );
                }

                $biens = Bien::withoutGlobalScopes()
                    ->where('proprietaire_id', $prop->id)
                    ->where('statut', 'loue')
                    ->get(['id', 'titre', 'reference', 'cfpb_montant_estime', 'teom_montant_estime']);

                foreach ($biens as $bien) {
                    // Regroupement OBLIGATOIRE CFPB + TEOM sur une seule ligne (même bien, même date, même base).
                    $montant = (int) $bien->cfpb_montant_estime + (int) $bien->teom_montant_estime;
                    $items[] = $this->item(
                        'cfpb_teom',
                        'CFPB + TEOM — Bien : ' . ($bien->titre ?: $bien->reference),
                        $prop,
                        $this->prochaineDate(1, 31, $ref), // 31 janvier
                        $montant,
                        'estimation_structurelle'
                    );
                }
            }
        }

        // ── Droits d'enregistrement — par Contrat non encore enregistré ──────
        $contrats = Contrat::withoutGlobalScopes()
            ->where('agency_id', $agencyId)
            ->where('droit_enreg_effectue', false)
            ->whereNotNull('droit_enreg_date_limite')
            ->with(['bien:id,proprietaire_id,titre,reference', 'bien.proprietaire:id,name'])
            ->get();

        foreach ($contrats as $contrat) {
            $prop = $contrat->bien?->proprietaire;
            $items[] = $this->item(
                'droit_enregistrement',
                'Droits d\'enregistrement — Bien : ' . ($contrat->bien?->titre ?: $contrat->bien?->reference ?: '—'),
                $prop,
                $contrat->droit_enreg_date_limite?->copy()->startOfDay(),
                (int) round($contrat->droit_enreg_total),
                $contrat->droit_enreg_statut_calcul ?? 'confirme'
            );
        }

        // ── Filtrage sur l'horizon (échéances entités) ───────────────────────
        $items = array_values(array_filter($items, function (array $e) use ($ref, $fin) {
            if ($e['date_limite'] === null) {
                return false; // les entités ont toujours une date
            }
            $d = Carbon::parse($e['date_limite']);
            return $d->betweenIncluded($ref, $fin);
        }));

        // ── Échéances AGENCE (toujours affichées, une fois par an, sans montant) ──
        foreach ($this->echeancesAgence($ref) as $agence) {
            $items[] = $agence;
        }

        // ── Tri par date croissante (dates nulles en dernier) ────────────────
        usort($items, function (array $a, array $b) {
            if ($a['date_limite'] === $b['date_limite']) {
                return 0;
            }
            if ($a['date_limite'] === null) {
                return 1;
            }
            if ($b['date_limite'] === null) {
                return -1;
            }
            return strcmp($a['date_limite'], $b['date_limite']);
        });

        return $items;
    }

    /** Les 3 échéances de l'agence — rappel calendrier SANS montant (jamais calculé). */
    private function echeancesAgence(Carbon $ref): array
    {
        return [
            [
                'type'          => 'is_agence',
                'libelle'       => 'Déclaration IS de l\'agence — à traiter avec votre comptable.',
                'proprietaire'  => null,
                'proprietaire_id' => null,
                'date_limite'   => null, // date dépend de la clôture d'exercice (non vérifiée)
                'montant'       => null,
                'statut_calcul' => 'non_verifie',
            ],
            [
                'type'          => 'cel_vl_agence',
                'libelle'       => 'Déclaration CEL (valeur locative) de l\'agence — à traiter avec votre comptable.',
                'proprietaire'  => null,
                'proprietaire_id' => null,
                'date_limite'   => $this->prochaineDate(1, 31, $ref)->format('Y-m-d'), // 31 janvier
                'montant'       => null,
                'statut_calcul' => null,
            ],
            [
                'type'          => 'cel_va_agence',
                'libelle'       => 'Déclaration CEL (valeur ajoutée) de l\'agence — à traiter avec votre comptable.',
                'proprietaire'  => null,
                'proprietaire_id' => null,
                'date_limite'   => $this->prochaineDate(4, 30, $ref)->format('Y-m-d'), // 30 avril
                'montant'       => null,
                'statut_calcul' => null,
            ],
        ];
    }

    /** Construit un item d'échéance normalisé. */
    private function item(string $type, string $libelle, ?User $prop, ?Carbon $date, ?int $montant, ?string $statut): array
    {
        return [
            'type'            => $type,
            'libelle'         => $libelle,
            'proprietaire'    => $prop?->name,
            'proprietaire_id' => $prop?->id,
            'date_limite'     => $date?->format('Y-m-d'),
            'montant'         => $montant,
            'statut_calcul'   => $statut,
        ];
    }

    /** Prochaine occurrence d'une date fixe (mois/jour) ≥ référence. */
    private function prochaineDate(int $mois, int $jour, Carbon $ref): Carbon
    {
        $candidate = Carbon::create($ref->year, $mois, $jour)->startOfDay();
        if ($candidate->lt($ref)) {
            $candidate = Carbon::create($ref->year + 1, $mois, $jour)->startOfDay();
        }
        return $candidate;
    }

    /** Prochaine occurrence d'un jour fixe du mois (ex. le 15) ≥ référence. */
    private function prochainJourFixe(int $jour, Carbon $ref): Carbon
    {
        $candidate = $ref->copy()->day($jour)->startOfDay();
        if ($candidate->lt($ref)) {
            $candidate = $ref->copy()->addMonthNoOverflow()->day($jour)->startOfDay();
        }
        return $candidate;
    }

    /** Somme de champs paiements (valides) d'un propriétaire sur un mois donné. */
    private function sommeMois(int $propId, int $agencyId, Carbon $mois, array $champs): int
    {
        return (int) round($this->requeteSomme($propId, $agencyId, $champs)
            ->whereYear('paiements.periode', $mois->year)
            ->whereMonth('paiements.periode', $mois->month)
            ->value('total'));
    }

    /** Somme de champs paiements (valides) d'un propriétaire sur une année. */
    private function sommeAnnee(int $propId, int $agencyId, int $annee, array $champs): int
    {
        return (int) round($this->requeteSomme($propId, $agencyId, $champs)
            ->whereYear('paiements.periode', $annee)
            ->value('total'));
    }

    /** Requête de base (scope-proof) pour sommer des champs de paiements valides. */
    private function requeteSomme(int $propId, int $agencyId, array $champs)
    {
        $expr = implode(' + ', array_map(fn ($c) => "COALESCE(paiements.$c, 0)", $champs));

        return Paiement::query()
            ->join('contrats', 'contrats.id', '=', 'paiements.contrat_id')
            ->join('biens', 'biens.id', '=', 'contrats.bien_id')
            ->where('biens.proprietaire_id', $propId)
            ->where('paiements.agency_id', $agencyId)
            ->where('paiements.statut', 'valide')
            ->selectRaw("COALESCE(SUM($expr), 0) as total");
    }
}
