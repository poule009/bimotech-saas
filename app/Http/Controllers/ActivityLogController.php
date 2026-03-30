<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        abort_unless($user && in_array($user->role, ['superadmin', 'admin']), 403);

        $query = ActivityLog::with(['user', 'agency'])->latest();

        if ($user->role === 'admin') {
            $query->where('agency_id', $user->agency_id);
        }

        $logs = $query->paginate(30);

        return view('activity-logs.index', compact('logs'));
    }
}
