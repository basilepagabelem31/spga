<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_date',
        'driver_id',
        'vehicle_info',
        'status',
        'temporary_deliverers',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'temporary_deliverers' => 'array', // Pour convertir le JSON en tableau
    ];

    /**
     * Le chauffeur assigné à cette tournée de livraison.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Les livraisons incluses dans cette tournée.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Vérifie si la tournée de livraison est terminée.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'terminée';
    }
}