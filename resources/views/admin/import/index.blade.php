@extends('layouts.app')
@section('header', 'Import Excel')

@section('content')

<div class="max-w-4xl space-y-5">

{{-- Résultats --}}
@if(session('import_created') !== null)
@php $type = session('import_type'); @endphp
@if(session('import_created') > 0)
<div class="flex items-start gap-2 bg-bimo-navy/[4%] border border-bimo-navy/10 rounded-[10px] px-4 py-3">
    <svg class="w-4 h-4 text-bimo-text flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    <span class="font-body text-sm text-bimo-text">✓ {{ session('import_created') }} {{ match($type) { 'proprietaires'=>'propriétaire(s) importé(s)', 'locataires'=>'locataire(s) importé(s)', 'biens'=>'bien(s) importé(s)', default=>'enregistrement(s) importé(s)' } }} avec succès</span>
</div>
@endif
@if(session('import_rolled_back'))
<div class="flex items-start gap-2 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3">
    <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
    <div class="font-body text-sm text-bimo-red">
        <strong>Import annulé — aucune donnée enregistrée.</strong>
        <p class="font-body text-xs text-bimo-red/70 mt-1">Des erreurs ont été détectées : toutes les lignes ont été annulées. Corrigez le fichier et réimportez-le.</p>
        @if(session('import_errors'))
        <ul class="mt-2 space-y-0.5">
            @foreach(session('import_errors') as $err)<li class="font-body text-xs text-bimo-red/70">› {{ $err }}</li>@endforeach
        </ul>
        @endif
    </div>
</div>
@elseif(session('import_skipped') > 0)
<div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
    <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    <div class="font-body text-sm text-bimo-gold">
        <strong>{{ session('import_skipped') }} ligne(s) ignorée(s)</strong>
        @if(session('import_errors'))<ul class="mt-1 space-y-0.5">@foreach(session('import_errors') as $err)<li class="font-body text-xs text-bimo-gold/70">› {{ $err }}</li>@endforeach</ul>@endif
    </div>
</div>
@endif
@if(session('import_created') == 0 && !session('import_rolled_back') && session('import_skipped') == 0)
<div class="flex items-center gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
    <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
    <span class="font-body text-sm text-bimo-gold">Aucune donnée importée — le fichier est peut-être vide ou mal formaté.</span>
</div>
@endif
@endif

@if($errors->any())
<div class="flex items-start gap-2 bg-bimo-red/[5%] border border-bimo-red/20 rounded-[10px] px-4 py-3">
    <svg class="w-4 h-4 text-bimo-red flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        <span class="font-body font-semibold text-sm text-bimo-red">Erreur de validation</span>
        <ul class="mt-1 space-y-0.5">@foreach($errors->all() as $e)<li class="font-body text-xs text-bimo-red/70">› {{ $e }}</li>@endforeach</ul>
    </div>
</div>
@endif

{{-- Guide --}}
<div class="bg-white rounded-[14px] border border-bimo-navy/10 p-5">
    <div class="flex items-center gap-2 font-display font-bold text-sm text-bimo-text mb-4">
        <svg class="w-4 h-4 text-bimo-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        Comment utiliser l'import Excel
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        @foreach([['1','Télécharger le modèle','Cliquer sur "Télécharger le modèle CSV" dans l\'onglet souhaité. Ce fichier contient les bonnes colonnes et un exemple.'],['2','Remplir vos données','Ouvrir le fichier dans Excel ou LibreOffice. Supprimer la ligne d\'exemple. Saisir vos données à partir de la ligne 2.'],['3','Uploader et importer','Glisser-déposer votre fichier (ou cliquer pour le choisir) puis cliquer sur le bouton "Importer".']] as [$num,$title,$desc])
        <div class="flex items-start gap-3 bg-bimo-bg border border-bimo-navy/[8%] rounded-[10px] p-4">
            <div class="w-6 h-6 rounded-full bg-bimo-navy flex items-center justify-center font-display font-bold text-[11px] text-white flex-shrink-0">{{ $num }}</div>
            <div>
                <div class="font-body font-semibold text-sm text-bimo-text mb-1">{{ $title }}</div>
                <div class="font-body text-xs text-bimo-text/50 leading-relaxed">{{ $desc }}</div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="flex items-start gap-2 bg-bimo-gold/[6%] border border-bimo-gold/20 rounded-[10px] px-4 py-3">
        <svg class="w-4 h-4 text-bimo-gold flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        <div class="font-body text-sm text-bimo-gold/80">
            <strong class="text-bimo-gold">Respectez cet ordre d'import</strong> — les biens se lient aux propriétaires via leur email.
            <div class="flex items-center gap-2 mt-1.5 font-display font-bold text-sm text-bimo-gold">
                <span>① Propriétaires</span><span class="text-bimo-gold/40">→</span>
                <span>② Biens</span><span class="text-bimo-gold/40">→</span>
                <span>③ Locataires</span>
            </div>
        </div>
    </div>
</div>

{{-- Onglets --}}
<div class="flex gap-2">
    @foreach([['proprietaires','① Propriétaires'],['biens','② Biens'],['locataires','③ Locataires']] as [$id,$lbl])
    <button id="tab-btn-{{ $id }}"
            onclick="switchTab('{{ $id }}', this)"
            class="px-4 py-2 rounded-[8px] border font-body font-semibold text-sm cursor-pointer transition-all duration-150 {{ $id === 'proprietaires' ? 'bg-bimo-gold/10 border-bimo-gold/30 text-bimo-gold' : 'border-bimo-navy/15 text-bimo-text/50 bg-white hover:border-bimo-gold/30 hover:text-bimo-gold' }}">
        {{ $lbl }}
    </button>
    @endforeach
</div>

{{-- Macro pour un onglet d'import --}}
@php
function renderImportPanel($id, $title, $icon, $iconColor, $templateRoute, $uploadRoute, $columns) {
    // rendered via blade sections below
}
@endphp

{{-- Onglet Propriétaires --}}
<div id="tab-proprietaires" class="space-y-4">
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[6%]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[rgba(59,130,246,.12)] flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#60a5fa]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-white">Importer des propriétaires</span>
            </div>
            <a href="{{ route('admin.import.template.proprietaires') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-[rgba(59,130,246,.1)] border border-[rgba(59,130,246,.25)] rounded-[8px] font-body font-semibold text-xs text-[#60a5fa] hover:bg-[rgba(59,130,246,.2)] transition-colors duration-150">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le modèle CSV
            </a>
        </div>
        <div class="px-5 py-5">
            <div class="flex items-start gap-2 bg-[rgba(251,191,36,.06)] border border-[rgba(251,191,36,.2)] rounded-[8px] px-4 py-3 mb-4">
                <svg class="w-3.5 h-3.5 text-[#d97706] flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-body text-xs text-[#d97706]">Le fichier utilise le <strong>point-virgule « ; »</strong> comme séparateur. Ne changez pas le séparateur lors de la réexportation.</p>
            </div>
            <div class="overflow-x-auto mb-4">
                <table class="w-full text-xs">
                    <thead><tr class="border-b border-white/[7%]">
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25">Colonne</th>
                        <th class="px-2.5 py-2 text-center font-body font-medium text-[10px] uppercase tracking-widest text-white/25">Requis</th>
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25 hidden md:table-cell">Format</th>
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25 hidden lg:table-cell">Exemple</th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/[4%]">
                        @foreach([['nom_complet',true,'Prénom et nom','Mamadou Diallo'],['email',true,'Adresse email valide','mamadou@exemple.com'],['telephone',false,'Numéro local ou international','77 000 00 01'],['genre',false,'M ou F','M'],['cni',false,'Numéro CNI ou passeport','SN-12345678'],['date_naissance',false,'Format AAAA-MM-JJ','1980-05-15'],['nationalite',false,'Texte libre','Sénégalaise'],['ville',false,'Texte libre','Dakar'],['quartier',false,'Texte libre','Plateau'],['mode_paiement',false,'virement · wave · orange_money · especes · cheque','virement'],['banque',false,'Nom de la banque','CBAO'],['numero_compte',false,'IBAN ou numéro local','SN000000001'],['numero_wave',false,'Numéro Wave Money','77 000 00 01'],['numero_om',false,'Numéro Orange Money','77 000 00 01'],['ninea',false,'13 chiffres (entreprises)','1234567890123']] as [$col,$req,$fmt,$ex])
                        <tr class="hover:bg-white/[2%] transition-colors duration-100">
                            <td class="px-2.5 py-2 font-body font-medium text-xs text-white/80" style="font-family:'Courier New',monospace">{{ $col }}</td>
                            <td class="px-2.5 py-2 text-center">
                                @if($req)<span class="inline-block bg-bimo-gold/15 text-bimo-gold text-[10px] font-bold px-2 py-0.5 rounded">Oui</span>
                                @else<span class="text-white/20">—</span>@endif
                            </td>
                            <td class="px-2.5 py-2 font-body text-xs text-[#6e7681] hidden md:table-cell">{{ $fmt }}</td>
                            <td class="px-2.5 py-2 font-body text-xs text-white/30 italic hidden lg:table-cell">{{ $ex }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form method="POST" action="{{ route('admin.import.proprietaires') }}" enctype="multipart/form-data">
                @csrf
                <div id="zone-prop" ondragover="onDragOver(event,'zone-prop')" ondragleave="onDragLeave('zone-prop')" ondrop="onDrop(event,'zone-prop','file-prop','name-prop')"
                     class="border-2 border-dashed border-white/[12%] rounded-[12px] p-7 text-center cursor-pointer hover:border-bimo-gold/50 hover:bg-bimo-gold/[4%] transition-all duration-150 relative">
                    <input type="file" name="fichier" id="file-prop" accept=".xlsx,.xls,.csv" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                           onchange="document.getElementById('name-prop').textContent = this.files[0]?.name ?? ''">
                    <svg class="w-8 h-8 text-[#6e7681] mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div class="font-body font-semibold text-sm text-white/80 mb-1">Glisser-déposer ou cliquer pour choisir</div>
                    <div class="font-body text-xs text-[#6e7681]">xlsx, xls ou csv — max 5 Mo</div>
                    <div class="font-body text-sm text-bimo-gold mt-2 font-medium" id="name-prop"></div>
                </div>
                <button type="submit"
                        class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-bimo-gold/15 border border-bimo-gold/35 text-bimo-gold rounded-[9px] font-body font-semibold text-sm hover:bg-bimo-gold/25 transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer les propriétaires
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Onglet Biens --}}
<div id="tab-biens" class="space-y-4 hidden">
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[6%]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[rgba(139,92,246,.12)] flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#a78bfa]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-white">Importer des biens immobiliers</span>
            </div>
            <a href="{{ route('admin.import.template.biens') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/25 rounded-[8px] font-body font-semibold text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-colors duration-150">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le modèle CSV
            </a>
        </div>
        <div class="px-5 py-5">
            <div class="flex items-start gap-2 bg-[rgba(251,191,36,.06)] border border-[rgba(251,191,36,.2)] rounded-[8px] px-4 py-3 mb-4">
                <svg class="w-3.5 h-3.5 text-[#d97706] flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-body text-xs text-[#d97706]">Fichier en point-virgule « ; ». La colonne <strong>proprietaire_email</strong> doit correspondre à un propriétaire déjà importé.</p>
            </div>
            <div class="overflow-x-auto mb-4">
                <table class="w-full text-xs">
                    <thead><tr class="border-b border-white/[7%]">
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25">Colonne</th>
                        <th class="px-2.5 py-2 text-center font-body font-medium text-[10px] uppercase tracking-widest text-white/25">Requis</th>
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25 hidden md:table-cell">Format</th>
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25 hidden lg:table-cell">Exemple</th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/[4%]">
                        @foreach([['titre',true,'Nom descriptif du bien','Appartement F3 Plateau'],['type',true,'appartement · villa · studio · bureau · commerce · terrain','appartement'],['loyer_mensuel',true,'Montant en FCFA (nombre entier)','250000'],['adresse',false,'Rue, numéro','12 Rue Carnot'],['ville',false,'Texte libre','Dakar'],['quartier',false,'Texte libre','Plateau'],['commune',false,'Texte libre','Dakar-Plateau'],['surface_m2',false,'Nombre (mètres carrés)','75'],['nombre_pieces',false,'Nombre entier','3'],['meuble',false,'oui ou non','non'],['taux_commission',false,'Pourcentage (ex: 10 pour 10%)','10'],['statut',false,'disponible · loue · en_travaux · archive','disponible'],['description',false,'Texte libre','Beau F3 lumineux'],['proprietaire_email',false,'Email d\'un propriétaire déjà importé','mamadou@exemple.com']] as [$col,$req,$fmt,$ex])
                        <tr class="hover:bg-white/[2%]">
                            <td class="px-2.5 py-2 font-body font-medium text-xs text-white/80" style="font-family:'Courier New',monospace">{{ $col }}</td>
                            <td class="px-2.5 py-2 text-center">@if($req)<span class="inline-block bg-bimo-gold/15 text-bimo-gold text-[10px] font-bold px-2 py-0.5 rounded">Oui</span>@else<span class="text-white/20">—</span>@endif</td>
                            <td class="px-2.5 py-2 font-body text-xs text-[#6e7681] hidden md:table-cell">{{ $fmt }}</td>
                            <td class="px-2.5 py-2 font-body text-xs text-white/30 italic hidden lg:table-cell">{{ $ex }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form method="POST" action="{{ route('admin.import.biens') }}" enctype="multipart/form-data">
                @csrf
                <div id="zone-bien" ondragover="onDragOver(event,'zone-bien')" ondragleave="onDragLeave('zone-bien')" ondrop="onDrop(event,'zone-bien','file-bien','name-bien')"
                     class="border-2 border-dashed border-white/[12%] rounded-[12px] p-7 text-center cursor-pointer hover:border-bimo-gold/50 hover:bg-bimo-gold/[4%] transition-all duration-150 relative">
                    <input type="file" name="fichier" id="file-bien" accept=".xlsx,.xls,.csv" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                           onchange="document.getElementById('name-bien').textContent = this.files[0]?.name ?? ''">
                    <svg class="w-8 h-8 text-[#6e7681] mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div class="font-body font-semibold text-sm text-white/80 mb-1">Glisser-déposer ou cliquer pour choisir</div>
                    <div class="font-body text-xs text-[#6e7681]">xlsx, xls ou csv — max 5 Mo</div>
                    <div class="font-body text-sm text-bimo-gold mt-2 font-medium" id="name-bien"></div>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-bimo-gold/15 border border-bimo-gold/35 text-bimo-gold rounded-[9px] font-body font-semibold text-sm hover:bg-bimo-gold/25 transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer les biens
                </button>
            </form>
        </div>
    </div>
</div>

{{-- Onglet Locataires --}}
<div id="tab-locataires" class="space-y-4 hidden">
    <div class="bg-bimo-navy rounded-[14px] border border-white/[7%] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-white/[6%]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-[8px] bg-[rgba(34,197,94,.12)] flex items-center justify-center">
                    <svg class="w-4 h-4 text-[#4ade80]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <span class="font-display font-bold text-sm text-white">Importer des locataires</span>
            </div>
            <a href="{{ route('admin.import.template.locataires') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 bg-bimo-gold/10 border border-bimo-gold/25 rounded-[8px] font-body font-semibold text-xs text-bimo-gold hover:bg-bimo-gold/20 transition-colors duration-150">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Télécharger le modèle CSV
            </a>
        </div>
        <div class="px-5 py-5">
            <div class="flex items-start gap-2 bg-[rgba(251,191,36,.06)] border border-[rgba(251,191,36,.2)] rounded-[8px] px-4 py-3 mb-4">
                <svg class="w-3.5 h-3.5 text-[#d97706] flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="font-body text-xs text-[#d97706]">Fichier en point-virgule « ; ». Chaque locataire recevra un accès à son espace avec l'email renseigné.</p>
            </div>
            <div class="overflow-x-auto mb-4">
                <table class="w-full text-xs">
                    <thead><tr class="border-b border-white/[7%]">
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25">Colonne</th>
                        <th class="px-2.5 py-2 text-center font-body font-medium text-[10px] uppercase tracking-widest text-white/25">Requis</th>
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25 hidden md:table-cell">Format</th>
                        <th class="px-2.5 py-2 text-left font-body font-medium text-[10px] uppercase tracking-widest text-white/25 hidden lg:table-cell">Exemple</th>
                    </tr></thead>
                    <tbody class="divide-y divide-white/[4%]">
                        @foreach([['nom_complet',true,'Prénom et nom','Fatou Ndiaye'],['email',true,'Email valide (identifiant de connexion)','fatou@exemple.com'],['telephone',false,'Numéro local ou international','78 000 00 02'],['genre',false,'M ou F','F'],['cni',false,'Numéro CNI ou passeport','SN-87654321'],['date_naissance',false,'Format AAAA-MM-JJ','1990-03-20'],['type_locataire',false,'particulier ou entreprise','particulier'],['profession',false,'Texte libre','Ingénieure'],['employeur',false,'Texte libre','Sonatel'],['revenu_mensuel',false,'Nombre (FCFA)','400000'],['contact_urgence_nom',false,'Prénom et nom','Ibrahima Ndiaye'],['contact_urgence_tel',false,'Numéro de téléphone','77 111 22 33'],['contact_urgence_lien',false,'Relation','Frère'],['nom_entreprise',false,'Si type_locataire = entreprise','SARL Immo SN'],['ninea_locataire',false,'NINEA entreprise (13 chiffres)','1234567890123'],['rccm_locataire',false,'N° RCCM entreprise','SN-DKR-2020-B-12345']] as [$col,$req,$fmt,$ex])
                        <tr class="hover:bg-white/[2%]">
                            <td class="px-2.5 py-2 font-body font-medium text-xs text-white/80" style="font-family:'Courier New',monospace">{{ $col }}</td>
                            <td class="px-2.5 py-2 text-center">@if($req)<span class="inline-block bg-bimo-gold/15 text-bimo-gold text-[10px] font-bold px-2 py-0.5 rounded">Oui</span>@else<span class="text-white/20">—</span>@endif</td>
                            <td class="px-2.5 py-2 font-body text-xs text-[#6e7681] hidden md:table-cell">{{ $fmt }}</td>
                            <td class="px-2.5 py-2 font-body text-xs text-white/30 italic hidden lg:table-cell">{{ $ex }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <form method="POST" action="{{ route('admin.import.locataires') }}" enctype="multipart/form-data">
                @csrf
                <div id="zone-loc" ondragover="onDragOver(event,'zone-loc')" ondragleave="onDragLeave('zone-loc')" ondrop="onDrop(event,'zone-loc','file-loc','name-loc')"
                     class="border-2 border-dashed border-white/[12%] rounded-[12px] p-7 text-center cursor-pointer hover:border-bimo-gold/50 hover:bg-bimo-gold/[4%] transition-all duration-150 relative">
                    <input type="file" name="fichier" id="file-loc" accept=".xlsx,.xls,.csv" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full"
                           onchange="document.getElementById('name-loc').textContent = this.files[0]?.name ?? ''">
                    <svg class="w-8 h-8 text-[#6e7681] mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    <div class="font-body font-semibold text-sm text-white/80 mb-1">Glisser-déposer ou cliquer pour choisir</div>
                    <div class="font-body text-xs text-[#6e7681]">xlsx, xls ou csv — max 5 Mo</div>
                    <div class="font-body text-sm text-bimo-gold mt-2 font-medium" id="name-loc"></div>
                </div>
                <button type="submit" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-bimo-gold/15 border border-bimo-gold/35 text-bimo-gold rounded-[9px] font-body font-semibold text-sm hover:bg-bimo-gold/25 transition-all duration-150 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importer les locataires
                </button>
            </form>
        </div>
    </div>
</div>

</div>

@push('scripts')
<script>
function switchTab(name, btn) {
    ['proprietaires','biens','locataires'].forEach(function(id){
        document.getElementById('tab-' + id).classList.add('hidden');
        document.getElementById('tab-btn-' + id).className = 'px-4 py-2 rounded-[8px] border font-body font-semibold text-sm cursor-pointer transition-all duration-150 border-bimo-navy/15 text-bimo-text/50 bg-white hover:border-bimo-gold/30 hover:text-bimo-gold';
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    btn.className = 'px-4 py-2 rounded-[8px] border font-body font-semibold text-sm cursor-pointer transition-all duration-150 bg-bimo-gold/10 border-bimo-gold/30 text-bimo-gold';
}
function onDragOver(e, id) { e.preventDefault(); document.getElementById(id).classList.add('border-bimo-gold/50'); }
function onDragLeave(id) { document.getElementById(id).classList.remove('border-bimo-gold/50'); }
function onDrop(e, zoneId, fileId, nameId) {
    e.preventDefault();
    document.getElementById(zoneId).classList.remove('border-bimo-gold/50');
    var file = e.dataTransfer.files[0];
    if (file) {
        var input = document.getElementById(fileId);
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        document.getElementById(nameId).textContent = file.name;
    }
}
</script>
@endpush
@endsection
