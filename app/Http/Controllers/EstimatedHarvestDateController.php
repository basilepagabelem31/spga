<?php

namespace App\Http\Controllers;

use App\Models\EstimatedHarvestDate;
use App\Models\ProductionFollowUp;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;

class EstimatedHarvestDateController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des dates de récolte estimées pour un suivi de production donné.
     * Cette méthode remplace la version précédente qui affichait toutes les dates.
     */
    public function index(ProductionFollowUp $productionFollowUp)
    {
        $estimatedHarvestDates = $productionFollowUp->estimatedHarvestDates()->paginate(8);
        return view('estimated_harvest_dates.index', compact('productionFollowUp', 'estimatedHarvestDates'));
    }

    /**
     * Stocke une nouvelle date de récolte estimée pour le suivi de production donné.
     * Cette méthode remplace la version précédente qui nécessitait un `production_follow_up_id` dans la requête.
     */
    public function store(Request $request, ProductionFollowUp $productionFollowUp)
    {
        $request->validate([
            'speculation_name' => 'required|string|max:255',
            'estimated_date' => 'required|date|after_or_equal:today',
        ]);

        $estimatedHarvestDate = $productionFollowUp->estimatedHarvestDates()->create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_date_recolte',
            'estimated_harvest_dates',
            $estimatedHarvestDate->id,
            null,
            $estimatedHarvestDate->toArray()
        );

        return redirect()->route('production_follow_ups.estimated_harvest_dates.index', $productionFollowUp)
                         ->with('success', 'Date de récolte estimée ajoutée avec succès.');
    }

    /**
     * Affiche les détails d'une date de récolte estimée spécifique pour un suivi de production donné.
     * La route imbriquée s'occupe de l'association.
     */
    public function show(ProductionFollowUp $productionFollowUp, EstimatedHarvestDate $estimatedHarvestDate)
    {
        return view('estimated_harvest_dates.show', compact('productionFollowUp', 'estimatedHarvestDate'));
    }

    /**
     * Met à jour une date de récolte estimée existante pour le suivi de production donné.
     * S'assure que la date appartient bien au suivi de production parent.
     */
    public function update(Request $request, ProductionFollowUp $productionFollowUp, EstimatedHarvestDate $estimatedHarvestDate)
    {
        $oldValues = $estimatedHarvestDate->toArray(); // Capture des valeurs avant la mise à jour

        $request->validate([
            'speculation_name' => 'required|string|max:255',
            'estimated_date' => 'required|date|after_or_equal:today',
        ]);

        $estimatedHarvestDate->update($request->all());
        $newValues = $estimatedHarvestDate->refresh()->toArray(); // Capture des nouvelles valeurs

        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_date_recolte',
            'estimated_harvest_dates',
            $estimatedHarvestDate->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('production_follow_ups.estimated_harvest_dates.index', $productionFollowUp)
                         ->with('success', 'Date de récolte estimée mise à jour avec succès.');
    }

    /**
     * Supprime une date de récolte estimée pour le suivi de production donné.
     */
    public function destroy(ProductionFollowUp $productionFollowUp, EstimatedHarvestDate $estimatedHarvestDate)
    {
        $oldValues = $estimatedHarvestDate->toArray(); // Capture des valeurs avant la suppression
        $estimatedHarvestDateId = $estimatedHarvestDate->id;

        $estimatedHarvestDate->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_date_recolte',
            'estimated_harvest_dates',
            $estimatedHarvestDateId,
            $oldValues,
            null
        );

        return redirect()->route('production_follow_ups.estimated_harvest_dates.index', $productionFollowUp)
                         ->with('success', 'Date de récolte estimée supprimée avec succès.');
    }
}