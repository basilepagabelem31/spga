<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimatedHarvestDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_follow_up_id',
        'speculation_name',
        'estimated_date',
    ];

    protected $casts = [
        'estimated_date' => 'date',
    ];

    /**
     * Le suivi de production associé à cette date estimée.
     */
    public function productionFollowUp()
    {
        return $this->belongsTo(ProductionFollowUp::class);
    }
}