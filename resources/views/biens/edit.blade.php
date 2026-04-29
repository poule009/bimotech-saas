@extends('layouts.app')
@section('title', 'Modifier — ' . $bien->reference)
@section('breadcrumb', 'Biens › Modifier')

@section('content')
<style>
.form-grid { display:grid; grid-template-columns:1fr 300px; gap:24px; align-items:start; }
.card { background:#fff;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:16px; }
.card-hd { padding:14px 20px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;gap:10px; }
.card-title { font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#0d1117; }
.card-body { padding:18px 20px; }
.ref-badge { display:inline-flex;align-items:center;gap:6px;padding:4px 12px;background:#f5e9c9;border:1px solid #e9d5a0;border-radius:7px;font-family:'Syne',sans-serif;font-size:11px;font-weight:600;color:#8a6e2f; }

/* Photos existantes */
.photo-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px; }
.photo-item { position:relative;border-radius:10px;overflow:hidden;border:2px solid #e5e7eb;transition:border .15s; }
.photo-item.principale { border-color:#c9a84c; }
.photo-item img { width:100%;height:90px;object-fit:cover;display:block; }
.photo-badge { position:absolute;top:4px;left:4px;background:#c9a84c;color:#0d1117;font-size:9px;font-weight:700;padding:2px 6px;border-radius:4px; }
.photo-actions { position:absolute;bottom:0;left:0;right:0;display:flex;gap:2px;padding:4px;background:rgba(0,0,0,.5);opacity:0;transition:opacity .15s; }
.photo-item:hover .photo-actions { opacity:1; }
.photo-btn { flex:1;padding:4px;border:none;border-radius:4px;font-size:10px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif; }
.photo-btn.set-main { background:rgba(201,168,76,.9);color:#0d1117; }
.photo-btn.delete { background:rgba(220,38,38,.9);color:#fff; }

/* Drop zone ajout photos */
.drop-zone { border:2px dashed #e5e7eb;border-radius:10px;padding:20px;text-align:center;cursor:pointer;transition:all .15s;background:#fafafa; }
.drop-zone:hover { border-color:#c9a84c;background:#fffbeb; }

/* Sidebar infos */
.info-sidebar { background:#0d1117;border-radius:14px;overflow:hidden;position:sticky;top:24px; }
.info-row { display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(255,255,255,.06);font-size:12px; }
.info-row:last-child { border-bottom:none; }
.info-lbl { color:rgba(255,255,255,.4); }
.info-val { color:#e6edf3;font-weight:500; }
</style>

<div style="padding:0 0 48px">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:#6b7280;margin-bottom:16px">
        <a href="{{ route('admin.biens.index') }}" style="color:#6b7280;text-decoration:none">Biens</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <a href="{{ route('admin.biens.show', $bien) }}" style="color:#6b7280;text-decoration:none">{{ $bien->reference }}</a>
        <svg style="width:12px;height:12px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        <span style="color:#0d1117;font-weight:500">Modifier</span>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
        <div>
            <h1 style="font-family:'Syne',sans-serif;font-size:22px;font-weight:700;color:#0d1117;letter-spacing:-.4px">Modifier le bien</h1>
            <p style="font-size:13px;color:#6b7280;margin-top:3px">La référence ne peut pas être modifiée.</p>
        </div>
        <div class="ref-badge">
            <svg style="width:11px;height:11px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
            {{ $bien->reference }}
        </div>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;border:1px solid #fecaca;border-left:3px solid #dc2626;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626">
        <strong>Veuillez corriger les erreurs :</strong>
        <ul style="margin-top:4px;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <div class="form-grid">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- FORMULAIRE PRINCIPAL --}}
            <form method="POST" action="{{ route('admin.biens.update', $bien) }}" id="form-edit">
                @csrf @method('PUT')

                {{-- PROPRIÉTAIRE --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div class="card-title">Propriétaire</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Propriétaire <span class="req">*</span></label>
                            <select name="proprietaire_id" class="form-select">
                                @foreach($proprietaires as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old('proprietaire_id', $bien->proprietaire_id) == $p->id ? 'selected':'' }}>
                                        {{ $p->name }} — {{ $p->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- INFORMATIONS GÉNÉRALES --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        </div>
                        <div class="card-title">Informations générales</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Type <span class="req">*</span></label>
                                <select name="type" class="form-select">
                                    @foreach(\App\Models\Bien::TYPES as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('type', $bien->type) === $val ? 'selected':'' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Statut <span class="req">*</span></label>
                                <select name="statut" class="form-select">
                                    @foreach(\App\Models\Bien::STATUTS as $val => $label)
                                        <option value="{{ $val }}"
                                            {{ old('statut', $bien->statut) === $val ? 'selected':'' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Titre <span class="opt">(optionnel — apparaît sur la fiche)</span></label>
                            <input type="text" name="titre" class="form-input"
                                   value="{{ old('titre', $bien->titre) }}"
                                   placeholder="Ex: Villa F4 avec piscine — Almadies">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Surface (m²)</label>
                                <input type="number" name="surface_m2" class="form-input"
                                       value="{{ old('surface_m2', $bien->surface_m2) }}" min="1" step="0.5">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nombre de pièces</label>
                                <input type="number" name="nombre_pieces" class="form-input"
                                       value="{{ old('nombre_pieces', $bien->nombre_pieces) }}" min="1">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" style="display:flex;align-items:center;gap:8px;cursor:pointer">
                                <input type="checkbox" name="meuble" value="1"
                                       {{ old('meuble', $bien->meuble) ? 'checked':'' }}
                                       style="width:16px;height:16px;accent-color:#c9a84c">
                                Bien meublé
                            </label>
                        </div>
                    </div>
                </div>

                {{-- LOCALISATION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div class="card-title">Localisation</div>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label">Adresse <span class="req">*</span></label>
                            <input type="text" name="adresse" class="form-input"
                                   value="{{ old('adresse', $bien->adresse) }}">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Quartier</label>
                                <input type="text" name="quartier" class="form-input"
                                       value="{{ old('quartier', $bien->quartier) }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Commune</label>
                                <input type="text" name="commune" class="form-input"
                                       value="{{ old('commune', $bien->commune) }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ville <span class="req">*</span></label>
                            <input type="text" name="ville" class="form-input"
                                   value="{{ old('ville', $bien->ville) }}">
                        </div>
                    </div>
                </div>

                {{-- FINANCIER --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon gold">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="card-title">Informations financières</div>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Loyer mensuel (FCFA) <span class="req">*</span></label>
                                <input type="number" name="loyer_mensuel" class="form-input"
                                       value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}"
                                       min="0" step="500">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Taux commission (%)</label>
                                <input type="number" name="taux_commission" class="form-input"
                                       value="{{ old('taux_commission', $bien->taux_commission ?? 10) }}"
                                       min="0" max="30" step="0.5">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="card">
                    <div class="card-hd">
                        <div class="card-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="card-title">Description</div>
                    </div>
                    <div class="card-body">
                        <textarea name="description" class="form-textarea"
                            placeholder="Description du bien, équipements…">{{ old('description', $bien->description) }}</textarea>
                    </div>
                    <div class="submit-bar">
                        <a href="{{ route('admin.biens.show', $bien) }}" class="btn-cancel">Annuler</a>
                        <button type="submit" class="btn-submit">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px"><polyline points="20 6 9 17 4 12"/></svg>
                            Enregistrer les modifications
                        </button>
                    </div>
                </div>

            </form>{{-- fin form edit --}}

            {{-- GESTION DES PHOTOS (formulaire séparé) --}}
            <div class="card">
                <div class="card-hd">
                    <div class="card-icon" style="background:#f0fdf4;color:#16a34a">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:15px;height:15px">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <circle cx="8.5" cy="8.5" r="1.5"/>
                            <polyline points="21 15 16 10 5 21"/>
                        </svg>
                    </div>
                    <div class="card-title">Photos {{ $bien->photos->count() > 0 ? '('.$bien->photos->count().')' : '' }}</div>
                </div>
                <div class="card-body">

                    {{-- Photos existantes --}}
                    @if($bien->photos->count() > 0)
                    <div class="photo-grid" style="margin-bottom:16px">
                        @foreach($bien->photos as $photo)
                        <div class="photo-item {{ $photo->est_principale ? 'principale' : '' }}">
                            <img src="{{ asset('storage/'.$photo->chemin) }}" alt="">
                            @if($photo->est_principale)
                                <span class="photo-badge">Principale</span>
                            @endif
                            <div class="photo-actions">
                                @if(!$photo->est_principale)
                                <form method="POST"
                                      action="{{ route('admin.biens.photos.principale', [$bien, $photo]) }}"
                                      style="flex:1">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="photo-btn set-main" style="width:100%">
                                        ★ Principale
                                    </button>
                                </form>
                                @endif
                                <form method="POST"
                                      action="{{ route('admin.biens.photos.destroy', [$bien, $photo]) }}"
                                      onsubmit="return confirm('Supprimer cette photo ?')"
                                      style="flex:1">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="photo-btn delete" style="width:100%">
                                        ✕ Sup.
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Ajouter de nouvelles photos --}}
                    <form method="POST"
                          action="{{ route('admin.biens.photos.store', $bien) }}"
                          enctype="multipart/form-data"
                          id="form-photos">
                        @csrf

                        <div class="drop-zone" id="drop-zone-edit"
                             onclick="document.getElementById('photos-edit-input').click()"
                             ondragover="event.preventDefault();this.style.borderColor='#c9a84c';this.style.background='#fffbeb'"
                             ondragleave="this.style.borderColor='#e5e7eb';this.style.background='#fafafa'"
                             ondrop="handleDropEdit(event)">
                            <svg style="width:28px;height:28px;color:#d1d5db;margin:0 auto 8px;display:block"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                                <polyline points="17 8 12 3 7 8"/>
                                <line x1="12" y1="3" x2="12" y2="15"/>
                            </svg>
                            <p style="font-size:12px;color:#6b7280;margin-bottom:2px">Ajouter des photos</p>
                            <p style="font-size:10px;color:#9ca3af">JPG, PNG, WEBP — max 3 Mo</p>
                        </div>

                        <input type="file" id="photos-edit-input" name="photos[]"
                               multiple accept="image/jpeg,image/png,image/webp"
                               style="display:none" onchange="previewEdit(this.files)">

                        <div id="preview-edit"
                             style="display:none;grid-template-columns:repeat(auto-fill,minmax(90px,1fr));gap:8px;margin-top:12px">
                        </div>

                        <div id="btn-upload-edit" style="display:none;margin-top:12px">
                            <button type="submit" class="btn-submit" style="width:100%;justify-content:center">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:13px;height:13px"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Uploader les photos
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>{{-- fin colonne gauche --}}

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div>
            <div class="info-sidebar">
                <div style="padding:14px 18px;border-bottom:1px solid rgba(255,255,255,.07)">
                    <div style="font-family:'Syne',sans-serif;font-size:13px;font-weight:700;color:#fff">Bien actuel</div>
                </div>
                <div style="padding:14px 18px">
                    <div class="info-row">
                        <span class="info-lbl">Référence</span>
                        <span class="info-val" style="font-family:'Syne',sans-serif">{{ $bien->reference }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Type</span>
                        <span class="info-val">{{ $bien->type_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Statut</span>
                        <span class="info-val">{{ $bien->statut_label }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Loyer</span>
                        <span class="info-val" style="color:#c9a84c">
                            {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} F
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Commission</span>
                        <span class="info-val">{{ $bien->taux_commission ?? 10 }} %</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Surface</span>
                        <span class="info-val">{{ $bien->surface_m2 ? $bien->surface_m2.' m²' : '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Pièces</span>
                        <span class="info-val">{{ $bien->nombre_pieces ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Meublé</span>
                        <span class="info-val">{{ $bien->meuble ? 'Oui' : 'Non' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Créé le</span>
                        <span class="info-val">{{ $bien->created_at?->format('d/m/Y') }}</span>
                    </div>
                </div>

                @if($bien->contratActif)
                <div style="padding:12px 18px;border-top:1px solid rgba(255,255,255,.07)">
                    <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:rgba(255,255,255,.25);margin-bottom:8px">Contrat actif</div>
                    <div style="font-size:12px;color:#e6edf3;margin-bottom:4px">
                        {{ $bien->contratActif->locataire?->name ?? '—' }}
                    </div>
                    <div style="font-size:11px;color:rgba(255,255,255,.35)">
                        Depuis {{ $bien->contratActif->date_debut?->format('d/m/Y') }}
                    </div>
                    <a href="{{ route('admin.contrats.show', $bien->contratActif) }}"
                       style="display:inline-flex;align-items:center;gap:4px;margin-top:8px;font-size:11px;color:#c9a84c;text-decoration:none">
                        Voir le contrat →
                    </a>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- fin form-grid --}}
</div>

<script>
function previewEdit(files) {
    const grid = document.getElementById('preview-edit');
    const btn  = document.getElementById('btn-upload-edit');
    grid.innerHTML = '';
    if (!files || !files.length) {
        grid.style.display = 'none';
        btn.style.display  = 'none';
        return;
    }
    grid.style.display = 'grid';
    btn.style.display  = 'block';
    [...files].forEach((file, i) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;height:80px;border:1px solid #e5e7eb';
            div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover">`;
            grid.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
    document.getElementById('drop-zone-edit').style.borderColor = '#c9a84c';
    document.getElementById('drop-zone-edit').style.background  = '#fffbeb';
}

function handleDropEdit(e) {
    e.preventDefault();
    document.getElementById('drop-zone-edit').style.borderColor = '#e5e7eb';
    document.getElementById('drop-zone-edit').style.background  = '#fafafa';
    const dt    = e.dataTransfer;
    const input = document.getElementById('photos-edit-input');
    try { input.files = dt.files; } catch(err) {}
    previewEdit(dt.files);
}
</script>
@endsection