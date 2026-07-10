<?php

namespace App\Models;

use App\Models\Concerns\HasAgencyScope;
use App\Models\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * DepenseGestion — Frais engagés par l'agence pour le compte du propriétaire.
 *
 * Principe d'isolation locataire/propriétaire (invariant métier) :
 *   - Ces dépenses N'affectent JAMAIS montant_encaisse ni montant_net_locataire.
 *   - Elles sont déduites UNIQUEMENT via l'accesseur paiement.net_final_bailleur.
 *   - net_final_bailleur = paiement.montant_net_bailleur − SUM(depenses.montant)
 *
 * Exemples de dépenses : plombier, électricien, peintre, gardien, frais notaire…
 *
 * @property int         $id
 * @property int         $agency_id
 * @property int         $paiement_id
 * @property string      $libelle
 * @property float       $montant
 * @property string      $categorie
 * @property \Carbon\Carbon $date_depense
 * @property string|null $prestataire
 * @property string|null $justificatif_path
 * @property string|null $notes
 */
class DepenseGestion extends Model
{
    use HasFactory, HasAgencyScope, LogsActivity;

    protected $table = 'depenses_gestion';

    protected $fillable = [
        'agency_id',
        'paiement_id',
        'proprietaire_id',
        'libelle',
        'montant',
        'categorie',
        'date_depense',
        'prestataire',
        'justificatif_path',
        'notes',
    ];

    protected $casts = [
        'montant'      => 'decimal:2',
        'date_depense' => 'date',
    ];

    // ── Labels lisibles ──────────────────────────────────────────────────────

    public const CATEGORIES = [
        'plomberie'    => 'Plomberie',
        'electricite'  => 'Électricité',
        'peinture'     => 'Peinture',
        'menuiserie'   => 'Menuiserie',
        'gardiennage'  => 'Gardiennage',
        'nettoyage'    => 'Nettoyage',
        'frais_notaire'=> 'Frais notaire',
        'autre'        => 'Autre',
    ];

    // ── Audit ActivityLog ────────────────────────────────────────────────────

    public function getActivityLogTitle(): string
    {
        return 'Dépense de gestion « ' . ($this->libelle ?? '#' . $this->getKey()) . ' »';
    }

    // ── Auto-injection agency_id ─────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function (self $depense) {
            if (empty($depense->agency_id) && Auth::check()) {
                $depense->agency_id = Auth::user()->agency_id;
            }
        });
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function paiement(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Paiement::class);
    }

    /**
     * Propriétaire visé quand la dépense est « directe » (sans bien/paiement).
     */
    public function proprietaire(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    // ── Accesseurs ───────────────────────────────────────────────────────────

    public function getCategorieLibelleAttribute(): string
    {
        return self::CATEGORIES[$this->categorie] ?? ucfirst($this->categorie);
    }

    /**
     * Dépense « directe » = rattachée au propriétaire sans bien ni paiement.
     */
    public function getEstDirecteAttribute(): bool
    {
        return $this->paiement_id === null && $this->proprietaire_id !== null;
    }
}
