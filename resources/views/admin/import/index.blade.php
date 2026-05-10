<x-app-layout>
    <x-slot name="header">Import Excel</x-slot>

<style>
.import-wrap { max-width:860px; margin:0 auto; padding-bottom:60px; }

.card {
    background:#161b22;
    border:1px solid rgba(255,255,255,.07);
    border-radius:14px; overflow:hidden; margin-bottom:20px;
}
.card-hd {
    padding:14px 20px;
    border-bottom:1px solid rgba(255,255,255,.06);
    display:flex; align-items:center; gap:10px;
}
.card-icon {
    width:32px; height:32px; border-radius:8px;
    display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.card-icon svg { width:15px; height:15px; }
.card-icon.blue   { background:rgba(59,130,246,.12); }  .card-icon.blue svg   { color:#60a5fa; }
.card-icon.green  { background:rgba(34,197,94,.12);  }  .card-icon.green svg  { color:#4ade80; }
.card-icon.purple { background:rgba(139,92,246,.12); }  .card-icon.purple svg { color:#a78bfa; }
.card-title { font-family:'Syne',sans-serif; font-size:13px; font-weight:700; color:#e6edf3; }
.card-body { padding:20px; }

.tabs { display:flex; gap:8px; margin-bottom:24px; }
.tab-btn {
    padding:8px 18px; border-radius:8px; border:1px solid rgba(255,255,255,.1);
    font-size:12px; font-weight:600; cursor:pointer;
    color:#8b949e; background:transparent; transition:all .15s;
}
.tab-btn.active, .tab-btn:hover {
    background:rgba(201,168,76,.12); border-color:rgba(201,168,76,.4); color:#c9a84c;
}
.tab-panel { display:none; }
.tab-panel.active { display:block; }

.upload-zone {
    border:2px dashed rgba(255,255,255,.12);
    border-radius:12px; padding:32px 20px;
    text-align:center; cursor:pointer;
    transition:border-color .15s, background .15s;
    position:relative;
}
.upload-zone:hover, .upload-zone.dragover {
    border-color:rgba(201,168,76,.5);
    background:rgba(201,168,76,.04);
}
.upload-zone input[type=file] {
    position:absolute; inset:0; opacity:0; cursor:pointer; width:100%; height:100%;
}
.upload-icon { color:#6e7681; margin-bottom:10px; }
.upload-icon svg { width:36px; height:36px; }
.upload-title { font-size:14px; font-weight:600; color:#e6edf3; margin-bottom:4px; }
.upload-hint  { font-size:12px; color:#6e7681; }
.file-name { font-size:12px; color:#c9a84c; margin-top:8px; font-weight:500; }

.btn-submit {
    display:inline-flex; align-items:center; gap:7px;
    padding:10px 22px; border-radius:9px;
    background:rgba(201,168,76,.15); border:1px solid rgba(201,168,76,.35);
    color:#c9a84c; font-size:13px; font-weight:600;
    cursor:pointer; transition:all .15s; margin-top:16px;
}
.btn-submit:hover { background:rgba(201,168,76,.25); }
.btn-submit svg { width:15px; height:15px; }

.btn-template {
    display:inline-flex; align-items:center; gap:6px;
    padding:7px 14px; border-radius:7px;
    background:rgba(59,130,246,.1); border:1px solid rgba(59,130,246,.25);
    color:#60a5fa; font-size:12px; font-weight:500;
    text-decoration:none; transition:all .15s; margin-bottom:18px;
}
.btn-template:hover { background:rgba(59,130,246,.18); }
.btn-template svg { width:13px; height:13px; }

.columns-list {
    display:flex; flex-wrap:wrap; gap:6px; margin-top:12px;
}
.col-tag {
    padding:3px 9px; border-radius:5px; font-size:11px; font-weight:600;
    background:rgba(255,255,255,.05); color:#8b949e; border:1px solid rgba(255,255,255,.07);
}
.col-tag.required { background:rgba(201,168,76,.1); color:#c9a84c; border-color:rgba(201,168,76,.25); }

.result-box {
    border-radius:10px; padding:14px 18px; margin-bottom:16px;
}
.result-ok   { background:rgba(34,197,94,.08);  border:1px solid rgba(34,197,94,.2);  }
.result-warn { background:rgba(251,191,36,.08); border:1px solid rgba(251,191,36,.2); }
.result-err  { background:rgba(248,113,113,.08);border:1px solid rgba(248,113,113,.2);}
.result-title { font-size:13px; font-weight:700; margin-bottom:6px; }
.result-ok   .result-title { color:#4ade80; }
.result-warn .result-title { color:#fbbf24; }
.result-err  .result-title { color:#f87171; }
.result-list { list-style:none; padding:0; margin:0; }
.result-list li { font-size:12px; color:#8b949e; padding:2px 0; }
.result-list li::before { content:'› '; color:#484f58; }

.section-label { font-size:11px; font-weight:600; color:#6e7681; text-transform:uppercase; letter-spacing:.5px; margin-bottom:8px; }
</style>

<div class="import-wrap">

    {{-- ── Résultats d'import ──────────────────────────────────────────── --}}
    @if(session('import_created') !== null)
        @php $type = session('import_type'); @endphp

        @if(session('import_created') > 0)
        <div class="result-box result-ok">
            <div class="result-title">
                {{ session('import_created') }} {{ match($type) {
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
            <div class="result-title">Aucune donnée importée — le fichier est peut-être vide.</div>
        </div>
        @endif
    @endif

    @if($errors->any())
    <div class="result-box result-err">
        <div class="result-title">Erreur de validation</div>
        <ul class="result-list">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- ── Onglets ─────────────────────────────────────────────────────── --}}
    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('proprietaires', this)">Propriétaires</button>
        <button class="tab-btn"        onclick="switchTab('locataires', this)">Locataires</button>
        <button class="tab-btn"        onclick="switchTab('biens', this)">Biens</button>
    </div>

    {{-- ── Onglet Propriétaires ──────────────────────────────────────────── --}}
    <div id="tab-proprietaires" class="tab-panel active">
        <div class="card">
            <div class="card-hd">
                <div class="card-icon blue">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="card-title">Importer des propriétaires</span>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.import.template.proprietaires') }}" class="btn-template">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger le modèle CSV
                </a>

                <div class="section-label">Colonnes du fichier</div>
                <div class="columns-list">
                    <span class="col-tag required">nom_complet *</span>
                    <span class="col-tag required">email *</span>
                    <span class="col-tag">telephone</span>
                    <span class="col-tag">genre (M/F)</span>
                    <span class="col-tag">cni</span>
                    <span class="col-tag">date_naissance</span>
                    <span class="col-tag">nationalite</span>
                    <span class="col-tag">ville</span>
                    <span class="col-tag">quartier</span>
                    <span class="col-tag">mode_paiement</span>
                    <span class="col-tag">banque</span>
                    <span class="col-tag">numero_compte</span>
                    <span class="col-tag">numero_wave</span>
                    <span class="col-tag">numero_om</span>
                    <span class="col-tag">ninea</span>
                </div>

                <form method="POST" action="{{ route('admin.import.proprietaires') }}" enctype="multipart/form-data" style="margin-top:20px">
                    @csrf
                    <div class="upload-zone" id="zone-prop" ondragover="onDragOver(event,'zone-prop')" ondragleave="onDragLeave('zone-prop')" ondrop="onDrop(event,'zone-prop','file-prop','name-prop')">
                        <input type="file" name="fichier" id="file-prop" accept=".xlsx,.xls,.csv"
                               onchange="document.getElementById('name-prop').textContent = this.files[0]?.name ?? ''">
                        <div class="upload-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                        <div class="upload-title">Glisser-déposer ou cliquer</div>
                        <div class="upload-hint">xlsx, xls ou csv — max 5 Mo</div>
                        <div class="file-name" id="name-prop"></div>
                    </div>
                    <button type="submit" class="btn-submit">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Importer les propriétaires
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Onglet Locataires ────────────────────────────────────────────── --}}
    <div id="tab-locataires" class="tab-panel">
        <div class="card">
            <div class="card-hd">
                <div class="card-icon green">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="card-title">Importer des locataires</span>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.import.template.locataires') }}" class="btn-template">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger le modèle CSV
                </a>

                <div class="section-label">Colonnes du fichier</div>
                <div class="columns-list">
                    <span class="col-tag required">nom_complet *</span>
                    <span class="col-tag required">email *</span>
                    <span class="col-tag">telephone</span>
                    <span class="col-tag">genre (M/F)</span>
                    <span class="col-tag">cni</span>
                    <span class="col-tag">date_naissance</span>
                    <span class="col-tag">nationalite</span>
                    <span class="col-tag">ville</span>
                    <span class="col-tag">quartier</span>
                    <span class="col-tag">profession</span>
                    <span class="col-tag">employeur</span>
                    <span class="col-tag">revenu_mensuel</span>
                    <span class="col-tag">contact_urgence_nom</span>
                    <span class="col-tag">contact_urgence_tel</span>
                    <span class="col-tag">contact_urgence_lien</span>
                    <span class="col-tag">type_locataire</span>
                    <span class="col-tag">nom_entreprise</span>
                    <span class="col-tag">ninea_locataire</span>
                    <span class="col-tag">rccm_locataire</span>
                </div>

                <form method="POST" action="{{ route('admin.import.locataires') }}" enctype="multipart/form-data" style="margin-top:20px">
                    @csrf
                    <div class="upload-zone" id="zone-loc" ondragover="onDragOver(event,'zone-loc')" ondragleave="onDragLeave('zone-loc')" ondrop="onDrop(event,'zone-loc','file-loc','name-loc')">
                        <input type="file" name="fichier" id="file-loc" accept=".xlsx,.xls,.csv"
                               onchange="document.getElementById('name-loc').textContent = this.files[0]?.name ?? ''">
                        <div class="upload-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                        <div class="upload-title">Glisser-déposer ou cliquer</div>
                        <div class="upload-hint">xlsx, xls ou csv — max 5 Mo</div>
                        <div class="file-name" id="name-loc"></div>
                    </div>
                    <button type="submit" class="btn-submit">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Importer les locataires
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Onglet Biens ─────────────────────────────────────────────────── --}}
    <div id="tab-biens" class="tab-panel">
        <div class="card">
            <div class="card-hd">
                <div class="card-icon purple">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="card-title">Importer des biens immobiliers</span>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.import.template.biens') }}" class="btn-template">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Télécharger le modèle CSV
                </a>

                <div class="section-label">Colonnes du fichier</div>
                <div class="columns-list">
                    <span class="col-tag required">titre *</span>
                    <span class="col-tag required">type * (appartement/villa/studio/bureau/commerce/terrain)</span>
                    <span class="col-tag required">loyer_mensuel *</span>
                    <span class="col-tag">adresse</span>
                    <span class="col-tag">ville</span>
                    <span class="col-tag">quartier</span>
                    <span class="col-tag">commune</span>
                    <span class="col-tag">surface_m2</span>
                    <span class="col-tag">nombre_pieces</span>
                    <span class="col-tag">meuble (oui/non)</span>
                    <span class="col-tag">taux_commission</span>
                    <span class="col-tag">statut (disponible/loue/en_travaux)</span>
                    <span class="col-tag">description</span>
                    <span class="col-tag">proprietaire_email</span>
                </div>

                <form method="POST" action="{{ route('admin.import.biens') }}" enctype="multipart/form-data" style="margin-top:20px">
                    @csrf
                    <div class="upload-zone" id="zone-bien" ondragover="onDragOver(event,'zone-bien')" ondragleave="onDragLeave('zone-bien')" ondrop="onDrop(event,'zone-bien','file-bien','name-bien')">
                        <input type="file" name="fichier" id="file-bien" accept=".xlsx,.xls,.csv"
                               onchange="document.getElementById('name-bien').textContent = this.files[0]?.name ?? ''">
                        <div class="upload-icon"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg></div>
                        <div class="upload-title">Glisser-déposer ou cliquer</div>
                        <div class="upload-hint">xlsx, xls ou csv — max 5 Mo</div>
                        <div class="file-name" id="name-bien"></div>
                    </div>
                    <button type="submit" class="btn-submit">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Importer les biens
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

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
