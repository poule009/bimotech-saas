@extends('layouts.app')

@section('title', 'Biens immobiliers')
@section('breadcrumb', 'Biens')

@section('content')

    {{-- En-tête avec bouton d'action conditionnel --}}
    <x-page-header
        title="Biens immobiliers"
        :subtitle="$biens->total() . ' bien(s) enregistré(s)'"
    >
        <x-slot:actions>
            @can('create', App\Models\Bien::class)
                <a href="{{ route('biens.create') }}" class="btn-primary">+ Nouveau bien</a>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- KPIs --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <x-kpi-card
            label="Total biens"
            :value="$biens->total()"
            color="gold"
        />
        <x-kpi-card
            label="Biens loués"
            :value="$biens->where('statut', 'loue')->count()"
            color="green"
        />
        <x-kpi-card
            label="Disponibles"
            :value="$biens->where('statut', 'disponible')->count()"
            color="blue"
        />
    </div>

    {{-- Filtres --}}
    <form method="GET" style="display: flex; gap: 8px; margin-bottom: 1.25rem; flex-wrap: wrap;">
        <select name="statut" onchange="this.form.submit()" style="font-family: 'DM Sans', sans-serif; font-size: 13px; border: 1px solid #d0d7de; border-radius: 8px; padding: 8px 12px; background: #fff; color: #1c2128;">
            <option value="">Tous les statuts</option>
            <option value="disponible" @selected(request('statut') === 'disponible')>Disponible</option>
            <option value="loue"       @selected(request('statut') === 'loue')>Loué</option>
            <option value="en_travaux" @selected(request('statut') === 'en_travaux')>En travaux</option>
        </select>
        <select name="type" onchange="this.form.submit()" style="font-family: 'DM Sans', sans-serif; font-size: 13px; border: 1px solid #d0d7de; border-radius: 8px; padding: 8px 12px; background: #fff; color: #1c2128;">
            <option value="">Tous les types</option>
            <option value="appartement" @selected(request('type') === 'appartement')>Appartement</option>
            <option value="villa"       @selected(request('type') === 'villa')>Villa</option>
            <option value="bureau"      @selected(request('type') === 'bureau')>Bureau</option>
            <option value="commerce"    @selected(request('type') === 'commerce')>Commerce</option>
        </select>
    </form>

    {{-- Liste des biens --}}
    @if($biens->isEmpty())
        <x-empty-state
            title="Aucun bien enregistré"
            description="Commencez par ajouter votre premier bien immobilier."
            action-label="Ajouter un bien"
            :action-url="route('biens.create')"
        />
    @else
        <div style="background: #fff; border-radius: 12px; border: 1px solid #eaeef2; overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13.5px;">
                <thead>
                    <tr style="background: #f6f8fa; border-bottom: 1px solid #eaeef2;">
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #57606a; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;">Référence</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #57606a; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;">Bien</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #57606a; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;">Propriétaire</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #57606a; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;">Loyer HC</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #57606a; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;">Statut</th>
                        <th style="padding: 12px 16px; text-align: left; font-weight: 500; color: #57606a; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;">Locataire</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biens as $bien)
                        <tr style="border-bottom: 1px solid #f0f3f6;" onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background='transparent'">
                            <td style="padding: 12px 16px; color: #8b949e; font-family: monospace; font-size: 12px;">
                                {{ $bien->reference }}
                            </td>
                            <td style="padding: 12px 16px;">
                                <div style="font-weight: 500; color: #1c2128;">{{ $bien->titre }}</div>
                                <div style="font-size: 12px; color: #8b949e;">{{ $bien->quartier }}, {{ $bien->ville }} · {{ $bien->surface }} m²</div>
                            </td>
                            <td style="padding: 12px 16px; color: #57606a;">
                                {{-- Eager loaded → pas de N+1 --}}
                                {{ $bien->proprietaire?->prenom }} {{ $bien->proprietaire?->nom }}
                            </td>
                            <td style="padding: 12px 16px; font-weight: 500; color: #1c2128;">
                                {{ number_format($bien->loyer_hors_charges, 0, ',', ' ') }} FCFA
                            </td>
                            <td style="padding: 12px 16px;">
                                {{-- Composant badge réutilisable --}}
                                <x-status-badge :status="$bien->statut" type="bien" />
                            </td>
<<<<<<< HEAD
                            <td>
                                
                                <div style="font-size:12px;color:#6b7280">{{ $bien->taux_commission }}%</div>
=======
                            <td style="padding: 12px 16px; color: #57606a; font-size: 13px;">
                                {{-- Eager loaded → pas de N+1 --}}
                                {{ $bien->contratActif?->locataire?->prenom ?? '—' }}
                                {{ $bien->contratActif?->locataire?->nom ?? '' }}
>>>>>>> 5b5bb31738ba6229eeabe5fd5a22c4edf79acdb0
                            </td>
                            <td style="padding: 12px 16px; text-align: right;">
                                <a href="{{ route('biens.show', $bien) }}" style="font-size: 13px; color: #c9a84c; text-decoration: none; font-weight: 500;">Voir →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div style="margin-top: 1.25rem;">
            {{ $biens->links() }}
        </div>
    @endif

@endsection