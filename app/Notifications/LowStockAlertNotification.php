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
     * Le style utilisé ici est le standard de Laravel, similaire à votre e-mail.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->error()
                    ->subject('Alerte de Stock Faible: ' . $this->product->name)
                    ->greeting("Bonjour {$notifiable->name},")
                    ->line("Le stock du produit **{$this->product->name}** est tombé en dessous de son seuil d'alerte.")
                    ->line("Stock Actuel : **" . number_format($this->product->current_stock_quantity, 2) . " " . $this->product->sale_unit . "**")
                    ->line("Seuil d'Alerte : **" . number_format($this->product->alert_threshold, 2) . " " . $this->product->sale_unit . "**")
                    ->action('Voir le produit', url(route('products.show', $this->product->id)))
                    ->line('Veuillez prendre les mesures nécessaires.');
    }

    /**
     * Obtient la représentation de la notification pour le canal de base de données.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'Alerte de stock bas',
            'message' => "Le stock du produit '{$this->product->name}' est tombé en dessous du seuil d'alerte. Stock actuel : {$this->product->current_stock_quantity}.",
            'product_id' => $this->product->id,
        ];
    }
}