<?php

namespace App\Services\Import\Handlers;

use App\Models\ImportBatch;
use App\Models\Proprietaire;
use App\Models\User;
use App\Services\Import\CodeSequencer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProprietaireHandler extends AbstractImportHandler
{
    public function type(): string { return 'proprietaires'; }

    public function templateHeaders(): array
    {
        return ['nom', 'telephone', 'email', 'adresse', 'ville', 'ninea'];
    }

    public function templateSample(): array
    {
        return ['Cheikh Diop', '77 123 45 67', '', 'Almadies, Dakar', 'Dakar', ''];
    }

    public function previewColumns(): array
    {
        return ['nom' => 'Nom', 'telephone' => 'Téléphone'];
    }

    public function validate(array $row, int $line, array &$ctx): array
    {
        $nom       = $this->val($row, 'nom');
        $telephone = $this->val($row, 'telephone');
        $email     = $this->val($row, 'email');
        $display   = ['nom' => $nom ?? '—', 'telephone' => $telephone ?? '—'];

        if (! $nom) {
            return $this->erreur($line, 'Nom manquant (champ obligatoire).', $display);
        }
        if (! $telephone) {
            return $this->erreur($line, 'Téléphone manquant (champ obligatoire).', $display);
        }

        if ($email && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->erreur($line, "Email « {$email} » invalide.", $display);
        }
        if ($email && $this->emailExiste($email, $ctx)) {
            return $this->erreur($line, "Email « {$email} » déjà utilisé.", $display);
        }

        // Détection de doublon par téléphone (existant en base OU déjà vu dans le fichier).
        $normPhone = $this->normPhone($telephone);
        if ($normPhone && $this->telephoneExiste($normPhone, $ctx)) {
            return $this->doublon($line, "Téléphone déjà présent — propriétaire non réimporté.", $display);
        }
        $ctx['seen_phones'][$normPhone] = true;

        return $this->ok($line, $display, [
            'nom'       => $nom,
            'telephone' => $telephone,
            'email'     => $email,
            'adresse'   => $this->val($row, 'adresse'),
            'ville'     => $this->val($row, 'ville') ?? 'Dakar',
            'ninea'     => $this->val($row, 'ninea'),
        ]);
    }

    public function create(array $data, ImportBatch $batch, CodeSequencer $seq, int &$sequence): array
    {
        $code = $seq->formater('proprietaires', $sequence);

        $user            = new User();
        $user->name      = $data['nom'];
        $user->email     = $data['email'] ?: null;
        $user->telephone = $data['telephone'];
        $user->adresse   = $data['adresse'];
        $user->password  = Hash::make(Str::random(24));
        $user->role      = 'proprietaire';
        $user->agency_id = $this->agencyId;
        $user->email_verified_at = now();
        $user->save();

        Proprietaire::create([
            'user_id'         => $user->id,
            'ville'           => $data['ville'],
            'ninea'           => $data['ninea'],
            'code_import'     => $code,
            'import_batch_id' => $batch->id,
        ]);

        $sequence++;

        return ['id' => $user->id, 'code' => $code, 'label' => $data['nom']];
    }

    private function emailExiste(string $email, array &$ctx): bool
    {
        if (! isset($ctx['emails'])) {
            $ctx['emails'] = User::withTrashed()
                ->whereNotNull('email')
                ->pluck('email')
                ->map(fn ($e) => strtolower($e))
                ->flip()
                ->all();
        }
        $key = strtolower($email);
        if (isset($ctx['emails'][$key])) return true;
        $ctx['emails'][$key] = true; // réserve l'email pour les lignes suivantes du fichier
        return false;
    }

    private function telephoneExiste(string $normPhone, array &$ctx): bool
    {
        if (isset($ctx['seen_phones'][$normPhone])) return true;

        if (! isset($ctx['existing_phones'])) {
            $phones = DB::table('users')
                ->where('agency_id', $this->agencyId)
                ->where('role', 'proprietaire')
                ->whereNotNull('telephone')
                ->pluck('telephone');
            $ctx['existing_phones'] = [];
            foreach ($phones as $p) {
                $n = $this->normPhone($p);
                if ($n) $ctx['existing_phones'][$n] = true;
            }
        }

        return isset($ctx['existing_phones'][$normPhone]);
    }
}
