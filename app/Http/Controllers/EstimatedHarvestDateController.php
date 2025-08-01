<?php

namespace App\Http\Controllers;

use App\Models\EstimatedHarvestDate;
use App\Models\ProductionFollowUp;
use Illuminate\Http\Request;

class EstimatedHarvestDateController extends Controller
{
    /**
     * Affiche la liste des dates de récolte estimées.
     */
    public function index()
    {
        $estimatedHarvestDates = EstimatedHarvestDate::with('productionFollowUp')->paginate(10);
        return view('estimated_harvest_dates.index', compact('estimatedHarvestDates'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle date de récolte estimée.
     */
    public function create()
    {
        $productionFollowUps = ProductionFollowUp::all();
        return view('estimated_harvest_dates.create', compact('productionFollowUps'));
    }

    /**
     * Stocke une nouvelle date de récolte estimée.
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_follow_up_id' => 'required|exists:production_follow_ups,id',
            'speculation_name' => 'required|string|max:255',
            'estimated_date' => 'required|date',
        ]);

        EstimatedHarvestDate::create($request->all());

        return redirect()->route('estimated_harvest_dates.index')
                         ->with('success', 'Date de récolte estimée créée avec succès.');
    }

    /**
     * Affiche les détails d'une date de récolte estimée spécifique.
     */
    public function show(EstimatedHarvestDate $estimatedHarvestDate)
    {
        $estimatedHarvestDate->load('productionFollowUp');
        return view('estimated_harvest_dates.show', compact('estimatedHarvestDate'));
    }

    /**
     * Affiche le formulaire d'édition d'une date de récolte estimée.
     */
    public function edit(EstimatedHarvestDate $estimatedHarvestDate)
    {
        $productionFollowUps = ProductionFollowUp::all();
        return view('estimated_harvest_dates.edit', compact('estimatedHarvestDate', 'productionFollowUps'));
    }

    /**
     * Met à jour une date de récolte estimée existante.
     */
    public function update(Request $request, EstimatedHarvestDate $estimatedHarvestDate)
    {
        $request->validate([
            'production_follow_up_id' => 'required|exists:production_follow_ups,id',
            'speculation_name' => 'required|string|max:255',
            'estimated_date' => 'required|date',
        ]);

        $estimatedHarvestDate->update($request->all());

        return redirect()->route('estimated_harvest_dates.index')
                         ->with('success', 'Date de récolte estimée mise à jour avec succès.');
    }

    /**
     * Supprime une date de récolte estimée.
     */
    public function destroy(EstimatedHarvestDate $estimatedHarvestDate)
    {
        $estimatedHarvestDate->delete();

        return redirect()->route('estimated_harvest_dates.index')
                         ->with('success', 'Date de récolte estimée supprimée avec succès.');
    }
}