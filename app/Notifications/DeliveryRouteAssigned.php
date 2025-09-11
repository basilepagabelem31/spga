<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DeliveryRoute;

class DeliveryRouteAssigned extends Notification
{
    use Queueable;

    /**
     * La tournée de livraison assignée.
     *
     * @var \App\Models\DeliveryRoute
     */
    public $deliveryRoute;

    /**
     * Crée une nouvelle instance de la notification.
     *
     * @param  \App\Models\DeliveryRoute  $deliveryRoute
     * @return void
     */
    public function __construct(DeliveryRoute $deliveryRoute)
    {
        $this->deliveryRoute = $deliveryRoute;
    }

    /**
     * Obtient les canaux de diffusion de la notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Construit la représentation par e-mail de la notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nouvelle tournée de livraison assignée')
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Une nouvelle tournée de livraison vous a été assignée.')
            ->line('Détails de la tournée :')
            ->line('- Date de livraison : ' . $this->deliveryRoute->delivery_date->format('d/m/Y'))
            ->line('- Statut : ' . $this->deliveryRoute->status)
            ->action('Voir la tournée de livraison', route('chauffeur.planning'))
            ->line('Merci d\'utiliser notre application !');
    }

    /**
     * Obtient la représentation de la notification sous forme de tableau.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'delivery_route_id' => $this->deliveryRoute->id,
            'delivery_date' => $this->deliveryRoute->delivery_date,
            'message' => 'Une nouvelle tournée de livraison vous a été assignée.',
            'status' => $this->deliveryRoute->status,
        ];
    }
}