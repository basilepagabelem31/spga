<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerProduct extends Model
{
    use HasFactory;

    protected $table = 'partner_products';

    protected $fillable = [
        'partner_id',
        'product_id',
    ];

    /**
     * Le partenaire associé.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Le produit associé.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}