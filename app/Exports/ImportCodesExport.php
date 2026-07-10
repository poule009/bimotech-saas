<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Fichier de retour des codes générés (Code + Nom), à réutiliser aux étapes suivantes.
 */
class ImportCodesExport implements FromArray, WithHeadings
{
    /** @param array<int,array{code:string,label:string}> $codes */
    public function __construct(private array $codes) {}

    public function headings(): array
    {
        return ['code', 'nom'];
    }

    public function array(): array
    {
        return array_map(
            fn ($c) => [$c['code'] ?? '', $c['label'] ?? ''],
            $this->codes
        );
    }
}
