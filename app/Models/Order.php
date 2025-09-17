<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'order_code',
        'order_date',
        'desired_delivery_date',
        'delivery_location',
        'geolocation',
        'delivery_mode',
        'payment_mode',
        'status',
        'total_amount',
        'notes',
        'validated_by',
    ];

    protected $casts = [
    'order_date' => 'datetime',
    'desired_delivery_date' => 'date',
    ];


    // Relation avec le validateur (utilisateur)
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by_id');
    }

    /**
     * Le client qui a passé la commande.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * L'utilisateur qui a validé la commande.
     */
    public function validatedBy()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }



     public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }



public function getBadgeClass(): string
{
    switch ($this->status) {
        case 'Validée':
            return 'bg-success';
        case 'Annulée':
            return 'bg-danger';
        case 'En attente de validation':
            return 'bg-warning text-dark';
        case 'En préparation':
            return 'bg-info';
        case 'En livraison':
            return 'bg-primary';
        case 'Terminée':
            return 'bg-dark';
        default:
            return 'bg-secondary';
    }
}


    /**
     * Les articles de cette commande.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Les livraisons associées à cette commande.
     */
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    /**
     * Récupère le montant total de la commande.
     */
    public function getTotalAmount(): float
    {
        // Si total_amount est stocké, utilisez-le, sinon, recalculez
        if ($this->total_amount) {
            return $this->total_amount;
        }
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->unit_price_at_order;
        });
    }

    /**
     * Vérifie si la commande est en attente de validation.
     */
    public function isPendingValidation(): bool
    {
        return $this->status === 'En attente de validation';
    }

    /**
     * Vérifie si la commande est livrée.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'Livrée';
    }

   /**
     * Add an item to the order.
     *
     * @param Product $product Le produit doit être une instance de modèle Product chargée depuis la base de données.
     * @param float $quantity La quantité du produit à ajouter.
     * @return OrderItem L'élément de commande créé.
     */
    public function addItem(Product $product, float $quantity): OrderItem
    {
        // Assurez-vous que $product est bien un modèle Product chargé depuis la base de données
        // pour que ses propriétés comme 'id', 'sale_unit', 'unit_price' soient définies.
        return $this->orderItems()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'sale_unit_at_order' => $product->sale_unit,
            'unit_price_at_order' => $product->unit_price,
        ]);
    }
}