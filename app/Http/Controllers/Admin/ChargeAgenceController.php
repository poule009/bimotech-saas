<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreChargeAgenceRequest;
use App\Models\ChargeAgence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ChargeAgenceController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('check.feature:comptabilite'),
        ];
    }

    public function store(StoreChargeAgenceRequest $request): RedirectResponse
    {
        ChargeAgence::create($request->validated());

        return redirect()
            ->route('admin.comptabilite.index')
            ->with('success', 'Dépense enregistrée avec succès.');
    }
}
