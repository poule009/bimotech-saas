@extends('layouts.app')
@section('title', 'Codes de récupération')

@section('content')
<style>
.rc-wrap { max-width:520px;margin:0 auto;padding:8px 0; }
.rc-card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:20px; }
.rc-card-hd { padding:18px 22px;border-bottom:1px solid #fcd34d;background:#fffbeb;display:flex;align-items:center;gap:12px; }
.rc-card-icon { width:36px;height:36px;border-radius:10px;background:#fef3c7;color:#d97706;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.rc-card-icon svg { width:18px;height:18px; }
.rc-card-title { font-family:'Syne',sans-serif;font-size:14px;font-weight:700;color:#0d1117; }
.rc-warn { padding:14px 22px;background:#fef3c7;border-bottom:1px solid #fcd34d;font-size:13px;color:#92400e;display:flex;align-items:flex-start;gap:10px; }
.rc-warn svg { width:16px;height:16px;flex-shrink:0;margin-top:2px; }
.rc-body { padding:22px; }
.rc-grid { display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:20px; }
.rc-code { font-family:'Courier New',monospace;font-size:15px;font-weight:700;letter-spacing:2px;color:#374151;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;text-align:center; }
.rc-actions { display:flex;gap:10px;flex-wrap:wrap; }
.btn-dl { background:#f3f4f6;color:#374151;font-family:'DM Sans',sans-serif;font-size:13px;font-weight:600;padding:9px 18px;border-radius:8px;border:1px solid #e5e7eb;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .15s; }
.btn-dl:hover { background:#e5e7eb; }
.btn-green { background:#16a34a;color:#fff;font-family:'DM Sans',sans-serif;font-size:13.5px;font-weight:600;padding:10px 22px;border-radius:8px;border:none;cursor:pointer;flex:1;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;transition:opacity .15s; }
.btn-green:hover { opacity:.88; }
</style>

<div class="rc-wrap">

    <div class="rc-card">
        <div class="rc-card-hd">
            <div class="rc-card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            </div>
            <div>
                <div class="rc-card-title">
                    @if(!empty($regenerated)) Nouveaux codes de récupération @else 2FA activé — Codes de récupération @endif
                </div>
                <div style="font-size:12px;color:#92400e;margin-top:2px">
                    @if(!empty($regenerated)) Les anciens codes sont maintenant invalides @else Votre compte est maintenant protégé @endif
                </div>
            </div>
        </div>

        <div class="rc-warn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span><strong>Ces codes ne s'affichent qu'une seule fois.</strong> Notez-les maintenant et conservez-les en lieu sûr. Chaque code est à usage unique.</span>
        </div>

        <div class="rc-body">
            <div class="rc-grid" id="codes-container">
                @foreach($codes as $code)
                <div class="rc-code">{{ $code }}</div>
                @endforeach
            </div>

            <div class="rc-actions">
                <button type="button" class="btn-dl" onclick="downloadCodes()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Télécharger (.txt)
                </button>
                <a href="{{ route('superadmin.dashboard') }}" class="btn-green">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    J'ai noté mes codes
                </a>
            </div>
        </div>
    </div>

</div>

<script>
function downloadCodes() {
    const codes = @json($codes);
    const text = "Codes de récupération BimoTech Immo — 2FA\n"
               + "Générés le: {{ now()->format('d/m/Y H:i') }}\n\n"
               + "IMPORTANT: Ces codes sont à usage unique. Conservez-les en lieu sûr.\n\n"
               + codes.join("\n");

    const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'bimotech-2fa-recovery-codes.txt';
    a.click();
    URL.revokeObjectURL(a.href);
}
</script>
@endsection
