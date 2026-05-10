<?php

namespace App\Imports;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LocatairesImport implements ToCollection, WithHeadingRow
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

            $nom   = trim($row['nom_complet'] ?? '');
            $email = trim(strtolower($row['email'] ?? ''));

            if (empty($nom) || empty($email)) {
                $this->errors[] = "Ligne {$line} : nom_complet et email sont obligatoires.";
                $this->skipped++;
                continue;
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = "Ligne {$line} : email « {$email} » invalide.";
                $this->skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $this->errors[] = "Ligne {$line} : email « {$email} » déjà utilisé — ligne ignorée.";
                $this->skipped++;
                continue;
            }

            $typeLocataire = $this->inList($row, 'type_locataire', [
                'particulier', 'entreprise', 'association', 'ambassade', 'ong',
            ]) ?? 'particulier';

            $estEntreprise = in_array($typeLocataire, ['entreprise', 'association']);

            try {
                DB::transaction(function () use ($row, $nom, $email, $typeLocataire, $estEntreprise) {
                    $user            = new User();
                    $user->name      = $nom;
                    $user->email     = $email;
                    $user->telephone = $this->val($row, 'telephone');
                    $user->password  = Hash::make(Str::random(16));
                    $user->role      = 'locataire';
                    $user->agency_id = $this->agencyId;
                    $user->email_verified_at = now();
                    $user->save();

                    Locataire::create([
                        'user_id'              => $user->id,
                        'cni'                  => $this->val($row, 'cni'),
                        'date_naissance'       => $this->date($row, 'date_naissance'),
                        'genre'                => $this->inList($row, 'genre', ['M', 'F']),
                        'nationalite'          => $this->val($row, 'nationalite') ?? 'Sénégalaise',
                        'ville'                => $this->val($row, 'ville') ?? 'Dakar',
                        'quartier'             => $this->val($row, 'quartier'),
                        'profession'           => $this->val($row, 'profession'),
                        'employeur'            => $this->val($row, 'employeur'),
                        'revenu_mensuel'       => $this->numeric($row, 'revenu_mensuel'),
                        'contact_urgence_nom'  => $this->val($row, 'contact_urgence_nom'),
                        'contact_urgence_tel'  => $this->val($row, 'contact_urgence_tel'),
                        'contact_urgence_lien' => $this->val($row, 'contact_urgence_lien'),
                        'type_locataire'       => $typeLocataire,
                        'est_entreprise'       => $estEntreprise,
                        'nom_entreprise'       => $estEntreprise ? $this->val($row, 'nom_entreprise') : null,
                        'ninea_locataire'      => $estEntreprise ? $this->val($row, 'ninea_locataire') : null,
                        'rccm_locataire'       => $estEntreprise ? $this->val($row, 'rccm_locataire') : null,
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

    private function date($row, string $key): ?string
    {
        $v = $this->val($row, $key);
        if (! $v) return null;
        try {
            return \Carbon\Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
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
}
