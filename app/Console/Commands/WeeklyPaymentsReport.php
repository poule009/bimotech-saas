<?php

namespace App\Console\Commands;

use App\Models\Paiement;
use App\Models\User;
use App\Notifications\WeeklyPaymentsSummaryNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WeeklyPaymentsReport extends Command
{
    protected $signature = 'app:weekly-payments-report';

    protected $description = 'Envoie un rapport hebdomadaire des paiements encaissés au Super Admin';

    public function handle(): int
    {
        $start = Carbon::now()->startOfWeek(Carbon::MONDAY)->subWeek();
        $end = Carbon::now()->startOfWeek(Carbon::MONDAY)->subSecond();

        $summary = Paiement::query()
            ->where('statut', 'valide')
            ->whereBetween('date_paiement', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('COUNT(*) as total_paiements')
            ->selectRaw('COALESCE(SUM(montant_encaisse), 0) as total_montant')
            ->selectRaw('COALESCE(SUM(commission_agence), 0) as total_commission')
            ->selectRaw('COALESCE(SUM(net_proprietaire), 0) as total_net')
            ->selectRaw('COUNT(DISTINCT agency_id) as total_agences')
            ->first();

        $superAdmin = User::query()
            ->where('role', 'superadmin')
            ->first();

        if (! $superAdmin) {
            $this->warn('Aucun Super Admin trouvé, rapport non envoyé.');
            return self::SUCCESS;
        }

        $payload = [
            'total_paiements' => (int) ($summary->total_paiements ?? 0),
            'total_montant' => (float) ($summary->total_montant ?? 0),
            'total_commission' => (float) ($summary->total_commission ?? 0),
            'total_net' => (float) ($summary->total_net ?? 0),
            'total_agences' => (int) ($summary->total_agences ?? 0),
        ];

        $superAdmin->notify(new WeeklyPaymentsSummaryNotification($payload, $start, $end));

        Log::info('Rapport hebdomadaire des paiements envoyé', [
            'super_admin_id' => $superAdmin->id,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'summary' => $payload,
        ]);

        $this->info('Rapport hebdomadaire envoyé au Super Admin.');

        return self::SUCCESS;
    }
}
