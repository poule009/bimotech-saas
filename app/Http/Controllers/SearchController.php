<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $agencyId = Auth::user()->agency_id;
        $results  = [];

        // ── Biens ────────────────────────────────────────────────────────────
        Bien::where('agency_id', $agencyId)
            ->where(function ($sub) use ($q) {
                $sub->where('reference', 'like', "%{$q}%")
                    ->orWhere('adresse',   'like', "%{$q}%")
                    ->orWhere('ville',     'like', "%{$q}%");
            })
            ->select(['id', 'reference', 'adresse', 'ville', 'statut'])
            ->limit(4)
            ->get()
            ->each(function ($b) use (&$results) {
                $results[] = [
                    'type'    => 'Bien',
                    'label'   => $b->reference,
                    'sub'     => $b->adresse . ', ' . $b->ville,
                    'badge'   => $b->statut,
                    'url'     => route('admin.biens.show', $b->id),
                    'icon'    => 'home',
                ];
            });

        // ── Contrats ─────────────────────────────────────────────────────────
        Contrat::where('agency_id', $agencyId)
            ->where(function ($sub) use ($q) {
                $sub->where('reference_bail', 'like', "%{$q}%")
                    ->orWhereHas('bien',      fn ($b) => $b->where('reference', 'like', "%{$q}%"))
                    ->orWhereHas('locataire', fn ($l) => $l->where('name', 'like', "%{$q}%"));
            })
            ->with(['bien:id,reference', 'locataire:id,name'])
            ->select(['id', 'bien_id', 'locataire_id', 'reference_bail', 'statut'])
            ->limit(4)
            ->get()
            ->each(function ($c) use (&$results) {
                $results[] = [
                    'type'  => 'Contrat',
                    'label' => $c->reference_bail ?? ('BAIL-' . $c->id),
                    'sub'   => ($c->bien?->reference ?? '—') . ' · ' . ($c->locataire?->name ?? '—'),
                    'badge' => $c->statut,
                    'url'   => route('admin.contrats.show', $c->id),
                    'icon'  => 'file',
                ];
            });

        // ── Locataires ───────────────────────────────────────────────────────
        User::where('agency_id', $agencyId)
            ->where('role', 'locataire')
            ->where(function ($sub) use ($q) {
                $sub->where('name',  'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('telephone', 'like', "%{$q}%");
            })
            ->select(['id', 'name', 'email', 'telephone'])
            ->limit(3)
            ->get()
            ->each(function ($u) use (&$results) {
                $results[] = [
                    'type'  => 'Locataire',
                    'label' => $u->name,
                    'sub'   => $u->email ?? $u->telephone ?? '',
                    'badge' => null,
                    'url'   => route('admin.users.show', $u->id),
                    'icon'  => 'user',
                ];
            });

        // ── Propriétaires ─────────────────────────────────────────────────────
        User::where('agency_id', $agencyId)
            ->where('role', 'proprietaire')
            ->where(function ($sub) use ($q) {
                $sub->where('name',  'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->select(['id', 'name', 'email'])
            ->limit(2)
            ->get()
            ->each(function ($u) use (&$results) {
                $results[] = [
                    'type'  => 'Propriétaire',
                    'label' => $u->name,
                    'sub'   => $u->email ?? '',
                    'badge' => null,
                    'url'   => route('admin.users.show', $u->id),
                    'icon'  => 'user',
                ];
            });

        return response()->json(['results' => $results]);
    }
}
