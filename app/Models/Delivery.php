<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\DeliveryCompletedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\StockService ;


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

            $oldStatus = $delivery->getOriginal('status');
            $newStatus = $delivery->status;

            // CAS 1 : Livraison terminée => déduction du stock
            if ($newStatus === 'Terminée' && $oldStatus !== 'Terminée') {
                DB::beginTransaction();
                try {
                    $order = $delivery->order;
                    $order->status = 'Terminée';
                    $order->save();

                    $order->load('orderItems.product');
                    app(\App\Services\StockService::class)->deductStockForOrder($order);

                    DB::commit();

                    // Notif client
                    if ($order->user) {
                        $order->user->notify(new \App\Notifications\DeliveryCompletedNotification($delivery));
                    }

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Erreur déduction stock (livraison {$delivery->id}) : " . $e->getMessage());
                }
            }

            // CAS 2 : Livraison annulée => remise en stock
            if ($newStatus === 'Annulée' && $oldStatus === 'Terminée') {
                DB::beginTransaction();
                try {
                    $order = $delivery->order;
                    $order->status = 'Annulée';
                    $order->save();

                    $order->load('orderItems.product');
                    app(\App\Services\StockService::class)->replenishStockForOrder($order);

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Erreur remise stock (livraison {$delivery->id}) : " . $e->getMessage());
                }
            }
        }
    });
}

}
