<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ImpayesSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(
        private $impayes,
        private array $stats,
        private Carbon $periode,
        private string $agencyName,
    ) {}

    public function title(): string
    {
        return 'Impayés';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 32,
            'C' => 26,
            'D' => 16,
            'E' => 26,
            'F' => 20,
            'G' => 16,
            'H' => 14,
        ];
    }

    public function array(): array
    {
        $rows = [];

        // Bloc titre (rows 1-3)
        $rows[] = [$this->agencyName];
        $rows[] = ['Rapport des Impayés — ' . $this->periode->translatedFormat('F Y')];
        $rows[] = ['Généré le ' . now()->format('d/m/Y à H:i')];
        $rows[] = []; // row 4 — séparateur

        // Bloc KPI (row 5)
        $rows[] = [
            'Impayés',
            $this->stats['nb_impayes'],
            'Payés',
            $this->stats['nb_payes'],
            'Montant dû (FCFA)',
            number_format($this->stats['montant_du'], 0, ',', ' '),
            'Taux recouvrement',
            $this->stats['taux_recouvrement'] . '%',
        ];
        $rows[] = []; // row 6 — séparateur

        // En-têtes colonnes (row 7)
        $rows[] = [
            'Référence Bien',
            'Adresse',
            'Locataire',
            'Téléphone',
            'Propriétaire',
            'Loyer dû (FCFA)',
            'Jours de retard',
            'Urgence',
        ];

        // Données (rows 8+)
        foreach ($this->impayes as $item) {
            $contrat = $item['contrat'];
            $jr      = $item['jours_retard'];
            $urgence = $jr > 15 ? 'HAUTE' : ($jr > 7 ? 'MOYENNE' : 'FAIBLE');
            $adresse = trim(
                ($contrat->bien?->adresse ?? '') . ', ' . ($contrat->bien?->ville ?? ''),
                ', '
            );

            $rows[] = [
                $contrat->bien?->reference ?? '—',
                $adresse ?: '—',
                $contrat->locataire?->name ?? '—',
                $contrat->locataire?->telephone ?? '—',
                $contrat->bien?->proprietaire?->name ?? '—',
                (int) $item['montant_du'],
                $jr > 0 ? $jr : 0,
                $urgence,
            ];
        }

        if ($this->impayes->isEmpty()) {
            $rows[] = ['Aucun impayé pour cette période', '', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $dataCount = max(1, $this->impayes->count());
                $lastRow   = 7 + $dataCount;

                // ── Row 1 : Nom agence ──────────────────────────────────
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0D1117']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // ── Row 2 : Titre période ───────────────────────────────
                $sheet->mergeCells('A2:H2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'DC2626']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ── Row 3 : Date génération ─────────────────────────────
                $sheet->mergeCells('A3:H3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(16);

                // ── Row 4 : séparateur ──────────────────────────────────
                $sheet->getRowDimension(4)->setRowHeight(8);

                // ── Row 5 : KPIs ────────────────────────────────────────
                $sheet->getStyle('A5:H5')->applyFromArray([
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F9F7F2']],
                    'font'      => ['size' => 10],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'borders'   => [
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
                    ],
                ]);
                // Labels KPI
                foreach (['A5', 'C5', 'E5', 'G5'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']],
                    ]);
                }
                // Valeurs KPI : impayés
                $sheet->getStyle('B5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'DC2626']],
                ]);
                // Valeurs KPI : payés
                $sheet->getStyle('D5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '16A34A']],
                ]);
                // Valeurs KPI : montant
                $sheet->getStyle('F5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'D97706']],
                ]);
                // Valeurs KPI : taux
                $taux = $this->stats['taux_recouvrement'];
                $tauxColor = $taux >= 80 ? '16A34A' : ($taux >= 50 ? 'D97706' : 'DC2626');
                $sheet->getStyle('H5')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => $tauxColor]],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(26);

                // ── Row 6 : séparateur ──────────────────────────────────
                $sheet->getRowDimension(6)->setRowHeight(8);

                // ── Row 7 : En-têtes tableau ────────────────────────────
                $sheet->getStyle('A7:H7')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D1117']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(7)->setRowHeight(26);

                // ── Rows 8+ : Données ───────────────────────────────────
                // Compteur manuel : sortByDesc() peut désynchroniser les clés de collection
                $row = 8;
                foreach ($this->impayes as $item) {
                    $jr  = $item['jours_retard'];

                    $bgColor  = $jr > 15 ? 'FEF2F2' : ($jr > 7 ? 'FEFCE8' : 'FFFFFF');
                    $urgColor = $jr > 15 ? 'DC2626' : ($jr > 7 ? 'D97706' : '6B7280');

                    $sheet->getStyle("A{$row}:H{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                        'font'      => ['size' => 10, 'color' => ['rgb' => '374151']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Loyer en rouge gras
                    $sheet->getStyle("F{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                    ]);

                    // Urgence colorée
                    $sheet->getStyle("H{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $urgColor]],
                    ]);

                    $sheet->getRowDimension($row)->setRowHeight(22);
                    $row++;
                }

                // Format nombre pour la colonne Loyer
                $sheet->getStyle("F8:F{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

                // ── Bordures tableau ────────────────────────────────────
                $sheet->getStyle("A7:H{$lastRow}")->applyFromArray([
                    'borders' => [
                        'inside'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'F3F4F6']],
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
                    ],
                ]);

                // ── Freeze sous les headers ─────────────────────────────
                $sheet->freezePane('A8');

                // Padding colonnes A & B (indent léger)
                $sheet->getStyle("A7:A{$lastRow}")->getAlignment()->setIndent(1);
                $sheet->getStyle("B7:B{$lastRow}")->getAlignment()->setIndent(1);
            },
        ];
    }
}
