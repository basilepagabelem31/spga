<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User; // Pour le client
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderSupplierNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderItem;
    public $client;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, OrderItem $orderItem, User $client)
    {
        $this->order = $order;
        $this->orderItem = $orderItem;
        $this->client = $client;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouvelle Commande de Votre Produit: ' . $this->orderItem->product->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order-supplier-notification',
            with: [
                'orderCode' => $this->order->order_code,
                'clientName' => $this->client->name, // Supposons que le modèle User a une propriété 'name'
                'productName' => $this->orderItem->product->name,
                'orderedQuantity' => $this->orderItem->quantity,
                'saleUnit' => $this->orderItem->product->sale_unit,
                'currentStock' => $this->orderItem->product->current_stock_quantity,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
