<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_route_id',
        'status',
        'delivery_proof_type',
        'delivery_proof_data',
        'recipient_name',
        'recipient_signature',
        'delivery_person_signature',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    /**
     * La commande associée à cette livraison.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * La tournée de livraison à laquelle cette livraison appartient.
     */
    public function deliveryRoute()
    {
        return $this->belongsTo(DeliveryRoute::class);
    }

    /**
     * Vérifie si la livraison est terminée.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'Terminée';
    }

    /**
     * Enregistre la preuve de livraison.
     *
     * @param string $type
     * @param string $data
     * @param string|null $recipientName
     * @param string|null $recipientSignature
     * @param string|null $deliveryPersonSignature
     * @return void
     */
    public function recordProof(string $type, string $data, ?string $recipientName = null, ?string $recipientSignature = null, ?string $deliveryPersonSignature = null): void
    {
        $this->update([
            'delivery_proof_type' => $type,
            'delivery_proof_data' => $data,
            'recipient_name' => $recipientName,
            'recipient_signature' => $recipientSignature,
            'delivery_person_signature' => $deliveryPersonSignature,
            'delivered_at' => now(),
            'status' => 'Terminée',
        ]);
    }
}