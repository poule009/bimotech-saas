<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Modèle .xlsx d'import : ligne d'entêtes (clés machine) + une ligne d'exemple.
 */
class ImportTemplateExport implements FromArray, WithHeadings
{
    public function __construct(
        private array $headers,
        private array $sample,
    ) {}

    public function headings(): array
    {
        return $this->headers;
    }

    public function array(): array
    {
        return [$this->sample];
    }
}
