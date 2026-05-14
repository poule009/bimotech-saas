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

class PayesSheet implements FromArray, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(
        private $payes,
        private Carbon $periode,
        private string $agencyName,
    ) {}

    public function title(): string
    {
        return 'Payés';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 32,
            'C' => 26,
            'D' => 18,
            'E' => 22,
            'F' => 20,
        ];
    }

    public function array(): array
    {
        $modes = [
            'especes'      => 'Espèces',
            'virement'     => 'Virement',
            'cheque'       => 'Chèque',
            'wave'         => 'Wave',
            'orange_money' => 'Orange Money',
            'free_money'   => 'Free Money',
            'e_money'      => 'E-Money',
        ];

        $rows = [];

        // Bloc titre (rows 1-3)
        $rows[] = [$this->agencyName];
        $rows[] = ['Loyers encaissés — ' . $this->periode->translatedFormat('F Y')];
        $rows[] = ['Généré le ' . now()->format('d/m/Y à H:i')];
        $rows[] = []; // row 4

        // En-têtes (row 5)
        $rows[] = [
            'Référence Bien',
            'Adresse',
            'Locataire',
            'Date de paiement',
            'Montant encaissé (FCFA)',
            'Mode de paiement',
        ];

        // Données (rows 6+)
        foreach ($this->payes as $item) {
            $contrat  = $item['contrat'];
            $paiement = $item['paiement'];
            $adresse  = trim(
                ($contrat->bien?->adresse ?? '') . ', ' . ($contrat->bien?->ville ?? ''),
                ', '
            );

            $rows[] = [
                $contrat->bien?->reference ?? '—',
                $adresse ?: '—',
                $contrat->locataire?->name ?? '—',
                $paiement->date_paiement
                    ? Carbon::parse($paiement->date_paiement)->format('d/m/Y')
                    : '—',
                (int) $paiement->montant_encaisse,
                $modes[$paiement->mode_paiement] ?? $paiement->mode_paiement,
            ];
        }

        if ($this->payes->isEmpty()) {
            $rows[] = ['Aucun paiement pour cette période', '', '', '', '', ''];
        }

        return $rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet     = $event->sheet->getDelegate();
                $dataCount = max(1, $this->payes->count());
                $lastRow   = 5 + $dataCount;

                // ── Row 1 : Nom agence ──────────────────────────────────
                $sheet->mergeCells('A1:F1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0D1117']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);

                // ── Row 2 : Titre période ───────────────────────────────
                $sheet->mergeCells('A2:F2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '16A34A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(22);

                // ── Row 3 : Date génération ─────────────────────────────
                $sheet->mergeCells('A3:F3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '9CA3AF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(16);

                // ── Row 4 : séparateur ──────────────────────────────────
                $sheet->getRowDimension(4)->setRowHeight(8);

                // ── Row 5 : En-têtes tableau ────────────────────────────
                $sheet->getStyle('A5:F5')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16A34A']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_LEFT,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(5)->setRowHeight(26);

                // ── Rows 6+ : Données ───────────────────────────────────
                $row   = 6;
                $zebra = 0;
                foreach ($this->payes as $item) {
                    $bgColor = $zebra % 2 === 0 ? 'FFFFFF' : 'F9FAFB';

                    $sheet->getStyle("A{$row}:F{$row}")->applyFromArray([
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                        'font'      => ['size' => 10, 'color' => ['rgb' => '374151']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    // Montant en vert gras
                    $sheet->getStyle("E{$row}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '16A34A']],
                    ]);

                    $sheet->getRowDimension($row)->setRowHeight(22);
                    $row++;
                    $zebra++;
                }

                // Format nombre pour la colonne Montant
                $sheet->getStyle("E6:E{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');

                // ── Bordures tableau ────────────────────────────────────
                $sheet->getStyle("A5:F{$lastRow}")->applyFromArray([
                    'borders' => [
                        'inside'  => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'F3F4F6']],
                        'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']],
                    ],
                ]);

                // ── Freeze sous les headers ─────────────────────────────
                $sheet->freezePane('A6');

                // Padding colonne A
                $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setIndent(1);
                $sheet->getStyle("B5:B{$lastRow}")->getAlignment()->setIndent(1);
            },
        ];
    }
}
