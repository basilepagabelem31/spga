<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'provenance_type',
        'provenance_id',
        'production_mode',
        'packaging_format',
        'min_order_quantity',
        'unit_price',
        'sale_unit',
        'image',
        'status',
        'payment_modalities',
        'estimated_harvest_quantity',
        'estimated_harvest_period',
        'current_stock_quantity',
    ];

    protected $casts = [
        'payment_modalities' => 'array', // Pour convertir le JSON en tableau
    ];

    /**
     * La catégorie à laquelle appartient le produit.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Les partenaires qui fournissent ce produit.
     */
    public function partners()
    {
        return $this->belongsToMany(Partner::class, 'partner_products', 'product_id', 'partner_id');
    }

    /**
     * Les articles de commande qui incluent ce produit.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Le stock de ce produit.
     */
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * Les contrôles qualité effectués sur ce produit.
     */
    public function qualityControls()
    {
        return $this->hasMany(QualityControl::class);
    }

    /**
     * Les non-conformités liées à ce produit.
     */
    public function nonConformities()
    {
        return $this->hasMany(NonConformity::class);
    }

    /**
     * Vérifie si le produit est disponible.
     */
    public function isAvailable(): bool
    {
        return $this->status === 'disponible';
    }

    /**
     * Scope pour récupérer uniquement les produits disponibles.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponible');
    }

    /**
     * Calcule le prix total pour une quantité donnée.
     *
     * @param float $quantity
     * @return float
     */
    public function calculateTotalPrice(float $quantity): float
    {
        return $this->unit_price * $quantity;
    }

    /**
     * Accesseur pour récupérer le nom de la provenance (producteur/ferme).
     */
    public function getProvenanceNameAttribute(): ?string
    {
        if ($this->provenance_type === 'producteur_partenaire' && $this->provenance_id) {
            // Supposons que provenance_id se réfère à un ID de Partner
            $partner = Partner::find($this->provenance_id);
            return $partner ? $partner->establishment_name : null;
        }
        // Ajoutez ici la logique pour 'ferme_propre' si vous avez une table 'farms'
        return null;
    }

    /**
     * Vérifie si le stock actuel du produit est en dessous ou égal au seuil d'alerte.
     * @return bool
     */
    public function isLowStock(): bool
    {
        // Le stock est considéré bas si un seuil d'alerte est défini
        // et que la quantité actuelle est inférieure ou égale à ce seuil.
        return $this->alert_threshold !== null && $this->current_stock_quantity <= $this->alert_threshold;
    }
}
