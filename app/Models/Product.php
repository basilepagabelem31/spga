<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; // DÉCOMMENTER CETTE LIGNE
use App\Mail\LowStockAlertMail; // DÉCOMMENTER CETTE LIGNE
use App\Models\User; // Assurez-vous que le modèle User est importé
use App\Models\Partner; // Assurez-vous que le modèle Partner est importé

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
        'alert_threshold',
    ];

    protected $casts = [
        'payment_modalities' => 'array',
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


     public function currentStock()
    {
        return $this->stocks()->sum('quantity');
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
            $partner = $this->getSupplierPartner();
            return $partner ? $partner->establishment_name : null;
        }
        return null;
    }

    /**
     * Récupère l'instance du partenaire fournisseur si le produit est de type 'producteur_partenaire'.
     * @return Partner|null
     */
    public function getSupplierPartner(): ?Partner
    {
        if ($this->provenance_type === 'producteur_partenaire' && $this->provenance_id) {
            return Partner::find($this->provenance_id);
        }
        return null;
    }

    /**
     * Vérifie si le stock actuel du produit est en dessous ou égal au seuil d'alerte.
     * @return bool
     */
    public function isLowStock(): bool
    {
        return $this->alert_threshold !== null && $this->current_stock_quantity <= $this->alert_threshold;
    }

    /**
     * Met à jour le statut de disponibilité du produit en fonction du stock actuel.
     * Le produit devient 'indisponible' si le stock est <= 0.
     * Le produit redevient 'disponible' si le stock est > 0 et qu'il était 'indisponible'.
     */
    public function updateAvailabilityStatus(): void
    {
        if ($this->current_stock_quantity <= 0 && $this->status !== 'indisponible') {
            $this->update(['status' => 'indisponible']);
        } elseif ($this->current_stock_quantity > 0 && $this->status === 'indisponible') {
            $this->update(['status' => 'disponible']);
        }
    }

    /**
     * Envoie une notification de stock faible aux administrateurs et au fournisseur concerné.
     */
    public function sendLowStockNotification(): void
    {
        // Vérifier si le stock est bas ou nul
        if (!$this->isLowStock() && $this->current_stock_quantity > 0) {
            return;
        }

        // Récupérer les adresses e-mail des administrateurs
        $adminEmails = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_commercial']);
        })->pluck('email')->filter()->all();

        $supplierEmail = null;
        $supplierPartner = $this->getSupplierPartner();
        if ($supplierPartner && $supplierPartner->email) {
            $supplierEmail = $supplierPartner->email;
        }

        // Collecter tous les destinataires
        $recipients = $adminEmails;
        if ($supplierEmail) {
            $recipients[] = $supplierEmail;
        }
        $recipients = array_unique($recipients);

        if (empty($recipients)) {
            Log::warning("Aucun destinataire trouvé pour l'alerte de stock du produit : {$this->name}.");
            return;
        }

        try {
            // ENVOI RÉEL DE L'E-MAIL
            Mail::to($recipients)->send(new LowStockAlertMail($this));
            Log::info("Alerte de stock envoyée pour le produit '{$this->name}' à: " . implode(', ', $recipients));
        } catch (\Exception $e) {
            Log::error("Échec de l'envoi de l'alerte de stock pour '{$this->name}': " . $e->getMessage());
        }
    }
}
