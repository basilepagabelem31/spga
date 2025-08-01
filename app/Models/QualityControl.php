<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QualityControl extends Model
{
    use HasFactory;

    protected $fillable = [
        'control_date',
        'controller_id',
        'production_unit',
        'product_id',
        'lot_reference',
        'control_type',
        'method_used',
        'control_result',
        'observed_non_conformities',
        'proposed_corrective_actions',
        'responsible_signature_qc',
    ];

    protected $casts = [
        'control_date' => 'datetime',
    ];

    /**
     * L'utilisateur qui a effectué le contrôle qualité.
     */
    public function controller()
    {
        return $this->belongsTo(User::class, 'controller_id');
    }

    /**
     * Le produit qui a été contrôlé.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Les non-conformités issues de ce contrôle.
     */
    public function nonConformities()
    {
        return $this->hasMany(NonConformity::class);
    }

    /**
     * Vérifie si le résultat du contrôle est conforme.
     */
    public function isConform(): bool
    {
        return $this->control_result === 'Conforme';
    }
}