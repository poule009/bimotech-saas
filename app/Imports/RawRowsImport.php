<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Lecture brute d'un fichier : renvoie les lignes indexées par entête (snake_case).
 * Aucune logique métier ici — la validation et la persistance sont pilotées par
 * ImportManager + les handlers, pour permettre l'étape d'aperçu avant écriture.
 */
class RawRowsImport implements ToCollection, WithHeadingRow
{
    public Collection $rows;

    public function __construct()
    {
        $this->rows = collect();
    }

    public function collection(Collection $rows): void
    {
        $this->rows = $rows;
    }
}
