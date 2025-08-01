<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'partner_id',
        'title',
        'file_path',
        'start_date',
        'end_date',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Le partenaire associé à ce contrat.
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Vérifie si le contrat est expiré.
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }
}