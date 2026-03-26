<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Contrat extends Model
{
    use HasFactory;
    protected $fillable = [
        'bien_id', 'locataire_id', 'date_debut', 'date_fin',
        'loyer_contractuel', 'caution', 'statut', 'observations'
    ];

    protected $casts = ['date_debut' => 'date', 'date_fin' => 'date'];

    public function bien()      { return $this->belongsTo(Bien::class); }
    public function locataire() { return $this->belongsTo(User::class, 'locataire_id'); }
    public function paiements() { return $this->hasMany(Paiement::class); }
}