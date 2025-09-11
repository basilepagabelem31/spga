<?php

namespace App\Http\Controllers;

use App\Models\EstimatedHarvestDate;
use App\Models\ProductionFollowUp;
use Illuminate\Http\Request;

class EstimatedHarvestDateController extends Controller
{
    /**
     * Affiche la liste des dates de récolte estimées pour un suivi de production donné.
     * Cette méthode remplace la version précédente qui affichait toutes les dates.
     */
    public function index(ProductionFollowUp $productionFollowUp)
    {
        $estimatedHarvestDates = $productionFollowUp->estimatedHarvestDates()->paginate(10);
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

        $productionFollowUp->estimatedHarvestDates()->create($request->all());

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
     * Affiche le formulaire d'édition d'une date de récolte estimée pour un suivi de production donné.
     * La méthode `create` et `edit` ne sont plus nécessaires car nous utiliserons des modales.
     * Les données sont passées directement aux modales dans la vue d'index.
     */
    // La méthode 'create' n'est plus nécessaire car nous utilisons une modale dans la vue 'index'.
    // public function create() {}

    /**
     * Met à jour une date de récolte estimée existante pour le suivi de production donné.
     * S'assure que la date appartient bien au suivi de production parent.
     */
    public function update(Request $request, ProductionFollowUp $productionFollowUp, EstimatedHarvestDate $estimatedHarvestDate)
    {
        // La validation s'assure que la date et le nom de spéculation sont présents.
        $request->validate([
            'speculation_name' => 'required|string|max:255',
            'estimated_date' => 'required|date|after_or_equal:today',
        ]);

        $estimatedHarvestDate->update($request->all());

        return redirect()->route('production_follow_ups.estimated_harvest_dates.index', $productionFollowUp)
                         ->with('success', 'Date de récolte estimée mise à jour avec succès.');
    }

    /**
     * Supprime une date de récolte estimée pour le suivi de production donné.
     */
    public function destroy(ProductionFollowUp $productionFollowUp, EstimatedHarvestDate $estimatedHarvestDate)
    {
        $estimatedHarvestDate->delete();

        return redirect()->route('production_follow_ups.estimated_harvest_dates.index', $productionFollowUp)
                         ->with('success', 'Date de récolte estimée supprimée avec succès.');
    }
}