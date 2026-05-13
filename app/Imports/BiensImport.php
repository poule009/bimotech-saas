<?php

namespace App\Imports;

use App\Models\Bien;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BiensImport implements ToCollection, WithHeadingRow
{
    public array $errors  = [];
    public int   $created = 0;
    public int   $skipped = 0;

    private int $agencyId;

    public function __construct(int $agencyId)
    {
        $this->agencyId = $agencyId;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $index => $row) {
            $line = $index + 2;

            if ($row->filter(fn($v) => trim((string) $v) !== '')->isEmpty()) {
                continue;
            }

            $titre  = trim($row['titre'] ?? '');
            $type   = strtolower(trim($row['type'] ?? ''));
            $loyer  = $row['loyer_mensuel'] ?? '';

            if (empty($titre)) {
                $this->errors[] = "Ligne {$line} : le titre est obligatoire.";
                $this->skipped++;
                continue;
            }

            if (! in_array($type, array_keys(Bien::TYPES))) {
                $this->errors[] = "Ligne {$line} : type « {$type} » invalide. Valeurs acceptées : " . implode(', ', array_keys(Bien::TYPES));
                $this->skipped++;
                continue;
            }

            if (! is_numeric($loyer) || (float) $loyer <= 0) {
                $this->errors[] = "Ligne {$line} : loyer_mensuel doit être un nombre positif.";
                $this->skipped++;
                continue;
            }

            $adresse = $this->val($row, 'adresse');
            if (! $adresse) {
                $this->errors[] = "Ligne {$line} : l'adresse est obligatoire.";
                $this->skipped++;
                continue;
            }

            // Liaison propriétaire via email (obligatoire — proprietaire_id NOT NULL en base)
            $emailProp = trim(strtolower($row['proprietaire_email'] ?? ''));
            if ($emailProp === '') {
                $this->errors[] = "Ligne {$line} : proprietaire_email est obligatoire.";
                $this->skipped++;
                continue;
            }

            $prop = User::where('email', $emailProp)
                ->where('role', 'proprietaire')
                ->where('agency_id', $this->agencyId)
                ->first();

            if (! $prop) {
                $this->errors[] = "Ligne {$line} : propriétaire « {$emailProp} » introuvable — bien ignoré.";
                $this->skipped++;
                continue;
            }

            $proprietaireId = $prop->id;

            // Limité aux valeurs acceptées par l'enum DB (archive n'existe pas en base)
            $statut = $this->inList($row, 'statut', ['disponible', 'loue', 'en_travaux']) ?? 'disponible';

            try {
                DB::transaction(function () use ($row, $titre, $type, $loyer, $statut, $proprietaireId, $adresse) {
                    Bien::create([
                        'agency_id'       => $this->agencyId,
                        'proprietaire_id' => $proprietaireId,
                        'reference'       => Bien::generateReference($this->agencyId),
                        'titre'           => $titre,
                        'type'            => $type,
                        'adresse'         => $adresse,
                        'ville'           => $this->val($row, 'ville') ?? 'Dakar',
                        'quartier'        => $this->val($row, 'quartier'),
                        'commune'         => $this->val($row, 'commune'),
                        'surface_m2'      => $this->numeric($row, 'surface_m2'),
                        'nombre_pieces'   => $this->integer($row, 'nombre_pieces'),
                        'meuble'          => $this->boolean($row, 'meuble'),
                        'loyer_mensuel'   => (float) $loyer,
                        'taux_commission' => $this->numeric($row, 'taux_commission'),
                        'statut'          => $statut,
                        'description'     => $this->val($row, 'description'),
                    ]);
                });

                $this->created++;
            } catch (\Throwable $e) {
                $this->errors[] = "Ligne {$line} : erreur — {$e->getMessage()}";
                $this->skipped++;
            }
        }
    }

    private function val($row, string $key): ?string
    {
        $v = trim((string) ($row[$key] ?? ''));
        return $v !== '' ? $v : null;
    }

    private function inList($row, string $key, array $allowed): ?string
    {
        $v = strtolower(trim((string) ($row[$key] ?? '')));
        return in_array($v, $allowed) ? $v : null;
    }

    private function numeric($row, string $key): ?float
    {
        $v = $this->val($row, $key);
        return $v !== null && is_numeric($v) ? (float) $v : null;
    }

    private function integer($row, string $key): ?int
    {
        $v = $this->val($row, $key);
        return $v !== null && is_numeric($v) ? (int) $v : null;
    }

    private function boolean($row, string $key): bool
    {
        $v = strtolower(trim((string) ($row[$key] ?? '')));
        return in_array($v, ['oui', '1', 'true', 'yes']);
    }
}
