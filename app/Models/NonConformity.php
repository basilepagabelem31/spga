<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonConformity extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quality_control_id',
        'description',
        'status',
        'decision_taken_by',
        'decision_date',
    ];

    protected $casts = [
        'decision_date' => 'datetime',
    ];

    /**
     * Le produit concerné par la non-conformité.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Le contrôle qualité d'où provient la non-conformité.
     */
    public function qualityControl()
    {
        return $this->belongsTo(QualityControl::class);
    }

    /**
     * L'utilisateur qui a pris la décision concernant la non-conformité.
     */
    public function decisionTakenBy()
    {
        return $this->belongsTo(User::class, 'decision_taken_by');
    }

    /**
     * Vérifie si la non-conformité est résolue (rejetée ou reconditionnée).
     */
    public function isResolved(): bool
    {
        return in_array($this->status, ['rejeté', 'reconditionné']);
    }
}