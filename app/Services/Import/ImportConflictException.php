<?php

namespace App\Services\Import;

use RuntimeException;

/**
 * Levée au COMMIT quand un bien ou un locataire, libre au moment de l'aperçu,
 * a été loué entre-temps (fenêtre TOCTOU aperçu → confirmation). Son message est
 * sûr à afficher à l'utilisateur ; le commit annule alors tout le lot.
 */
class ImportConflictException extends RuntimeException
{
}
