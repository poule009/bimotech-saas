@extends('layouts.app')
@section('header', 'Codes de récupération')

@section('content')

<div class="max-w-lg mx-auto">

    <div class="bg-white rounded-[14px] border border-bimo-gold/25 overflow-hidden">
        {{-- Header --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-bimo-gold/20 bg-bimo-gold/[6%]">
            <div class="w-9 h-9 rounded-[10px] bg-bimo-gold/15 flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <div class="font-display font-bold text-sm text-bimo-navy">
                    @if(!empty($regenerated)) Nouveaux codes de récupération @else 2FA activé — Codes de récupération @endif
                </div>
                <div class="font-body text-xs text-bimo-gold/70 mt-0.5">
                    @if(!empty($regenerated)) Les anciens codes sont maintenant invalides @else Votre compte est maintenant protégé @endif
                </div>
            </div>
        </div>

        {{-- Avertissement --}}
        <div class="flex items-start gap-3 px-5 py-4 bg-bimo-gold/[6%] border-b border-bimo-gold/20">
            <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <p class="font-body text-sm text-bimo-gold/80 leading-relaxed"><strong class="text-bimo-gold">Ces codes ne s'affichent qu'une seule fois.</strong> Notez-les maintenant et conservez-les en lieu sûr. Chaque code est à usage unique.</p>
        </div>

        {{-- Codes --}}
        <div class="px-5 py-6">
            <div class="grid grid-cols-2 gap-2 mb-6" id="codes-container">
                @foreach($codes as $code)
                <div class="bg-bimo-bg border border-bimo-navy/[8%] rounded-[8px] px-4 py-3 text-center font-bold text-[15px] text-bimo-navy tracking-[2px]" style="font-family:'Courier New',monospace">{{ $code }}</div>
                @endforeach
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="button" onclick="downloadCodes()"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-bimo-bg border border-bimo-navy/15 rounded-[9px] font-body font-medium text-sm text-bimo-navy/60 hover:border-bimo-navy/30 hover:text-bimo-navy transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Télécharger (.txt)
                </button>
                <a href="{{ route('superadmin.dashboard') }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-bimo-navy text-white font-display font-bold text-sm rounded-[10px] hover:bg-bimo-navy-dk transition-colors duration-150">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    J'ai noté mes codes
                </a>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function downloadCodes() {
    var codes = @json($codes);
    var text = "Codes de récupération BimoTech Immo — 2FA\n"
             + "Générés le: {{ now()->format('d/m/Y H:i') }}\n\n"
             + "IMPORTANT: Ces codes sont à usage unique. Conservez-les en lieu sûr.\n\n"
             + codes.join("\n");
    var blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    var a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'bimotech-2fa-recovery-codes.txt';
    a.click();
    URL.revokeObjectURL(a.href);
}
</script>
@endpush
@endsection
