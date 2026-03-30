@extends('layouts.app')

@section('content')
<div class="container" style="max-width:1200px;margin:24px auto;padding:0 16px;">
    <div class="card">
        <div class="card-header">
            <h2 style="margin:0;font-size:20px;">Journal d'activité</h2>
            <p style="margin:6px 0 0;color:#666;font-size:13px;">
                Affiché pour Super Admin (global) et Admin d'agence (filtré par agence).
            </p>
        </div>

        <div class="card-body" style="overflow:auto;">
            <table class="table" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Date</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Action</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Description</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Modèle</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">ID</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Utilisateur</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">Agence</th>
                        <th style="text-align:left;padding:10px;border-bottom:1px solid #eee;">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ optional($log->created_at)->format('d/m/Y H:i:s') }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                <span style="font-weight:600;">{{ $log->action }}</span>
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ $log->description }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ class_basename($log->model_type) }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ $log->model_id }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ $log->user->name ?? 'Système' }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ $log->agency->name ?? '-' }}
                            </td>
                            <td style="padding:10px;border-bottom:1px solid #f3f3f3;">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:18px;text-align:center;color:#666;">
                                Aucun log d'activité.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div style="margin-top:14px;">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
