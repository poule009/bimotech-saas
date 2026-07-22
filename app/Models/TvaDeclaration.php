<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class TvaDeclaration extends Model
{
    use HasAgencyScope;

    protected $table = 'tva_declarations_agence';

    /** Auto-injection de l'agence à la création (défense en profondeur). */
    protected static function booted(): void
    {
        static::creating(function (self $declaration) {
            if (empty($declaration->agency_id) && Auth::check()) {
                $declaration->agency_id = Auth::user()->agency_id;
            }
        });
    }

    protected $fillable = [
        'agency_id', 'mois', 'annee',
        'tva_commissions', 'tva_loyers_commerciaux', 'tva_charges_forfait', 'tva_honoraires',
        'total_tva_collectee',
        'tva_achats_fournitures', 'tva_loyer_bureau', 'tva_autres_deductible',
        'total_tva_deductible',
        'credit_reporte_entrant', 'tva_nette_due', 'credit_reporte_sortant',
        'statut', 'deposee_le', 'deposee_par', 'notes',
    ];

    protected $casts = [
        'mois'                   => 'integer',
        'annee'                  => 'integer',
        'tva_commissions'        => 'decimal:2',
        'tva_loyers_commerciaux' => 'decimal:2',
        'tva_charges_forfait'    => 'decimal:2',
        'tva_honoraires'         => 'decimal:2',
        'total_tva_collectee'    => 'decimal:2',
        'tva_achats_fournitures' => 'decimal:2',
        'tva_loyer_bureau'       => 'decimal:2',
        'tva_autres_deductible'  => 'decimal:2',
        'total_tva_deductible'   => 'decimal:2',
        'credit_reporte_entrant' => 'decimal:2',
        'tva_nette_due'          => 'decimal:2',
        'credit_reporte_sortant' => 'decimal:2',
        'deposee_le'             => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function deposePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deposee_par');
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeBrouillon($query)
    {
        return $query->where('statut', 'brouillon');
    }

    public function scopeValidee($query)
    {
        return $query->where('statut', 'validee');
    }

    public function scopeDeposee($query)
    {
        return $query->where('statut', 'deposee');
    }

    public function scopePourMois($query, int $mois, int $annee)
    {
        return $query->where('mois', $mois)->where('annee', $annee);
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Ex: "Mai 2026" */
    public function getPeriodeLabelAttribute(): string
    {
        return Carbon::create($this->annee, $this->mois, 1)
            ->locale('fr')
            ->translatedFormat('F Y');
    }

    /** Le 15 du mois M+1 — date limite légale (Art. 370 CGI SN) */
    public function getDateEcheanceAttribute(): Carbon
    {
        return Carbon::create($this->annee, $this->mois, 1)
            ->addMonth()
            ->day(15)
            ->startOfDay()
            ->timezone('Africa/Dakar');
    }

    /** Vrai si la déclaration n'est pas déposée ET que l'échéance est dépassée */
    public function getEstEnRetardAttribute(): bool
    {
        if ($this->statut === 'deposee') {
            return false;
        }
        return now()->timezone('Africa/Dakar')->gt($this->date_echeance);
    }

    // ── Méthodes ─────────────────────────────────────────────────────────────

    /**
     * Recalcule TVA nette due et crédit reportable, puis sauvegarde.
     * Formule : nette = collectée - déductible - crédit entrant
     * Si nette < 0 : crédit reportable = |nette|, nette_due = 0
     */
    public function calculerTvaNette(): void
    {
        $nette = (float) $this->total_tva_collectee
            - (float) $this->total_tva_deductible
            - (float) $this->credit_reporte_entrant;

        if ($nette > 0) {
            $this->tva_nette_due          = round($nette, 2);
            $this->credit_reporte_sortant = 0.00;
        } else {
            $this->tva_nette_due          = 0.00;
            $this->credit_reporte_sortant = round(abs($nette), 2);
        }

        $this->save();
    }
}
