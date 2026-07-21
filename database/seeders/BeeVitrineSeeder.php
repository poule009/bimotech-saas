<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\Bien;
use App\Models\BienPhoto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * BeeVitrineSeeder — 10 biens de démonstration pour l'agence BEE.
 *
 * Objectif : alimenter la vitrine publique (/agences/{slug}) avec des biens
 * réellement publiables — c'est-à-dire remplissant TOUS les critères de
 * Bien::portail() : statut disponible, visible_portail, aucun contrat actif,
 * titre + quartier renseignés, et au moins une photo.
 *
 * Idempotent : relancer le seeder met à jour les mêmes biens (clé titre+agence)
 * sans créer de doublons. Les photos sont régénérées à chaque passage.
 */
class BeeVitrineSeeder extends Seeder
{
    /** Slug de l'agence cible. */
    private const AGENCY_SLUG = 'bee-ka1sOy';

    public function run(): void
    {
        $agency = Agency::withoutGlobalScopes()->where('slug', self::AGENCY_SLUG)->first();

        if (! $agency) {
            $this->command->error('Agence « ' . self::AGENCY_SLUG . ' » introuvable — seeder annulé.');

            return;
        }

        // Propriétaire de rattachement : un propriétaire existant de l'agence,
        // sinon le premier utilisateur de l'agence (jamais de compte inventé).
        $proprietaire = User::withoutGlobalScopes()
            ->where('agency_id', $agency->id)
            ->where('role', 'proprietaire')
            ->first()
            ?? User::withoutGlobalScopes()->where('agency_id', $agency->id)->first();

        if (! $proprietaire) {
            $this->command->error('Aucun utilisateur rattaché à l\'agence — seeder annulé.');

            return;
        }

        $this->command->info("Agence : {$agency->name} (ID {$agency->id}) — propriétaire #{$proprietaire->id}");
        $this->command->newLine();

        foreach ($this->definitions() as $def) {
            $bien = Bien::withoutAgencyScope()->updateOrCreate(
                [
                    'agency_id' => $agency->id,
                    'titre'     => $def['titre'],
                ],
                [
                    'proprietaire_id' => $proprietaire->id,
                    'type'            => $def['type'],
                    'adresse'         => $def['adresse'],
                    'ville'           => 'Dakar',
                    'quartier'        => $def['quartier'],
                    'commune'         => $def['commune'],
                    'surface_m2'      => $def['surface'],
                    'nombre_pieces'   => $def['pieces'],
                    'nombre_chambres' => $def['chambres'],
                    'nombre_sdb'      => $def['sdb'],
                    'loyer_mensuel'   => $def['loyer'],
                    'caution'         => $def['loyer'] * 2,
                    'taux_commission' => 10,
                    'meuble'          => $def['meuble'],
                    'parking'         => $def['parking'],
                    'climatise'       => $def['climatise'],
                    'statut'          => 'disponible',
                    'visible_portail' => true,
                    'est_en_vedette'  => $def['vedette'],
                    'description'     => $def['description'],
                    'reference'       => Bien::generateReference($agency->id),
                ]
            );

            $this->creerPhotos($bien, $def['photos'], $def['couleur']);

            $this->command->line(sprintf(
                '  ✅ %-46s %9s FCFA/mois%s',
                Str::limit($bien->titre, 44),
                number_format((float) $bien->loyer_mensuel, 0, ',', ' '),
                $def['vedette'] ? '  ★ vedette' : ''
            ));
        }

        $publiables = Bien::portail()->where('agency_id', $agency->id)->count();

        $this->command->newLine();
        $this->command->info("✅ 10 biens créés — {$publiables} biens publiables au total sur la vitrine.");
        $this->command->line('   🌐 http://localhost:8000/agences/' . $agency->slug);
    }

    /**
     * Les 10 biens : types et quartiers volontairement variés pour alimenter
     * les sections « Que recherchez-vous ? » et « Quartiers » de la vitrine.
     *
     * @return array<int, array<string, mixed>>
     */
    private function definitions(): array
    {
        return [
            [
                'titre' => 'Appartement lumineux 3 pièces — Almadies', 'type' => 'appartement',
                'quartier' => 'Almadies', 'commune' => 'Ngor', 'adresse' => '14 Route des Almadies',
                'loyer' => 450000, 'surface' => 105, 'pieces' => 3, 'chambres' => 2, 'sdb' => 2,
                'meuble' => false, 'parking' => true, 'climatise' => true, 'vedette' => true,
                'description' => "Appartement traversant au 3e étage, très lumineux, avec un large balcon donnant sur la route des Almadies. Cuisine équipée, séjour spacieux, deux chambres dont une suite parentale. Résidence sécurisée avec gardien 24h/24 et place de parking privative.",
                'couleur' => [27, 58, 63],
                'photos' => ['1560448204-e02f11c3d0e2', '1502672260266-1c1ef2d93688', '1545324418-cc1a3fa10c00'],
            ],
            [
                'titre' => 'Villa 5 pièces avec piscine — Ngor', 'type' => 'villa',
                'quartier' => 'Ngor', 'commune' => 'Ngor', 'adresse' => '7 Rue de la Corniche',
                'loyer' => 1200000, 'surface' => 320, 'pieces' => 5, 'chambres' => 4, 'sdb' => 3,
                'meuble' => true, 'parking' => true, 'climatise' => true, 'vedette' => true,
                'description' => "Villa de standing entièrement meublée à quelques minutes de la plage de Ngor. Piscine, jardin arboré, terrasse couverte, cuisine américaine, quatre chambres climatisées. Idéale pour une famille ou une location d'expatriés.",
                'couleur' => [138, 100, 32],
                'photos' => ['1613490493576-7fde63acd811', '1600047509807-ba8f99d2cdde', '1512917774080-9991f1c4c750'],
            ],
            [
                'titre' => 'Studio meublé tout confort — Mermoz', 'type' => 'studio',
                'quartier' => 'Mermoz', 'commune' => 'Dakar', 'adresse' => '22 Rue Dial Diop',
                'loyer' => 165000, 'surface' => 34, 'pieces' => 1, 'chambres' => 1, 'sdb' => 1,
                'meuble' => true, 'parking' => false, 'climatise' => true, 'vedette' => false,
                'description' => "Studio entièrement meublé et équipé, parfait pour un jeune actif ou un étudiant. Climatisation, eau chaude, fibre optique incluse. Immeuble calme, à proximité immédiate des commerces et des transports.",
                'couleur' => [156, 67, 38],
                'photos' => ['1493809842364-78817add7ffb', '1502672260266-1c1ef2d93688', '1522708323590-d24dbb6b0267'],
            ],
            [
                'titre' => 'Appartement 2 pièces — Point-E', 'type' => 'appartement',
                'quartier' => 'Point-E', 'commune' => 'Dakar', 'adresse' => '9 Avenue Cheikh Anta Diop',
                'loyer' => 280000, 'surface' => 62, 'pieces' => 2, 'chambres' => 1, 'sdb' => 1,
                'meuble' => false, 'parking' => true, 'climatise' => false, 'vedette' => false,
                'description' => "Appartement fonctionnel au cœur du Point-E, quartier calme et central. Séjour avec balcon, chambre spacieuse, cuisine séparée. Proche des universités, des banques et des administrations.",
                'couleur' => [22, 48, 47],
                'photos' => ['1522708323590-d24dbb6b0267', '1560448204-e02f11c3d0e2', '1545324418-cc1a3fa10c00'],
            ],
            [
                'titre' => 'Villa familiale 4 pièces — Sacré-Cœur', 'type' => 'villa',
                'quartier' => 'Sacré-Cœur', 'commune' => 'Dakar', 'adresse' => '3 Villa Sacré-Cœur 3',
                'loyer' => 620000, 'surface' => 210, 'pieces' => 4, 'chambres' => 3, 'sdb' => 2,
                'meuble' => false, 'parking' => true, 'climatise' => true, 'vedette' => false,
                'description' => "Villa individuelle sur deux niveaux avec cour intérieure. Trois chambres, deux salles de bain, grand séjour, cuisine indépendante et dépendance pour le personnel. Quartier résidentiel très recherché.",
                'couleur' => [93, 64, 55],
                'photos' => ['1580587771525-78b9dba3b914', '1568605114967-8130f3a36994', '1600566753086-00f18fb6b3ea'],
            ],
            [
                'titre' => 'Bureau open space — Plateau', 'type' => 'bureau',
                'quartier' => 'Plateau', 'commune' => 'Dakar-Plateau', 'adresse' => '31 Rue Félix Faure',
                'loyer' => 750000, 'surface' => 140, 'pieces' => 4, 'chambres' => 0, 'sdb' => 2,
                'meuble' => true, 'parking' => true, 'climatise' => true, 'vedette' => false,
                'description' => "Plateau de bureaux climatisé en plein centre d'affaires. Open space de 90 m², salle de réunion, kitchenette et deux sanitaires. Immeuble avec ascenseur, groupe électrogène et parking sous-sol.",
                'couleur' => [45, 84, 89],
                'photos' => ['1497366754035-f200968a6e72', '1497366216548-37526070297c', '1600607687939-ce8a6c25118c'],
            ],
            [
                'titre' => 'Local commercial pied d\'immeuble — Liberté 6', 'type' => 'commerce',
                'quartier' => 'Liberté 6', 'commune' => 'Grand-Dakar', 'adresse' => '48 Cité Liberté 6',
                'loyer' => 400000, 'surface' => 75, 'pieces' => 2, 'chambres' => 0, 'sdb' => 1,
                'meuble' => false, 'parking' => false, 'climatise' => false, 'vedette' => false,
                'description' => "Local commercial en rez-de-chaussée avec vitrine sur rue passante. Grande surface de vente, réserve à l'arrière et sanitaire. Emplacement idéal pour un commerce de proximité ou une agence.",
                'couleur' => [176, 122, 42],
                'photos' => ['1441986300917-64674bd600d8', '1497366216548-37526070297c', '1522708323590-d24dbb6b0267'],
            ],
            [
                'titre' => 'Studio moderne — Ouakam', 'type' => 'studio',
                'quartier' => 'Ouakam', 'commune' => 'Ouakam', 'adresse' => '11 Rue des Mamelles',
                'loyer' => 140000, 'surface' => 28, 'pieces' => 1, 'chambres' => 1, 'sdb' => 1,
                'meuble' => true, 'parking' => false, 'climatise' => true, 'vedette' => false,
                'description' => "Studio récent dans une petite copropriété calme d'Ouakam. Coin nuit séparé, kitchenette équipée, salle d'eau moderne. Charges d'eau incluses dans le loyer.",
                'couleur' => [108, 92, 71],
                'photos' => ['1502672260266-1c1ef2d93688', '1493809842364-78817add7ffb', '1502005229762-cf1b2da7c5d6'],
            ],
            [
                'titre' => 'Appartement haut standing 4 pièces — Fann', 'type' => 'appartement',
                'quartier' => 'Fann', 'commune' => 'Dakar', 'adresse' => '5 Boulevard Martin Luther King',
                'loyer' => 850000, 'surface' => 155, 'pieces' => 4, 'chambres' => 3, 'sdb' => 3,
                'meuble' => true, 'parking' => true, 'climatise' => true, 'vedette' => true,
                'description' => "Appartement d'exception avec vue mer sur la corniche de Fann. Prestations haut de gamme, trois chambres en suite, double séjour, cuisine américaine équipée. Ascenseur, gardiennage et deux places de parking.",
                'couleur' => [31, 71, 76],
                'photos' => ['1600566753086-00f18fb6b3ea', '1560448204-e02f11c3d0e2', '1502005229762-cf1b2da7c5d6'],
            ],
            [
                'titre' => 'Terrain viabilisé 300 m² — Diamniadio', 'type' => 'terrain',
                'quartier' => 'Diamniadio', 'commune' => 'Diamniadio', 'adresse' => 'Lot 214, Pôle Urbain',
                'loyer' => 200000, 'surface' => 300, 'pieces' => 0, 'chambres' => 0, 'sdb' => 0,
                'meuble' => false, 'parking' => false, 'climatise' => false, 'vedette' => false,
                'description' => "Terrain viabilisé et clôturé dans le pôle urbain de Diamniadio. Accès goudronné, eau et électricité en bordure de parcelle. Idéal pour un projet de construction ou un entreposage temporaire.",
                'couleur' => [122, 111, 74],
                'photos' => ['1500382017468-9049fed747ef', '1524813686514-a57563d77965'],
            ],
        ];
    }

    /**
     * Attache les photos d'un bien.
     *
     * Les visuels sont de vraies photos d'illustration (Unsplash, licence libre)
     * téléchargées une seule fois puis mises en cache dans storage/app/seed-photos.
     * Si le réseau est indisponible, on retombe sur un aplat GD : le bien reste
     * publiable (photo obligatoire dans Bien::portail()).
     *
     * @param  array<int, string>  $refs   identifiants Unsplash, 1re = photo principale
     * @param  array<int, int>     $rgb    teinte du repli GD
     */
    private function creerPhotos(Bien $bien, array $refs, array $rgb): void
    {
        // Régénération propre à chaque passage (seeder idempotent).
        BienPhoto::withoutGlobalScopes()->where('bien_id', $bien->id)->delete();

        $dossier = 'biens/' . $bien->id;
        Storage::disk('public')->makeDirectory($dossier);

        foreach ($refs as $i => $ref) {
            $chemin    = $dossier . '/photo-' . Str::random(8) . '.jpg';
            $cheminAbs = Storage::disk('public')->path($chemin);

            $binaire = $this->photoDepuisCache($ref);

            if ($binaire !== null) {
                file_put_contents($cheminAbs, $binaire);
            } elseif (function_exists('imagecreatetruecolor')) {
                $this->dessiner($rgb, $cheminAbs);   // repli hors-ligne
            } else {
                continue;                            // ni réseau ni GD
            }

            BienPhoto::create([
                'bien_id'        => $bien->id,
                'chemin'         => $chemin,
                'nom_original'   => 'photo-' . ($i + 1) . '.jpg',
                'est_principale' => $i === 0,
                'ordre'          => $i + 1,
            ]);
        }
    }

    /**
     * Contenu binaire d'une photo, depuis le cache local ou téléchargée une fois.
     * Retourne null si le réseau est indisponible (le repli GD prend le relais).
     */
    private function photoDepuisCache(string $ref): ?string
    {
        $cache = 'seed-photos/' . $ref . '.jpg';

        if (Storage::disk('local')->exists($cache)) {
            return Storage::disk('local')->get($cache);
        }

        try {
            $reponse = Http::timeout(25)
                ->get('https://images.unsplash.com/photo-' . $ref, ['w' => 1600, 'q' => 75]);
        } catch (\Throwable) {
            return null;
        }

        if (! $reponse->successful() || $reponse->body() === '') {
            return null;
        }

        Storage::disk('local')->put($cache, $reponse->body());

        return $reponse->body();
    }

    /** Dessine une image 1200×800 : fond dégradé + silhouette de maison. */
    private function dessiner(array $rgb, string $cheminAbsolu): void
    {
        $w = 1200;
        $h = 800;
        $img = imagecreatetruecolor($w, $h);

        // Dégradé vertical du plus clair (haut) au plus sombre (bas).
        for ($y = 0; $y < $h; $y++) {
            $f = 1.15 - ($y / $h) * 0.5;
            $ligne = imagecolorallocate(
                $img,
                (int) max(0, min(255, $rgb[0] * $f)),
                (int) max(0, min(255, $rgb[1] * $f)),
                (int) max(0, min(255, $rgb[2] * $f))
            );
            imagefilledrectangle($img, 0, $y, $w, $y, $ligne);
        }

        // Silhouette de maison, très légèrement plus claire que le fond.
        $voile = imagecolorallocatealpha($img, 247, 243, 234, 100);
        $cx = $w / 2;
        $cy = $h / 2 - 30;

        imagefilledpolygon($img, [
            (int) ($cx - 130), (int) $cy,
            (int) $cx,         (int) ($cy - 105),
            (int) ($cx + 130), (int) $cy,
        ], $voile);
        imagefilledrectangle($img, (int) ($cx - 95), (int) $cy, (int) ($cx + 95), (int) ($cy + 120), $voile);

        imagejpeg($img, $cheminAbsolu, 88);
        imagedestroy($img);
    }
}
