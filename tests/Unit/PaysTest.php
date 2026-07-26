<?php

namespace Tests\Unit;

use App\Support\Pays;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cohérence du référentiel pays/devises (internationalisation — étape 1).
 *
 * Ces tests protègent les DEUX fichiers de config l'un contre l'autre : un pays
 * ajouté à config/pays.php avec une devise absente de config/devises.php ne se
 * verrait qu'au moment d'afficher un montant — c'est-à-dire trop tard, chez le
 * client. Le cas s'est produit à l'écriture initiale (Djibouti → DJF manquant).
 */
class PaysTest extends TestCase
{
    #[Test]
    public function chaque_pays_du_catalogue_pointe_vers_une_devise_definie(): void
    {
        $devises = array_keys(config('devises'));

        foreach (config('pays.liste') as $code => $pays) {
            $this->assertArrayHasKey(
                'devise',
                $pays,
                "Le pays {$code} n'a pas de devise déclarée dans config/pays.php."
            );

            $this->assertContains(
                $pays['devise'],
                $devises,
                "Le pays {$code} pointe vers la devise {$pays['devise']}, absente de config/devises.php."
            );
        }
    }

    #[Test]
    public function chaque_devise_declare_ses_decimales_et_sa_position(): void
    {
        foreach (config('devises') as $code => $devise) {
            $this->assertIsInt($devise['decimales'], "Devise {$code} : décimales non entières.");
            $this->assertGreaterThanOrEqual(0, $devise['decimales'], "Devise {$code} : décimales négatives.");
            $this->assertContains(
                $devise['position'],
                ['avant', 'apres'],
                "Devise {$code} : position invalide (attendu 'avant' ou 'apres')."
            );
            $this->assertNotEmpty($devise['symbole'], "Devise {$code} : symbole vide.");
        }
    }

    /**
     * Un pays ne peut pas être ouvert à l'inscription sans figurer au catalogue :
     * il n'aurait ni libellé ni devise, et `Pays::devise()` retomberait
     * silencieusement sur le Sénégal.
     */
    #[Test]
    public function tout_pays_ouvert_figure_au_catalogue(): void
    {
        foreach (Pays::ouverts() as $code) {
            $this->assertArrayHasKey(
                $code,
                config('pays.liste'),
                "Le pays {$code} est ouvert à l'inscription mais absent du catalogue."
            );
        }
    }

    /**
     * GARDE-FOU MÉTIER : tant que le socle générique hors Sénégal n'est pas livré,
     * aucun autre pays ne doit être ouvert. Ce test échouera volontairement le jour
     * où on ouvrira un pays — ce sera le rappel de vérifier documents, devise et
     * moyens de paiement AVANT de le rendre sélectionnable.
     */
    #[Test]
    public function seul_le_senegal_est_ouvert_a_ce_stade(): void
    {
        $this->assertSame(['SN'], Pays::ouverts());
    }

    #[Test]
    public function le_pays_par_defaut_est_coherent_avec_sa_devise(): void
    {
        $this->assertSame('SN', Pays::DEFAUT);
        $this->assertSame('XOF', Pays::devise(Pays::DEFAUT));
        $this->assertSame('Sénégal', Pays::nom(Pays::DEFAUT));
    }

    #[Test]
    public function les_options_d_inscription_ne_contiennent_que_les_pays_ouverts(): void
    {
        $options = Pays::optionsInscription();

        $this->assertSame(Pays::ouverts(), array_keys($options));
        $this->assertArrayNotHasKey('CI', $options, "Un pays fermé ne doit jamais être proposé.");
    }

    #[Test]
    public function est_ouvert_rejette_un_pays_ferme_et_une_casse_incorrecte(): void
    {
        $this->assertTrue(Pays::estOuvert('SN'));
        $this->assertFalse(Pays::estOuvert('CI'), 'Pays au catalogue mais non ouvert.');
        $this->assertFalse(Pays::estOuvert('ZZ'), 'Pays inexistant.');
        $this->assertFalse(Pays::estOuvert('sn'), 'La comparaison est stricte : ISO alpha-2 en majuscules.');
    }
}
