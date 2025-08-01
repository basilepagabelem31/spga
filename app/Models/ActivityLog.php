<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array', // Pour convertir le JSON en tableau
        'new_values' => 'array', // Pour convertir le JSON en tableau
    ];

    /**
     * L'utilisateur qui a effectué l'action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accesseur pour une description formatée de l'action.
     */
    public function getActionDescriptionAttribute(): string
    {
        $description = "L'utilisateur {$this->user->name} a effectué l'action '{$this->action}'.";
        if ($this->table_name && $this->record_id) {
            $description .= " Concerne la table '{$this->table_name}' avec l'ID {$this->record_id}.";
        }
        return $description;
    }
}