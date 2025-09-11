<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\DeliveryCompletedNotification;

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
     */
    public function recordProof(
        string $type,
        string $data,
        ?string $recipientName = null,
        ?string $recipientSignature = null,
        ?string $deliveryPersonSignature = null
    ): void {
        // Mise à jour de la livraison
        $this->update([
            'delivery_proof_type' => $type,
            'delivery_proof_data' => $data,
            'recipient_name' => $recipientName,
            'recipient_signature' => $recipientSignature,
            'delivery_person_signature' => $deliveryPersonSignature,
            'delivered_at' => now(),
            'status' => 'Terminée',
        ]);

        // Recharger la relation pour garantir la mise à jour
        $this->load('order');

        // Notification au client
        if ($this->order && $this->status === 'Terminée') {
            $client = $this->order->user;
            if ($client) {
                $client->notify(new DeliveryCompletedNotification($this));
            }
        }
    }

    /**
     * Boot method pour gérer la synchronisation automatique avec la commande.
     */
    protected static function booted()
    {
        static::updated(function ($delivery) {
            if ($delivery->isDirty('status') && $delivery->order) {
                switch ($delivery->status) {
                    case 'Terminée':
                        $delivery->order->status = 'Terminée';
                        break;
                    case 'Annulée':
                        $delivery->order->status = 'Annulée';
                        break;
                    case 'En cours':
                        $delivery->order->status = 'En Livraison';
                        break;
                }
                $delivery->order->save();

                // Notification si statut changé en Terminée
                if ($delivery->status === 'Terminée') {
                    $client = $delivery->order->user;
                    if ($client) {
                        $client->notify(new DeliveryCompletedNotification($delivery));
                    }
                }
            }
        });
    }
}
