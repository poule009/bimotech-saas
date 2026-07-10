<?php

namespace App\Services\Import\Handlers;

use App\Models\ImportBatch;
use App\Services\Import\CodeSequencer;
use Carbon\Carbon;

/**
 * Base commune aux handlers d'import (un par type d'entité).
 *
 * Contrat :
 *  - validate() : juge une ligne brute SANS rien persister → verdict
 *      ['line','status'(valid|duplicate|error),'message','display'(cols),'data'(normalisé)]
 *  - create()   : crée l'entité à partir d'une ligne valide (appelé dans une transaction),
 *      attribue le code de liaison, et renvoie ['id','code','label'].
 *
 * $ctx est un accumulateur partagé entre les lignes d'un même fichier (caches DB,
 * détection de doublons intra-fichier). Chaque handler gère ses propres clés.
 */
abstract class AbstractImportHandler
{
    public function __construct(protected int $agencyId) {}

    abstract public function type(): string;

    /** Entêtes machine du modèle .xlsx (clés snake_case ASCII). */
    abstract public function templateHeaders(): array;

    /** Ligne d'exemple pour le modèle. */
    abstract public function templateSample(): array;

    /** Colonnes du tableau d'aperçu : ['cle' => 'Libellé'] (hors Ligne/Statut). */
    abstract public function previewColumns(): array;

    abstract public function validate(array $row, int $line, array &$ctx): array;

    abstract public function create(array $data, ImportBatch $batch, CodeSequencer $seq, int &$sequence): array;

    // ── Fabrique de verdicts ────────────────────────────────────────────────

    protected function ok(int $line, array $display, array $data): array
    {
        return ['line' => $line, 'status' => 'valid', 'message' => null, 'display' => $display, 'data' => $data];
    }

    protected function erreur(int $line, string $message, array $display = []): array
    {
        return ['line' => $line, 'status' => 'error', 'message' => $message, 'display' => $display, 'data' => []];
    }

    protected function doublon(int $line, string $message, array $display = []): array
    {
        return ['line' => $line, 'status' => 'duplicate', 'message' => $message, 'display' => $display, 'data' => []];
    }

    // ── Helpers de lecture/normalisation ────────────────────────────────────

    protected function val(array $row, string $key): ?string
    {
        $v = trim((string) ($row[$key] ?? ''));
        return $v !== '' ? $v : null;
    }

    /** Téléphone réduit à ses chiffres — clé de détection de doublon fiable. */
    protected function normPhone(?string $s): ?string
    {
        if ($s === null) return null;
        $digits = preg_replace('/\D+/', '', $s);
        return $digits !== '' ? $digits : null;
    }

    protected function parseDate(array $row, string $key): ?string
    {
        $v = $this->val($row, $key);
        if (! $v) return null;
        try {
            // Excel peut renvoyer un numéro de série de date — géré par Carbon si string.
            return Carbon::parse($v)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    protected function inList(array $row, string $key, array $allowed): ?string
    {
        $v = strtolower(trim((string) ($row[$key] ?? '')));
        return in_array($v, $allowed, true) ? $v : null;
    }

    public function estLigneVide(array $row): bool
    {
        foreach ($row as $v) {
            if (trim((string) $v) !== '') return false;
        }
        return true;
    }
}
