<x-app-layout>
    <x-slot name="header">Import Excel</x-slot>

<style>
.import-wrap { max-width:900px; margin:0 auto; padding-bottom:60px; }

/* ── Guide d'onboarding ── */
.ob-banner {
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    padding:22px 24px;
    margin-bottom:24px;
}
.ob-title {
    font-family:'Syne',sans-serif;
    font-size:14px;font-weight:700;color:#0d1117;
    margin-bottom:16px;
    display:flex;align-items:center;gap:8px;
}
.ob-steps {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:12px;
}
.ob-step {
    display:flex;align-items:flex-start;gap:10px;
    padding:14px;
    border-radius:10px;
    background:#f9f7f2;
    border:1px solid #e8e3d8;
}
.ob-num {
    width:24px;height:24px;border-radius:50%;
    background:#0d1117;color:#fff;
    font-family:'Syne',sans-serif;font-size:11px;font-weight:700;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.ob-step-title { font-size:12px;font-weight:600;color:#0d1117;margin-bottom:3px; }
.ob-step-sub   { font-size:11px;color:#6b7280;line-height:1.5; }

.ob-order {
    margin-top:14px;padding:12px 16px;
    background:#fef9c3;border:1px solid #fde68a;border-radius:10px;
    display:flex;align-items:center;gap:10px;
    font-size:12px;color:#92400e;
}
.ob-order-chain {
    display:flex;align-items:center;gap:6px;
    font-weight:700;white-space:nowrap;
}
.ob-arrow { color:#d97706;font-size:14px; }

/* ── Cards sombres ── */
.card {
    background:#161b22;
    border:1px solid rgba(255,255,255,.07);
    border-radius:14px;overflow:hidden;margin-bottom:20px;
}
.card-hd {
    padding:14px 20px;
    border-bottom:1px solid rgba(255,255,255,.06);
    display:flex;align-items:center;justify-content:space-between;gap:10px;
}
.card-hd-left  { display:flex;align-items:center;gap:10px; }
.card-icon {
    width:32px;height:32px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.card-icon svg { width:15px;height:15px; }
.card-icon.blue   { background:rgba(59,130,246,.12); }  .card-icon.blue svg   { color:#60a5fa; }
.card-icon.green  { background:rgba(34,197,94,.12);  }  .card-icon.green svg  { color:#4ade80; }
.card-icon.purple { background:rgba(139,92,246,.12); }  .card-icon.purple svg { color:#a78bfa; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#e6edf3; }
.card-body { padding:20px; }

/* ── Onglets ── */
.tabs { display:flex;gap:8px;margin-bottom:24px; }
.tab-btn {
    padding:8px 18px;border-radius:8px;border:1px solid rgba(255,255,255,.1);
    font-size:12px;font-weight:600;cursor:pointer;
    color:#8b949e;background:transparent;transition:all .15s;
    font-family:'DM Sans',sans-serif;
}
.tab-btn.active, .tab-btn:hover {
    background:rgba(201,168,76,.12);border-color:rgba(201,168,76,.4);color:#c9a84c;
}
.tab-panel { display:none; }
.tab-panel.active { display:block; }

/* ── Bouton template ── */
.btn-template {
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 16px;border-radius:8px;
    background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.25);
    color:#60a5fa;font-size:12px;font-weight:600;
    text-decoration:none;transition:all .15s;
    font-family:'DM Sans',sans-serif;
}
.btn-template:hover { background:rgba(59,130,246,.2); }
.btn-template svg { width:13px;height:13px; }

/* ── Avertissement séparateur ── */
.sep-warn {
    display:flex;align-items:flex-start;gap:8px;
    margin-top:10px;padding:10px 14px;
    background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);
    border-radius:8px;font-size:11px;color:#d97706;line-height:1.5;
}
.sep-warn svg { width:13px;height:13px;flex-shrink:0;margin-top:1px; }

/* ── Tableau colonnes ── */
.col-table {
    width:100%;border-collapse:collapse;margin-top:14px;margin-bottom:20px;
    font-size:12px;
}
.col-table th {
    text-align:left;padding:7px 10px;
    font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;
    color:rgba(255,255,255,.25);border-bottom:1px solid rgba(255,255,255,.07);
}
.col-table td {
    padding:7px 10px;
    border-bottom:1px solid rgba(255,255,255,.04);
    color:#8b949e;vertical-align:top;
}
.col-table tbody tr:last-child td { border-bottom:none; }
.col-table tbody tr:hover td { background:rgba(255,255,255,.02); }
.col-name  { font-family:'DM Mono',monospace,sans-serif;color:#e6edf3;font-weight:500;white-space:nowrap; }
.col-req   { text-align:center; }
.col-req .yes { display:inline-block;background:rgba(201,168,76,.15);color:#c9a84c;font-size:10px;font-weight:700;padding:1px 7px;border-radius:4px; }
.col-req .no  { color:rgba(255,255,255,.18);font-size:12px; }
.col-fmt   { color:#6e7681; }
.col-ex    { color:rgba(255,255,255,.35);font-style:italic; }

/* ── Zone de dépôt ── */
.upload-zone {
    border:2px dashed rgba(255,255,255,.12);
    border-radius:12px;padding:28px 20px;
    text-align:center;cursor:pointer;
    transition:border-color .15s,background .15s;
    position:relative;
}
.upload-zone:hover, .upload-zone.dragover {
    border-color:rgba(201,168,76,.5);background:rgba(201,168,76,.04);
}
.upload-zone input[type=file] {
    position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;
}
.upload-icon { color:#6e7681;margin-bottom:8px; }
.upload-icon svg { width:32px;height:32px; }
.upload-title { font-size:13px;font-weight:600;color:#e6edf3;margin-bottom:3px; }
.upload-hint  { font-size:11px;color:#6e7681; }
.file-name    { font-size:12px;color:#c9a84c;margin-top:8px;font-weight:500; }

/* ── Bouton submit ── */
.btn-import {
    display:inline-flex;align-items:center;gap:7px;
    padding:10px 22px;border-radius:9px;
    background:rgba(201,168,76,.15);border:1px solid rgba(201,168,76,.35);
    color:#c9a84c;font-size:13px;font-weight:600;
    cursor:pointer;transition:all .15s;margin-top:16px;
    font-family:'DM Sans',sans-serif;
}
.btn-import:hover { background:rgba(201,168,76,.25); }
.btn-import svg { width:15px;height:15px; }

/* ── Résultats ── */
.result-box { border-radius:10px;padding:14px 18px;margin-bottom:14px; }
.result-ok   { background:rgba(34,197,94,.08); border:1px solid rgba(34,197,94,.2);  }
.result-warn { background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2); }
.result-err  { background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);}
.result-title { font-size:13px;font-weight:700;margin-bottom:6px; }
.result-ok   .result-title { color:#4ade80; }
.result-warn .result-title { color:#fbbf24; }
.result-err  .result-title { color:#f87171; }
.result-list { list-style:none;padding:0;margin:0; }
.result-list li { font-size:12px;color:#8b949e;padding:2px 0; }
.result-list li::before { content:'› ';color:#484f58; }

/* ── Responsive ── */
@media (max-width:768px) {
    .ob-steps { grid-template-columns:1fr; }
    .ob-order { flex-direction:column;gap:6px; }
    .col-fmt, .col-ex { display:none; }
}
</style>

<div class="import-wrap">

{{-- ── RÉSULTATS ─────────────────────────────────────────────────────── --}}
@if(session('import_created') !== null)
    @php $type = session('import_type'); @endphp
    @if(session('import_created') > 0)
    <div class="result-box result-ok">
        <div class="result-title">
            ✓ {{ session('import_created') }} {{ match($type) {
                'proprietaires' => 'propriétaire(s) importé(s)',
                'locataires'    => 'locataire(s) importé(s)',
                'biens'         => 'bien(s) importé(s)',
                default         => 'enregistrement(s) importé(s)'
            } }} avec succès
        </div>
    </div>
    @endif
    @if(session('import_skipped') > 0)
    <div class="result-box result-warn">
        <div class="result-title">{{ session('import_skipped') }} ligne(s) ignorée(s)</div>
        @if(session('import_errors'))
        <ul class="result-list">
            @foreach(session('import_errors') as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
        @endif
    </div>
    @endif
    @if(session('import_created') == 0 && session('import_skipped') == 0)
    <div class="result-box result-warn">
        <div class="result-title">Aucune donnée importée — le fichier est peut-être vide ou mal formaté.</div>
    </div>
    @endif
@endif

@if($errors->any())
<div class="result-box result-err">
    <div class="result-title">Erreur de validation</div>
    <ul class="result-list">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- ── GUIDE PAR OÙ COMMENCER ──────────────────────────────────────────── --}}
<div class="ob-banner">
    <div class="ob-title">
        <svg style="width:16px;height:16px;color:#c9a84c" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Comment utiliser l'import Excel
    </div>

    <div class="ob-steps">
        <div class="ob-step">
            <div class="ob-num">1</div>
            <div>
                <div class="ob-step-title">Télécharger le modèle</div>
                <div class="ob-step-sub">Cliquer sur "Télécharger le modèle CSV" dans l'onglet souhaité. Ce fichier contient les bonnes colonnes et un exemple.</div>
            </div>
        </div>
        <div class="ob-step">
            <div class="ob-num">2</div>
            <div>
                <div class="ob-step-title">Remplir vos données</div>
                <div class="ob-step-sub">Ouvrir le fichier dans Excel ou LibreOffice. Supprimer la ligne d'exemple. Saisir vos données à partir de la ligne 2.</div>
            </div>
        </div>
        <div class="ob-step">
            <div class="ob-num">3</div>
            <div>
                <div class="ob-step-title">Uploader et importer</div>
                <div class="ob-step-sub">Glisser-déposer votre fichier (ou cliquer pour le choisir) puis cliquer sur le bouton "Importer".</div>
            </div>
        </div>
    </div>

    <div class="ob-order">
        <svg style="width:15px;height:15px;flex-shrink:0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <div>
            <strong>Respectez cet ordre d'import</strong> — les biens se lient aux propriétaires via leur email.
            Si vous importez des biens avant les propriétaires, le lien sera perdu.
            <div class="ob-order-chain" style="margin-top:6px">
                <span>① Propriétaires</span>
                <span class="ob-arrow">→</span>
                <span>② Biens</span>
                <span class="ob-arrow">→</span>
                <span>③ Locataires</span>
            </div>
        </div>
    </div>
</div>

{{-- ── ONGLETS ──────────────────────────────────────────────────────────── --}}
<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('proprietaires', this)">① Propriétaires</button>
    <button class="tab-btn"        onclick="switchTab('biens', this)">② Biens</button>
    <button class="tab-btn"        onclick="switchTab('locataires', this)">③ Locataires</button>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     ONGLET PROPRIÉTAIRES
     ════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-proprietaires" class="tab-panel active">
    <div class="card">
        <div class="card-hd">
            <div class="card-hd-left">
                <div class="card-icon blue">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="card-title">Importer des propriétaires</span>
            </div>
            <a href="{{ route('admin.import.template.proprietaires') }}" class="btn-template">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le modèle CSV
            </a>
        </div>
        <div class="card-body">

            <div class="sep-warn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Le fichier utilise le <strong>point-virgule « ; »</strong> comme séparateur.
                Si vous l'ouvrez dans Excel ou LibreOffice, ne changez pas le séparateur lors de la réexportation.
            </div>

            <table class="col-table">
                <thead>
                    <tr>
                        <th>Colonne</th>
                        <th class="col-req">Requis</th>
                        <th class="col-fmt">Format / Valeurs acceptées</th>
                        <th class="col-ex">Exemple</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="col-name">nom_complet</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt">Prénom et nom</td><td class="col-ex">Mamadou Diallo</td></tr>
                    <tr><td class="col-name">email</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt">Adresse email valide (sera l'identifiant de connexion)</td><td class="col-ex">mamadou@exemple.com</td></tr>
                    <tr><td class="col-name">telephone</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro local ou international</td><td class="col-ex">77 000 00 01</td></tr>
                    <tr><td class="col-name">genre</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt"><code>M</code> ou <code>F</code></td><td class="col-ex">M</td></tr>
                    <tr><td class="col-name">cni</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro CNI ou passeport</td><td class="col-ex">SN-12345678</td></tr>
                    <tr><td class="col-name">date_naissance</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Format <code>AAAA-MM-JJ</code></td><td class="col-ex">1980-05-15</td></tr>
                    <tr><td class="col-name">nationalite</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Sénégalaise</td></tr>
                    <tr><td class="col-name">ville</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Dakar</td></tr>
                    <tr><td class="col-name">quartier</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Plateau</td></tr>
                    <tr><td class="col-name">mode_paiement</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt"><code>virement</code> · <code>wave</code> · <code>orange_money</code> · <code>especes</code> · <code>cheque</code></td><td class="col-ex">virement</td></tr>
                    <tr><td class="col-name">banque</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Nom de la banque</td><td class="col-ex">CBAO</td></tr>
                    <tr><td class="col-name">numero_compte</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">IBAN ou numéro local</td><td class="col-ex">SN000000001</td></tr>
                    <tr><td class="col-name">numero_wave</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro Wave Money</td><td class="col-ex">77 000 00 01</td></tr>
                    <tr><td class="col-name">numero_om</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro Orange Money</td><td class="col-ex">77 000 00 01</td></tr>
                    <tr><td class="col-name">ninea</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">13 chiffres (entreprises)</td><td class="col-ex">1234567890123</td></tr>
                </tbody>
            </table>

            <form method="POST" action="{{ route('admin.import.proprietaires') }}" enctype="multipart/form-data">
                @csrf
                <div class="upload-zone" id="zone-prop"
                     ondragover="onDragOver(event,'zone-prop')"
                     ondragleave="onDragLeave('zone-prop')"
                     ondrop="onDrop(event,'zone-prop','file-prop','name-prop')">
                    <input type="file" name="fichier" id="file-prop" accept=".xlsx,.xls,.csv"
                           onchange="document.getElementById('name-prop').textContent = this.files[0]?.name ?? ''">
                    <div class="upload-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                    <div class="upload-title">Glisser-déposer ou cliquer pour choisir</div>
                    <div class="upload-hint">xlsx, xls ou csv — max 5 Mo</div>
                    <div class="file-name" id="name-prop"></div>
                </div>
                <button type="submit" class="btn-import">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer les propriétaires
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     ONGLET BIENS (② en priorité)
     ════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-biens" class="tab-panel">
    <div class="card">
        <div class="card-hd">
            <div class="card-hd-left">
                <div class="card-icon purple">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="card-title">Importer des biens immobiliers</span>
            </div>
            <a href="{{ route('admin.import.template.biens') }}" class="btn-template">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le modèle CSV
            </a>
        </div>
        <div class="card-body">

            <div class="sep-warn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    Le fichier utilise le <strong>point-virgule « ; »</strong> comme séparateur.<br>
                    La colonne <strong>proprietaire_email</strong> doit correspondre à un propriétaire déjà importé dans BimoTech.
                    Assurez-vous d'avoir importé les propriétaires en premier.
                </div>
            </div>

            <table class="col-table">
                <thead>
                    <tr>
                        <th>Colonne</th>
                        <th class="col-req">Requis</th>
                        <th class="col-fmt">Format / Valeurs acceptées</th>
                        <th class="col-ex">Exemple</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="col-name">titre</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt">Nom descriptif du bien</td><td class="col-ex">Appartement F3 Plateau</td></tr>
                    <tr><td class="col-name">type</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt"><code>appartement</code> · <code>villa</code> · <code>studio</code> · <code>bureau</code> · <code>commerce</code> · <code>terrain</code></td><td class="col-ex">appartement</td></tr>
                    <tr><td class="col-name">loyer_mensuel</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt">Montant en FCFA (nombre entier)</td><td class="col-ex">250000</td></tr>
                    <tr><td class="col-name">adresse</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Rue, numéro</td><td class="col-ex">12 Rue Carnot</td></tr>
                    <tr><td class="col-name">ville</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Dakar</td></tr>
                    <tr><td class="col-name">quartier</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Plateau</td></tr>
                    <tr><td class="col-name">commune</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Dakar-Plateau</td></tr>
                    <tr><td class="col-name">surface_m2</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Nombre (mètres carrés)</td><td class="col-ex">75</td></tr>
                    <tr><td class="col-name">nombre_pieces</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Nombre entier</td><td class="col-ex">3</td></tr>
                    <tr><td class="col-name">meuble</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt"><code>oui</code> ou <code>non</code></td><td class="col-ex">non</td></tr>
                    <tr><td class="col-name">taux_commission</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Pourcentage (nombre, ex: 10 pour 10%)</td><td class="col-ex">10</td></tr>
                    <tr><td class="col-name">statut</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt"><code>disponible</code> · <code>loue</code> · <code>en_travaux</code> · <code>archive</code></td><td class="col-ex">disponible</td></tr>
                    <tr><td class="col-name">description</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Beau F3 lumineux</td></tr>
                    <tr><td class="col-name">proprietaire_email</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt" style="color:#fbbf24">Email d'un propriétaire déjà importé — lien créé automatiquement</td><td class="col-ex">mamadou@exemple.com</td></tr>
                </tbody>
            </table>

            <form method="POST" action="{{ route('admin.import.biens') }}" enctype="multipart/form-data">
                @csrf
                <div class="upload-zone" id="zone-bien"
                     ondragover="onDragOver(event,'zone-bien')"
                     ondragleave="onDragLeave('zone-bien')"
                     ondrop="onDrop(event,'zone-bien','file-bien','name-bien')">
                    <input type="file" name="fichier" id="file-bien" accept=".xlsx,.xls,.csv"
                           onchange="document.getElementById('name-bien').textContent = this.files[0]?.name ?? ''">
                    <div class="upload-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                    <div class="upload-title">Glisser-déposer ou cliquer pour choisir</div>
                    <div class="upload-hint">xlsx, xls ou csv — max 5 Mo</div>
                    <div class="file-name" id="name-bien"></div>
                </div>
                <button type="submit" class="btn-import">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer les biens
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════════════════
     ONGLET LOCATAIRES
     ════════════════════════════════════════════════════════════════════════ --}}
<div id="tab-locataires" class="tab-panel">
    <div class="card">
        <div class="card-hd">
            <div class="card-hd-left">
                <div class="card-icon green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="card-title">Importer des locataires</span>
            </div>
            <a href="{{ route('admin.import.template.locataires') }}" class="btn-template">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le modèle CSV
            </a>
        </div>
        <div class="card-body">

            <div class="sep-warn">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Le fichier utilise le <strong>point-virgule « ; »</strong> comme séparateur.
                Chaque locataire recevra un accès à son espace locataire avec l'email renseigné.
            </div>

            <table class="col-table">
                <thead>
                    <tr>
                        <th>Colonne</th>
                        <th class="col-req">Requis</th>
                        <th class="col-fmt">Format / Valeurs acceptées</th>
                        <th class="col-ex">Exemple</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="col-name">nom_complet</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt">Prénom et nom</td><td class="col-ex">Fatou Ndiaye</td></tr>
                    <tr><td class="col-name">email</td><td class="col-req"><span class="yes">Oui</span></td><td class="col-fmt">Email valide (identifiant de connexion)</td><td class="col-ex">fatou@exemple.com</td></tr>
                    <tr><td class="col-name">telephone</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro local ou international</td><td class="col-ex">78 000 00 02</td></tr>
                    <tr><td class="col-name">genre</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt"><code>M</code> ou <code>F</code></td><td class="col-ex">F</td></tr>
                    <tr><td class="col-name">cni</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro CNI ou passeport</td><td class="col-ex">SN-87654321</td></tr>
                    <tr><td class="col-name">date_naissance</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Format <code>AAAA-MM-JJ</code></td><td class="col-ex">1990-03-20</td></tr>
                    <tr><td class="col-name">type_locataire</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt"><code>particulier</code> ou <code>entreprise</code></td><td class="col-ex">particulier</td></tr>
                    <tr><td class="col-name">profession</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Ingénieure</td></tr>
                    <tr><td class="col-name">employeur</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Texte libre</td><td class="col-ex">Sonatel</td></tr>
                    <tr><td class="col-name">revenu_mensuel</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Nombre (FCFA)</td><td class="col-ex">400000</td></tr>
                    <tr><td class="col-name">contact_urgence_nom</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Prénom et nom</td><td class="col-ex">Ibrahima Ndiaye</td></tr>
                    <tr><td class="col-name">contact_urgence_tel</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Numéro de téléphone</td><td class="col-ex">77 111 22 33</td></tr>
                    <tr><td class="col-name">contact_urgence_lien</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Relation (Frère, Épouse, Parent…)</td><td class="col-ex">Frère</td></tr>
                    <tr><td class="col-name">nom_entreprise</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">Si type_locataire = entreprise</td><td class="col-ex">SARL Immo SN</td></tr>
                    <tr><td class="col-name">ninea_locataire</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">NINEA entreprise (13 chiffres)</td><td class="col-ex">1234567890123</td></tr>
                    <tr><td class="col-name">rccm_locataire</td><td class="col-req"><span class="no">—</span></td><td class="col-fmt">N° RCCM entreprise</td><td class="col-ex">SN-DKR-2020-B-12345</td></tr>
                </tbody>
            </table>

            <form method="POST" action="{{ route('admin.import.locataires') }}" enctype="multipart/form-data">
                @csrf
                <div class="upload-zone" id="zone-loc"
                     ondragover="onDragOver(event,'zone-loc')"
                     ondragleave="onDragLeave('zone-loc')"
                     ondrop="onDrop(event,'zone-loc','file-loc','name-loc')">
                    <input type="file" name="fichier" id="file-loc" accept=".xlsx,.xls,.csv"
                           onchange="document.getElementById('name-loc').textContent = this.files[0]?.name ?? ''">
                    <div class="upload-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                    <div class="upload-title">Glisser-déposer ou cliquer pour choisir</div>
                    <div class="upload-hint">xlsx, xls ou csv — max 5 Mo</div>
                    <div class="file-name" id="name-loc"></div>
                </div>
                <button type="submit" class="btn-import">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer les locataires
                </button>
            </form>
        </div>
    </div>
</div>

</div>{{-- /import-wrap --}}

<script>
function switchTab(name, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + name).classList.add('active');
    btn.classList.add('active');
}

function onDragOver(e, zoneId) {
    e.preventDefault();
    document.getElementById(zoneId).classList.add('dragover');
}
function onDragLeave(zoneId) {
    document.getElementById(zoneId).classList.remove('dragover');
}
function onDrop(e, zoneId, fileId, nameId) {
    e.preventDefault();
    document.getElementById(zoneId).classList.remove('dragover');
    const file = e.dataTransfer.files[0];
    if (file) {
        const input = document.getElementById(fileId);
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        document.getElementById(nameId).textContent = file.name;
    }
}
</script>

</x-app-layout>
