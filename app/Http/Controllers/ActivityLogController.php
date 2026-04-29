<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();

        abort_unless($user && in_array($user->role, ['superadmin', 'admin']), 403);

        $query = ActivityLog::latest();

        if ($user->role === 'admin') {
            $query->where('agency_id', $user->agency_id);
        }

        // with() sélectif : on ne charge que id + name (pas password, remember_token…)
        if ($request->filled('q')) {
            // Echapper les wildcards LIKE pour que % et _ soient traités littéralement
            $q = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->q);
            $query->where('description', 'like', '%' . $q . '%');
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model')) {
            // Filtre exact sur le basename du modèle (ex: 'Paiement' → '%\\Paiement')
            $m = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $request->model);
            $query->where('model_type', 'like', '%\\\\' . $m);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        // Totaux par action sur l'ensemble des résultats filtrés (pas juste la page)
        $actionStats = (clone $query)
            ->reorder()
            ->selectRaw('action, COUNT(*) as total')
            ->groupBy('action')
            ->pluck('total', 'action');

        $logs = $query
            ->with([
                'user:id,name',
                'agency:id,name',
            ])
            ->paginate(30);

        return view('activity-logs.index', compact('logs', 'actionStats'));
    }
}
