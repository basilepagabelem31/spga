<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionFollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_site',
        'commune',
        'village',
        'producer_name',
        'technical_agent_name',
        'follow_up_date',
        'culture_name',
        'cultivated_variety',
        'sowing_planting_date',
        'cultivated_surface',
        'production_type',
        'development_stage',
        'works_performed',
        'technical_observations',
        'recommended_interventions',
        'responsible_signature',
    ];

    protected $casts = [
        'follow_up_date' => 'date',
        'sowing_planting_date' => 'date',
    ];

    /**
     * Les dates de récolte estimées associées à ce suivi de production.
     */
    public function estimatedHarvestDates()
    {
        return $this->hasMany(EstimatedHarvestDate::class);
    }
}