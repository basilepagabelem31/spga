<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowStockAlertNotification extends Notification
{
    use Queueable;

    protected $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * Définit les canaux de notification (e-mail et base de données).
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Obtient la représentation de la notification pour le canal e-mail.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // On récupère les données dont la vue a besoin
        $productName = $this->product->name;
        $currentStock = $this->product->current_stock_quantity;
        $alertThreshold = $this->product->alert_threshold;
        $saleUnit = $this->product->sale_unit;

        // On retourne un MailMessage qui utilise votre vue
        return (new MailMessage)
                    ->subject('Alerte de Stock Faible: ' . $this->product->name)
                    ->view('emails.low-stock-alert', compact('productName', 'currentStock', 'alertThreshold', 'saleUnit'));
    }

    /**
     * Obtient la représentation de la notification pour le canal de base de données.
     */
 public function toDatabase(object $notifiable): array
{
    return [
        'type' => 'Alerte de stock bas',
        'product_id' => $this->product->id,
        'product_name' => $this->product->name,
        'current_stock' => $this->product->current_stock_quantity,
        'alert_threshold' => $this->product->alert_threshold,
        'sale_unit' => $this->product->sale_unit,
        // message facultatif
        'message' => "Le stock du produit '{$this->product->name}' est tombé en dessous du seuil d'alerte. Stock actuel : {$this->product->current_stock_quantity}.",
    ];
}

}