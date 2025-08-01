<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'movement_type',
        'reference_id',
        'alert_threshold',
        'movement_date',
    ];

    protected $casts = [
        'movement_date' => 'datetime',
    ];

    /**
     * Le produit associé à ce stock.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Vérifie si le stock est en dessous du seuil d'alerte.
     */
    public function isLowStock(): bool
    {
        return $this->alert_threshold !== null && $this->quantity <= $this->alert_threshold;
    }
}