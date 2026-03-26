<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bien extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'proprietaire_id', 'reference', 'type', 'adresse', 'ville',
        'surface_m2', 'nombre_pieces', 'loyer_mensuel',
        'taux_commission', 'statut', 'description'
    ];

    protected $casts = [
        'loyer_mensuel'   => 'decimal:2',
        'taux_commission' => 'decimal:2',
    ];

    // Un bien appartient à UN propriétaire (User)
    public function proprietaire()
    {
        return $this->belongsTo(User::class, 'proprietaire_id');
    }

    // Un bien peut avoir plusieurs contrats (historique)
    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    // Le contrat actif en cours
    public function contratActif()
    {
        return $this->hasOne(Contrat::class)->where('statut', 'actif');
    }
    // Toutes les photos
public function photos()
{
    return $this->hasMany(BienPhoto::class)->orderBy('ordre');
}

// Photo principale
public function photoPrincipale()
{
    return $this->hasOne(BienPhoto::class)->where('est_principale', true);
}
}