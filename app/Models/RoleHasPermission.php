<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    use HasFactory;

    protected $table = 'role_has_permissions'; // Spécifier le nom de la table pivot
    public $incrementing = false; // Désactiver l'auto-incrémentation pour une clé primaire composite
    protected $primaryKey = ['role_id', 'permission_id']; // Définir la clé primaire composite

// AJOUTEZ CETTE LIGNE POUR DÉSACTIVER LES TIMESTAMPS
    public $timestamps = false; 

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    /**
     * Le rôle associé.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * La permission associée.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}