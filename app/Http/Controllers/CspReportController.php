<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CspReportController extends Controller
{
    public function store(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        if (is_array($payload)) {
            // Le navigateur encapsule le rapport dans la clé "csp-report"
            $report = $payload['csp-report'] ?? $payload;

            Log::channel('csp')->info('CSP Violation', [
                'blocked-uri'        => $report['blocked-uri'] ?? null,
                'violated-directive' => $report['violated-directive'] ?? null,
                'document-uri'       => $report['document-uri'] ?? null,
                'source-file'        => $report['source-file'] ?? null,
                'line-number'        => $report['line-number'] ?? null,
            ]);
        }

        return response()->noContent();
    }
}
