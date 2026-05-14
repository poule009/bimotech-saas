<?php

namespace App\Exports;

use App\Exports\Sheets\ImpayesSheet;
use App\Exports\Sheets\PayesSheet;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImpayesExport implements WithMultipleSheets
{
    public function __construct(
        private $impayes,
        private $payes,
        private array $stats,
        private Carbon $periode,
        private string $agencyName,
    ) {}

    public function sheets(): array
    {
        return [
            new ImpayesSheet($this->impayes, $this->stats, $this->periode, $this->agencyName),
            new PayesSheet($this->payes, $this->periode, $this->agencyName),
        ];
    }
}
