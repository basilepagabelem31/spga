<?php

namespace App\Http\Controllers;

use App\Models\ProductionFollowUp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductionFollowUpController extends Controller
{
    /**
     * Affiche la liste des suivis de production.
     */
    public function index()
    {
        $productionFollowUps = ProductionFollowUp::paginate(10);
        return view('production_follow_ups.index', compact('productionFollowUps'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau suivi de production.
     */
    public function create()
    {
        return view('production_follow_ups.create');
    }

    /**
     * Stocke un nouveau suivi de production dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'production_site' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'producer_name' => 'nullable|string|max:255',
            'technical_agent_name' => 'nullable|string|max:255',
            'follow_up_date' => 'required|date',
            'culture_name' => 'required|string|max:255',
            'cultivated_variety' => 'nullable|string|max:255',
            'sowing_planting_date' => 'nullable|date',
            'cultivated_surface' => 'nullable|numeric|min:0',
            'production_type' => ['required', Rule::in(['Conventionnel', 'Biologique', 'Agroécologie'])],
            'development_stage' => 'nullable|string|max:255',
            'works_performed' => 'nullable|string',
            'technical_observations' => 'nullable|string',
            'recommended_interventions' => 'nullable|string',
            'responsible_signature' => 'nullable|string|max:255',
        ]);

        ProductionFollowUp::create($request->all());

        return redirect()->route('production_follow_ups.index')
                         ->with('success', 'Suivi de production créé avec succès.');
    }

    /**
     * Affiche les détails d'un suivi de production spécifique.
     */
    public function show(ProductionFollowUp $productionFollowUp)
    {
        $productionFollowUp->load('estimatedHarvestDates');
        return view('production_follow_ups.show', compact('productionFollowUp'));
    }

    /**
     * Affiche le formulaire d'édition d'un suivi de production.
     */
    public function edit(ProductionFollowUp $productionFollowUp)
    {
        return view('production_follow_ups.edit', compact('productionFollowUp'));
    }

    /**
     * Met à jour un suivi de production existant dans la base de données.
     */
    public function update(Request $request, ProductionFollowUp $productionFollowUp)
    {
        $request->validate([
            'production_site' => 'required|string|max:255',
            'commune' => 'nullable|string|max:255',
            'village' => 'nullable|string|max:255',
            'producer_name' => 'nullable|string|max:255',
            'technical_agent_name' => 'nullable|string|max:255',
            'follow_up_date' => 'required|date',
            'culture_name' => 'required|string|max:255',
            'cultivated_variety' => 'nullable|string|max:255',
            'sowing_planting_date' => 'nullable|date',
            'cultivated_surface' => 'nullable|numeric|min:0',
            'production_type' => ['required', Rule::in(['Conventionnel', 'Biologique', 'Agroécologie'])],
            'development_stage' => 'nullable|string|max:255',
            'works_performed' => 'nullable|string',
            'technical_observations' => 'nullable|string',
            'recommended_interventions' => 'nullable|string',
            'responsible_signature' => 'nullable|string|max:255',
        ]);

        $productionFollowUp->update($request->all());

        return redirect()->route('production_follow_ups.index')
                         ->with('success', 'Suivi de production mis à jour avec succès.');
    }

    /**
     * Supprime un suivi de production de la base de données.
     */
    public function destroy(ProductionFollowUp $productionFollowUp)
    {
        if ($productionFollowUp->estimatedHarvestDates()->count() > 0) {
            return redirect()->route('production_follow_ups.index')
                             ->with('error', 'Impossible de supprimer ce suivi car il a des dates de récolte estimées.');
        }

        $productionFollowUp->delete();

        return redirect()->route('production_follow_ups.index')
                         ->with('success', 'Suivi de production supprimé avec succès.');
    }
}