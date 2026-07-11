<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiements à valider — BIMO-tech</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper-dim text-ink">
<div class="max-w-[900px] mx-auto px-6 py-10">

    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display font-semibold text-[24px]">Paiements déclarés à valider</h1>
        <a href="{{ route('superadmin.dashboard') }}" class="text-[13px] font-bold text-teal hover:underline">← Back-office</a>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-lg bg-green/10 border border-green/25 px-4 py-3 text-[13px] text-green">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-5 rounded-lg bg-error/10 border border-error/25 px-4 py-3 text-[13px] text-error">{{ $errors->first() }}</div>
    @endif

    @if($paiements->isEmpty())
        <div class="bg-white border border-line rounded-2xl p-10 text-center text-muted text-[14px]">
            Aucune déclaration de paiement en attente.
        </div>
    @else
        <div class="space-y-4">
            @foreach($paiements as $p)
                <div class="bg-white border border-line rounded-xl p-5">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <div>
                            <div class="font-bold text-[15px]">{{ $p->agency?->name ?? 'Agence #' . $p->agency_id }}</div>
                            <div class="text-[12.5px] text-muted mt-0.5">
                                Plan <strong>{{ ucfirst($p->plan_niveau ?? $p->plan) }}</strong> ·
                                {{ number_format((float) $p->montant, 0, ',', ' ') }} F ·
                                {{ \App\Models\SubscriptionPayment::METHODE_LABELS[$p->methode] ?? $p->methode }} ·
                                réf. {{ $p->reference }} ·
                                {{ $p->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        @if($p->justificatif)
                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($p->justificatif) }}" target="_blank"
                               class="text-[12.5px] font-bold text-teal hover:underline shrink-0">Voir le reçu ↗</a>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 mt-4 flex-wrap">
                        <form method="POST" action="{{ route('superadmin.paiements.confirmer', $p) }}">
                            @csrf
                            <button type="submit" class="bg-green text-white px-4 py-2 rounded-lg text-[13px] font-bold">Confirmer</button>
                        </form>

                        <form method="POST" action="{{ route('superadmin.paiements.rejeter', $p) }}" class="flex items-center gap-2 flex-1 min-w-[280px]">
                            @csrf
                            <input type="text" name="motif_rejet" placeholder="Motif de rejet (obligatoire)" class="f-input flex-1" required>
                            <button type="submit" class="bg-error text-white px-4 py-2 rounded-lg text-[13px] font-bold whitespace-nowrap">Rejeter</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
</body>
</html>
