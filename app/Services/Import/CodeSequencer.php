<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\DB;

/**
 * Génère les codes de liaison séquentiels par agence (P-0001, B-0001, L-0001).
 *
 * L'unicité par agence est garantie ici (pas par contrainte DB) : proprietaires et
 * locataires n'ont pas de colonne agency_id (scopés via users.agency_id). Le max
 * existant est calculé côté PHP pour rester portable et lisible ; l'appelant DOIT
 * invoquer ce séquenceur DANS une transaction (le commit d'import l'est) pour que la
 * lecture du max et l'insertion des nouveaux codes forment une unité cohérente.
 */
class CodeSequencer
{
    /** Préfixe de code par type d'entité. */
    private const PREFIXES = [
        'proprietaires' => 'P',
        'biens'         => 'B',
        'locataires'    => 'L',
    ];

    /**
     * Prochaine valeur de séquence (entier) pour une agence et un type donnés.
     * Ex. si le dernier code est P-0011 → retourne 12.
     */
    public function prochaineSequence(int $agencyId, string $type): int
    {
        $prefixe = self::PREFIXES[$type] ?? null;
        if ($prefixe === null) {
            return 1;
        }

        $codes = $this->codesExistants($agencyId, $type);

        $max = 0;
        foreach ($codes as $code) {
            // Format attendu : {P|B|L}-{NNNN}
            if (preg_match('/-(\d+)$/', (string) $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $max + 1;
    }

    /** Formatte un numéro de séquence en code (12 → P-0012). */
    public function formater(string $type, int $sequence): string
    {
        $prefixe = self::PREFIXES[$type] ?? 'X';
        return $prefixe . '-' . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Codes déjà attribués dans l'agence pour ce type (verrou pessimiste).
     * proprietaires/locataires : jointure users pour le filtre agence.
     */
    private function codesExistants(int $agencyId, string $type): array
    {
        $q = DB::table($type)->whereNotNull($type . '.code_import');

        if ($type === 'biens') {
            $q->where('biens.agency_id', $agencyId);
        } else {
            $q->join('users', 'users.id', '=', $type . '.user_id')
              ->where('users.agency_id', $agencyId);
        }

        return $q->lockForUpdate()->pluck($type . '.code_import')->all();
    }
}
