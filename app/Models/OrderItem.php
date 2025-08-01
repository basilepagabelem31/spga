<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'sale_unit_at_order',
        'unit_price_at_order',
    ];

    /**
     * La commande à laquelle cet article appartient.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Le produit associé à cet article de commande.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calcule le total de la ligne pour cet article.
     */
    public function getLineTotal(): float
    {
        return $this->quantity * $this->unit_price_at_order;
    }
}