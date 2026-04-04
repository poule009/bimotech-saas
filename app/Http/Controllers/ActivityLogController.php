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

        /**
         * PERFORMANCE — select() sur les relations :
         *
         * La vue affiche uniquement user->name et agency->name.
         * Avant : with(['user', 'agency']) chargeait toutes les colonnes
         * (password hash, remember_token, couleur_primaire, logo_path...).
         *
         * Après : on ne charge que id + name pour chaque relation.
         */            if ($request->filled('q')) {
            $query->where('description', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('model')) {
            $query->where('model_type', 'like', '%' . $request->model);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }
        $logs = $query
            ->with([
                'user:id,name',
                'agency:id,name',
            ])
            ->paginate(30);

        return view('activity-logs.index', compact('logs'));
    }
}
