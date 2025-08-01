<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'establishment_name',
        'contact_name',
        'function',
        'phone',
        'email',
        'locality_region',
        'type',
        'years_of_experience',
    ];

    /**
     * L'utilisateur associé à ce partenaire (si applicable).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Les produits fournis par ce partenaire.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'partner_products', 'partner_id', 'product_id');
    }

    /**
     * Les contrats signés avec ce partenaire.
     */
    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Récupère les informations de contact du partenaire.
     *
     * @return string
     */
    public function getContactInfo(): string
    {
        return "Contact: {$this->contact_name} - Fonction: {$this->function} - Téléphone: {$this->phone} - Email: {$this->email}";
    }
}