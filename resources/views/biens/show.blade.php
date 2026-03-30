<x-app-layout>
    <x-slot name="header">{{ $bien->reference }}</x-slot>

    {{-- Header navigation --}}
    <div class="flex-between section-gap" style="flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <a href="{{ route('biens.index') }}"
               style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:var(--radius-sm);border:1px solid var(--border);color:var(--text-2);transition:background .15s;"
               onmouseenter="this.style.background='var(--bg)'"
               onmouseleave="this.style.background='transparent'">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <h1 style="font-size:20px;font-weight:700;color:var(--text);letter-spacing:-.3px;">{{ $bien->reference }}</h1>
                    @php
                        $sc = match($bien->statut) {
                            'loue'       => 'badge badge-green',
                            'disponible' => 'badge badge-blue',
                            'en_travaux' => 'badge badge-amber',
                            default      => 'badge badge-gray',
                        };
                        $sl = match($bien->statut) {
                            'loue'       => 'Loué',
                            'disponible' => 'Disponible',
                            'en_travaux' => 'En travaux',
                            default      => ucfirst($bien->statut),
                        };
                    @endphp
                    <span class="{{ $sc }}">{{ $sl }}</span>
                </div>
                <p style="font-size:13px;color:var(--text-3);margin-top:2px;">
                    {{ $bien->type }} — {{ $bien->adresse }}, {{ $bien->ville }}
                </p>
            </div>
        </div>
        @can('update', $bien)
            <a href="{{ route('biens.edit', $bien) }}" class="btn btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
        @endcan
    </div>

    {{-- Alertes --}}
    @if(session('success'))
        <div class="alert alert-success section-gap">✅ {{ session('success') }}</div>
    @endif

    {{-- Infos principales --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">Informations du bien</span>
            <span style="font-size:13px;color:#16a34a;font-weight:700;" class="text-money">
                {{ number_format($totalEncaisse, 0, ',', ' ') }} FCFA encaissés
            </span>
        </div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;" id="bien-infos">

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Type & Surface</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $bien->type }}</div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;">
                        @if($bien->surface_m2) {{ $bien->surface_m2 }} m² @endif
                        @if($bien->surface_m2 && $bien->nombre_pieces) · @endif
                        @if($bien->nombre_pieces) {{ $bien->nombre_pieces }} pièces @endif
                    </div>
                </div>

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Adresse</div>
                    <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $bien->adresse }}</div>
                    <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $bien->ville }}</div>
                </div>

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Loyer mensuel</div>
                    <div style="font-weight:800;font-size:18px;color:var(--text);" class="text-money">
                        {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} FCFA
                    </div>
                </div>

                <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Commission agence</div>
                    <div style="font-weight:700;font-size:16px;color:var(--agency);">{{ $bien->taux_commission }}%</div>
                    <div style="font-size:12px;color:var(--text-3);margin-top:1px;">
                        TTC : {{ number_format($bien->loyer_mensuel * ($bien->taux_commission / 100) * 1.18, 0, ',', ' ') }} F
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                    <div style="padding:14px;background:var(--bg);border-radius:var(--radius-sm);">
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Propriétaire</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $bien->proprietaire->name }}</div>
                        <div style="font-size:12px;color:var(--text-2);margin-top:2px;">{{ $bien->proprietaire->telephone ?? '—' }}</div>
                    </div>
                @endif

            </div>

            @if($bien->description)
                <div style="margin-top:16px;padding:14px;background:var(--bg);border-radius:var(--radius-sm);border-left:3px solid var(--agency);">
                    <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">Description</div>
                    <div style="font-size:13px;color:var(--text-2);line-height:1.6;">{{ $bien->description }}</div>
                </div>
            @endif
        </div>
    </div>

    {{-- Galerie photos --}}
    <div class="card section-gap">
        <div class="card-header">
            <span class="card-title">
                Photos
                <span style="font-size:12px;font-weight:400;color:var(--text-3);margin-left:6px;">
                    {{ $bien->photos->count() }} photo(s)
                </span>
            </span>
        </div>

        @if($bien->photos->isNotEmpty())
            <div style="padding:16px;display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
                @foreach($bien->photos as $photo)
                    <div style="position:relative;border-radius:var(--radius-sm);overflow:hidden;border:2px solid {{ $photo->est_principale ? 'var(--agency)' : 'var(--border)' }};"
                         class="photo-item">
                        <img src="{{ $photo->url }}"
                             alt="{{ $bien->reference }}"
                             style="width:100%;height:140px;object-fit:cover;display:block;">

                        @if($photo->est_principale)
                            <div style="position:absolute;top:6px;left:6px;background:var(--agency);color:white;font-size:10px;font-weight:600;padding:2px 8px;border-radius:999px;">
                                Principale
                            </div>
                        @endif

                        <div class="photo-actions" style="position:absolute;inset:0;background:rgba(0,0,0,0);display:flex;align-items:center;justify-content:center;gap:6px;opacity:0;transition:all .2s;">
                            @if(!$photo->est_principale)
                                <form method="POST" action="{{ route('biens.photos.principale', [$bien, $photo]) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-sm" style="background:white;color:var(--agency);font-size:11px;">
                                        ⭐ Principale
                                    </button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('biens.photos.destroy', [$bien, $photo]) }}"
                                  onsubmit="return confirm('Supprimer cette photo ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:white;color:#dc2626;font-size:11px;">
                                    🗑
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Upload --}}
        @can('update', $bien)
            <div style="padding:16px;{{ $bien->photos->isNotEmpty() ? 'border-top:1px solid var(--border);' : '' }}">
                <form method="POST" action="{{ route('biens.photos.store', $bien) }}" enctype="multipart/form-data">
                    @csrf

                    @if($errors->has('photos') || $errors->has('photos.*'))
                        <div class="alert alert-error" style="margin-bottom:12px;">
                            @foreach($errors->get('photos.*') as $messages)
                                @foreach($messages as $msg)<div>❌ {{ $msg }}</div>@endforeach
                            @endforeach
                        </div>
                    @endif

                    <label for="photos-input"
                           style="display:flex;flex-direction:column;align-items:center;justify-content:center;width:100%;height:120px;border:2px dashed var(--border);border-radius:var(--radius);cursor:pointer;transition:all .15s;text-align:center;"
                           onmouseenter="this.style.borderColor='var(--agency)';this.style.background='var(--agency-soft)'"
                           onmouseleave="this.style.borderColor='var(--border)';this.style.background='transparent'">
                        <div style="font-size:28px;margin-bottom:8px;">📷</div>
                        <div style="font-size:13px;font-weight:500;color:var(--text-2);">Cliquer pour ajouter des photos</div>
                        <div style="font-size:11px;color:var(--text-3);margin-top:4px;">JPG, PNG, WEBP · Max 3 Mo · 10 photos max</div>
                        <input id="photos-input" type="file" name="photos[]" multiple
                               accept="image/jpeg,image/png,image/webp" style="display:none;"
                               onchange="previewPhotos(this)">
                    </label>

                    <div id="preview-container" style="display:none;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-top:12px;"></div>

                    <button type="submit" id="btn-upload"
                            style="display:none;margin-top:12px;width:100%;"
                            class="btn btn-primary">
                        Uploader les photos
                    </button>
                </form>
            </div>
        @endcan
    </div>

    {{-- Contrat actif --}}
    @if($contratActif)
        <div class="card section-gap" style="border-color:#bbf7d0;">
            <div class="card-header" style="background:#f0fdf4;">
                <span class="card-title" style="color:#15803d;">✅ Contrat actif</span>
                <a href="{{ route('admin.contrats.show', $contratActif) }}" class="btn btn-sm" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                    Voir le contrat →
                </a>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" id="contrat-actif-grid">
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Locataire</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ $contratActif->locataire->name }}</div>
                        <div style="font-size:12px;color:var(--text-2);">{{ $contratActif->locataire->telephone ?? '—' }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Début du bail</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);">{{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}</div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Loyer contractuel</div>
                        <div style="font-weight:800;font-size:16px;color:var(--text);" class="text-money">{{ number_format($contratActif->loyer_contractuel, 0, ',', ' ') }} FCFA</div>
                    </div>
                    <div>
                        <div style="font-size:10px;font-weight:600;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:4px;">Caution</div>
                        <div style="font-weight:700;font-size:14px;color:var(--text);" class="text-money">{{ number_format($contratActif->caution, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Historique paiements --}}
    @if($bien->contrats->isNotEmpty())
        <div class="card section-gap">
            <div class="card-header">
                <span class="card-title">Historique des paiements</span>
            </div>

            {{-- Mobile --}}
            <div class="mobile-cards" style="padding:12px;">
                @forelse($bien->contrats->flatMap->paiements->sortByDesc('periode')->take(10) as $p)
                    <div class="mobile-card">
                        <div class="flex-between" style="margin-bottom:8px;">
                            <span style="font-size:13px;color:var(--text-2);">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}</span>
                            <span class="{{ $p->statut === 'valide' ? 'badge badge-green' : 'badge badge-red' }}">
                                {{ ucfirst($p->statut) }}
                            </span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Locataire</span>
                            <span class="mobile-card-value">{{ $p->contrat->locataire->name }}</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Montant</span>
                            <span class="text-money" style="font-weight:700;">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</span>
                        </div>
                        <div class="mobile-card-row">
                            <span class="mobile-card-label">Net proprio</span>
                            <span class="text-money" style="color:#16a34a;font-weight:700;">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</span>
                        </div>
                    </div>
                @empty
                    <div style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucun paiement enregistré</div>
                @endforelse
            </div>

            {{-- Desktop --}}
            <div class="desktop-table table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Période</th>
                            <th>Locataire</th>
                            <th style="text-align:right;">Montant</th>
                            <th style="text-align:right;">Net proprio</th>
                            <th style="text-align:center;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bien->contrats->flatMap->paiements->sortByDesc('periode')->take(10) as $p)
                            <tr>
                                <td style="color:var(--text-2);">{{ \Carbon\Carbon::parse($p->periode)->translatedFormat('F Y') }}</td>
                                <td style="font-size:13px;">{{ $p->contrat->locataire->name }}</td>
                                <td style="text-align:right;" class="text-money">{{ number_format($p->montant_encaisse, 0, ',', ' ') }} F</td>
                                <td style="text-align:right;color:#16a34a;font-weight:700;" class="text-money">{{ number_format($p->net_proprietaire, 0, ',', ' ') }} F</td>
                                <td style="text-align:center;">
                                    <span class="{{ $p->statut === 'valide' ? 'badge badge-green' : 'badge badge-red' }}">
                                        {{ ucfirst($p->statut) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:32px;color:var(--text-3);font-size:13px;">Aucun paiement enregistré</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Zone dangereuse --}}
    @can('delete', $bien)
        @if($bien->statut !== 'loue')
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:var(--radius);padding:20px 24px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                    <div>
                        <div style="font-size:14px;font-weight:700;color:#dc2626;margin-bottom:4px;">⚠️ Zone dangereuse</div>
                        <div style="font-size:13px;color:#ef4444;">La suppression est irréversible. Impossible de supprimer un bien avec un contrat actif.</div>
                    </div>
                    <form method="POST" action="{{ route('biens.destroy', $bien) }}"
                          onsubmit="return confirm('Supprimer définitivement ce bien ?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer ce bien
                        </button>
                    </form>
                </div>
            </div>
        @endif
    @endcan

    <style>
        .photo-item:hover .photo-actions {
            background: rgba(0,0,0,.4) !important;
            opacity: 1 !important;
        }
        @media (min-width: 768px) {
            #bien-infos { grid-template-columns: repeat(3, 1fr); }
            #contrat-actif-grid { grid-template-columns: repeat(4, 1fr); }
        }
    </style>

    <script>
        function previewPhotos(input) {
            const container = document.getElementById('preview-container');
            const btn = document.getElementById('btn-upload');
            container.innerHTML = '';
            if (input.files.length === 0) {
                container.style.display = 'none';
                btn.style.display = 'none';
                return;
            }
            container.style.display = 'grid';
            btn.style.display = 'block';
            btn.textContent = `Uploader ${input.files.length} photo(s)`;
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = e => {
                    const div = document.createElement('div');
                    div.style.cssText = 'border-radius:8px;overflow:hidden;border:1px solid var(--border);';
                    div.innerHTML = `<img src="${e.target.result}" style="width:100%;height:80px;object-fit:cover;display:block;">`;
                    container.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>

</x-app-layout>