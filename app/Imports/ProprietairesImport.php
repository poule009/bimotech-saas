<?php

namespace App\Imports;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProprietairesImport implements ToCollection, WithHeadingRow
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
            $line = $index + 2; // ligne Excel (entête = 1)

            // Ignorer les lignes entièrement vides (ex: fin de fichier Excel)
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

            try {
                DB::transaction(function () use ($row, $nom, $email) {
                    $user              = new User();
                    $user->name        = $nom;
                    $user->email       = $email;
                    $user->telephone   = $this->val($row, 'telephone');
                    $user->password    = Hash::make(Str::random(16));
                    $user->role        = 'proprietaire';
                    $user->agency_id   = $this->agencyId;
                    $user->email_verified_at = now();
                    $user->save();

                    Proprietaire::create([
                        'user_id'               => $user->id,
                        'cni'                   => $this->val($row, 'cni'),
                        'date_naissance'        => $this->date($row, 'date_naissance'),
                        'genre'                 => $this->inList($row, 'genre', ['M', 'F']),
                        'nationalite'           => $this->val($row, 'nationalite') ?? 'Sénégalaise',
                        'ville'                 => $this->val($row, 'ville') ?? 'Dakar',
                        'quartier'              => $this->val($row, 'quartier'),
                        'mode_paiement_prefere' => $this->inList($row, 'mode_paiement', [
                            'especes', 'virement', 'wave', 'orange_money', 'free_money', 'cheque', 'mobile_money',
                        ]) ?? 'virement',
                        'banque'         => $this->val($row, 'banque'),
                        'numero_compte'  => $this->val($row, 'numero_compte'),
                        'numero_wave'    => $this->val($row, 'numero_wave'),
                        'numero_om'      => $this->val($row, 'numero_om'),
                        'ninea'          => $this->val($row, 'ninea'),
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
}
