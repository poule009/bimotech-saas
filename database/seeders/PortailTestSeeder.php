<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\BienPhoto;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Bien;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortailTestSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Agence ──────────────────────────────────────────────────────
        $agency = Agency::updateOrCreate(
            ['slug' => 'bimo-tech'],
            [
                'name'             => 'BIMO-Tech Immo',
                'email'            => 'contact@bimotech.sn',
                'telephone'        => '+221771234567',
                'whatsapp'         => '+221771234567',
                'adresse'          => 'Plateau, Dakar',
                'couleur_primaire' => '#1a3c5e',
                'taux_tva'         => 18.00,
                'actif'            => true,
            ]
        );

        Subscription::updateOrCreate(
            ['agency_id' => $agency->id],
            [
                'statut'                => 'actif',
                'plan'                  => 'annuel',
                'plan_niveau'           => 'agence',
                'montant_paye'          => 240000,
                'date_debut_essai'      => now(),
                'date_fin_essai'        => now()->addDays(30),
                'date_debut_abonnement' => now(),
                'date_fin_abonnement'   => now()->addYear(),
            ]
        );

        $this->command->info("✓ Agence : {$agency->name} (ID {$agency->id})");

        // ── 2. Admin ───────────────────────────────────────────────────────
        $admin = User::updateOrCreate(
            ['email' => 'admin@bimotech.sn'],
            [
                'name'              => 'Admin BiMO-Tech',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'agency_id'         => $agency->id,
                'telephone'         => '+221771000001',
                'email_verified_at' => now(),
            ]
        );

        // ── 3. SuperAdmin ──────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'superadmin@bimotech.sn'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('password'),
                'role'              => 'superadmin',
                'agency_id'         => null,
                'email_verified_at' => now(),
            ]
        );

        // ── 4. Propriétaire ────────────────────────────────────────────────
        $proprio = User::updateOrCreate(
            ['email' => 'proprio@bimotech.sn'],
            [
                'name'              => 'Moussa Diallo',
                'password'          => Hash::make('password'),
                'role'              => 'proprietaire',
                'agency_id'         => $agency->id,
                'telephone'         => '+221771000002',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("✓ Admin : {$admin->email}  |  Proprio : {$proprio->email}");

        // ── 5. Biens portail ───────────────────────────────────────────────
        $biensDef = [
            [
                'titre'        => 'Appartement moderne 3 pièces — Plateau',
                'type'         => 'appartement',
                'quartier'     => 'Plateau',
                'commune'      => 'Dakar-Plateau',
                'ville'        => 'Dakar',
                'adresse'      => '12 Rue de Thiong',
                'loyer'        => 350000,
                'surface'      => 95,
                'pieces'       => 3,
                'meuble'       => false,
                'description'  => 'Bel appartement au cœur du Plateau, lumineux avec vue dégagée. Cuisine équipée, parking inclus.',
                'couleur'      => [41, 128, 185],
            ],
            [
                'titre'        => 'Villa 5 pièces avec jardin — Almadies',
                'type'         => 'villa',
                'quartier'     => 'Almadies',
                'commune'      => 'Ngor',
                'ville'        => 'Dakar',
                'adresse'      => '8 Corniche Ouest',
                'loyer'        => 750000,
                'surface'      => 280,
                'pieces'       => 5,
                'meuble'       => true,
                'description'  => 'Superbe villa meublée face à la mer. Piscine, jardin, 3 chambres, salon climatisé, gardien.',
                'couleur'      => [39, 174, 96],
            ],
            [
                'titre'        => 'Studio meublé tout confort — Mermoz',
                'type'         => 'studio',
                'quartier'     => 'Mermoz',
                'commune'      => 'Dakar',
                'ville'        => 'Dakar',
                'adresse'      => '5 Rue Dial Diop',
                'loyer'        => 150000,
                'surface'      => 32,
                'pieces'       => 1,
                'meuble'       => true,
                'description'  => 'Studio entièrement meublé, idéal pour jeune professionnel. Climatisation, eau chaude, fibre optique.',
                'couleur'      => [142, 68, 173],
            ],
            [
                'titre'        => 'Appartement 2 pièces — Liberté 6',
                'type'         => 'appartement',
                'quartier'     => 'Liberté 6',
                'commune'      => 'Grand-Dakar',
                'ville'        => 'Dakar',
                'adresse'      => '24 Cité Liberté',
                'loyer'        => 220000,
                'surface'      => 68,
                'pieces'       => 2,
                'meuble'       => false,
                'description'  => 'Appartement calme en étage élevé. Salon, chambre, cuisine, salle de bain, parking.',
                'couleur'      => [230, 126, 34],
            ],
            [
                'titre'        => 'Bureau haut standing — Point-E',
                'type'         => 'bureau',
                'quartier'     => 'Point-E',
                'commune'      => 'Dakar',
                'ville'        => 'Dakar',
                'adresse'      => '17 Avenue Cheikh A. Diop',
                'loyer'        => 580000,
                'surface'      => 120,
                'pieces'       => 4,
                'meuble'       => true,
                'description'  => 'Bureau climatisé avec open space, salle de réunion, kitchenette. Immeuble sécurisé, parking.',
                'couleur'      => [22, 160, 133],
            ],
            [
                'titre'        => 'Villa 4 pièces — Sacré-Cœur',
                'type'         => 'villa',
                'quartier'     => 'Sacré-Cœur',
                'commune'      => 'Dakar',
                'ville'        => 'Dakar',
                'adresse'      => '3 Rue Tolbiac',
                'loyer'        => 480000,
                'surface'      => 200,
                'pieces'       => 4,
                'meuble'       => false,
                'description'  => 'Belle villa résidentielle, 4 chambres, 2 salles de bain, cuisine, salon-salle à manger, cour.',
                'couleur'      => [192, 57, 43],
            ],
        ];

        foreach ($biensDef as $def) {
            // Créer le bien
            $bien = Bien::withoutAgencyScope()->updateOrCreate(
                [
                    'agency_id'       => $agency->id,
                    'titre'           => $def['titre'],
                ],
                [
                    'proprietaire_id' => $proprio->id,
                    'type'            => $def['type'],
                    'titre'           => $def['titre'],
                    'adresse'         => $def['adresse'],
                    'ville'           => $def['ville'],
                    'quartier'        => $def['quartier'],
                    'commune'         => $def['commune'],
                    'surface_m2'      => $def['surface'],
                    'nombre_pieces'   => $def['pieces'],
                    'loyer_mensuel'   => $def['loyer'],
                    'taux_commission' => 10,
                    'meuble'          => $def['meuble'],
                    'statut'          => 'disponible',
                    'visible_portail' => true,
                    'description'     => $def['description'],
                    'reference'       => Bien::generateReference($agency->id),
                ]
            );

            // Générer photo placeholder colorée (GD)
            $this->creerPhotoPlaceholder($bien, $def['couleur'], $def['titre']);

            $this->command->line("  ✅ {$bien->titre}");
        }

        $this->command->newLine();
        $this->command->info('════════════════════════════════════════════════');
        $this->command->info('  ✅ Données portail créées avec succès !');
        $this->command->info('════════════════════════════════════════════════');
        $this->command->table(
            ['Compte', 'Email', 'Mot de passe'],
            [
                ['Admin agence', 'admin@bimotech.sn',      'password'],
                ['SuperAdmin',   'superadmin@bimotech.sn', 'password'],
                ['Propriétaire', 'proprio@bimotech.sn',    'password'],
            ]
        );
        $this->command->newLine();
        $this->command->line('  🌐 Portail : <fg=cyan>http://localhost:8000/biens</>');
        $this->command->newLine();
    }

    private function creerPhotoPlaceholder(Bien $bien, array $rgb, string $titre): void
    {
        // Supprimer les photos existantes
        BienPhoto::withoutGlobalScopes()
            ->where('bien_id', $bien->id)
            ->delete();

        if (! function_exists('imagecreatetruecolor')) {
            $this->command->warn("  ⚠ GD absent — photo ignorée pour {$titre}");
            return;
        }

        $dossier = 'biens/' . $bien->id;
        Storage::disk('public')->makeDirectory($dossier);

        $w = 800; $h = 500;
        $img = imagecreatetruecolor($w, $h);

        // Fond couleur
        $fond = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        imagefill($img, 0, 0, $fond);

        // Dégradé sombre en bas
        for ($y = (int)($h * 0.6); $y < $h; $y++) {
            $factor = ($y - $h * 0.6) / ($h * 0.4);
            $r = (int)($rgb[0] * (1 - $factor * 0.5));
            $g = (int)($rgb[1] * (1 - $factor * 0.5));
            $b = (int)($rgb[2] * (1 - $factor * 0.5));
            $ligne = imagecolorallocate($img, max(0,$r), max(0,$g), max(0,$b));
            imagefilledrectangle($img, 0, $y, $w, $y, $ligne);
        }

        // Icône maison centrée (simple)
        $blanc = imagecolorallocate($img, 255, 255, 255);
        $cx = $w / 2; $cy = $h / 2 - 20;
        // Toit
        imagefilledpolygon($img, [
            (int)($cx - 50), (int)($cy),
            (int)($cx),      (int)($cy - 40),
            (int)($cx + 50), (int)($cy),
        ], $blanc);
        // Corps
        imagefilledrectangle($img, (int)($cx - 35), (int)($cy), (int)($cx + 35), (int)($cy + 45), $blanc);
        // Porte (couleur fond)
        $porte = imagecolorallocate($img, $rgb[0], $rgb[1], $rgb[2]);
        imagefilledrectangle($img, (int)($cx - 12), (int)($cy + 20), (int)($cx + 12), (int)($cy + 45), $porte);

        $chemin = $dossier . '/photo-' . Str::random(8) . '.jpg';
        $cheminAbs = Storage::disk('public')->path($chemin);
        imagejpeg($img, $cheminAbs, 85);
        imagedestroy($img);

        BienPhoto::create([
            'bien_id'        => $bien->id,
            'chemin'         => $chemin,
            'nom_original'   => 'photo-placeholder.jpg',
            'est_principale' => true,
            'ordre'          => 1,
        ]);
    }
}
