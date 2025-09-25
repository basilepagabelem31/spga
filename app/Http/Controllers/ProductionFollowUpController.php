<?php

namespace App\Http\Controllers;
use App\Services\Geolocalisation;

use App\Models\ProductionFollowUp;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class ProductionFollowUpController extends Controller
{

    protected $geolocalisation;

    public function __construct(Geolocalisation $geolocalisation)
    {
        $this->geolocalisation = $geolocalisation;
    }

    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des suivis de production.
     */
    public function index(Request $request)
    {
        $query = ProductionFollowUp::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('production_site', 'like', '%' . $search . '%')
                  ->orWhere('producer_name', 'like', '%' . $search . '%')
                  ->orWhere('culture_name', 'like', '%' . $search . '%');
            });
        }
        
        if ($request->filled('production_type')) {
            $query->where('production_type', $request->production_type);
        }

        $productionFollowUps = $query->paginate(8)->withQueryString();
        
        $productionTypes = ProductionFollowUp::select('production_type')->distinct()->get();

        return view('production_follow_ups.index', compact('productionFollowUps', 'productionTypes'));
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


    $coords = $this->geolocalisation->geocode($request->address);

    $data = $request->all();
    $data['latitude'] = $coords['latitude'];
    $data['longitude'] = $coords['longitude'];

    $productionFollowUp = ProductionFollowUp::create($data);


    $request->validate([
        'production_site' => 'required|string|max:255',
        'commune' => 'nullable|string|max:255',
        'village' => 'nullable|string|max:255',
        'address' => 'required|string|max:255', // Nouveau champ
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
        'responsible_signature' => 'nullable|string',
    ]);


    // $coords = $this->geolocalisation->geocode($request->address);
    // dd($coords);
    // dd(env('GOOGLE_MAPS_API_KEY'));
    $geo = new \App\Services\Geolocalisation();
    $coords = $geo->geocode('350 5th Ave, New York, NY 10118, United States');
    dd($coords);

    // Log de la création
    $this->recordLog(
        'creation_suivi_production',
        'production_follow_ups',
        $productionFollowUp->id,
        null,
        $productionFollowUp->toArray()
    );

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
    $oldValues = $productionFollowUp->toArray(); // Capture des valeurs avant la mise à jour

    $request->validate([
        'production_site' => 'required|string|max:255',
        'commune' => 'nullable|string|max:255',
        'village' => 'nullable|string|max:255',
        'address' => 'required|string|max:255', // Nouveau champ
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
        'responsible_signature' => 'nullable|string',
    ]);

    // Calculer latitude et longitude via le service Geolocalisation
    if ($request->filled('address')) {
        $coords = $this->geolocalisation->geocode($request->address);
        $request->merge($coords);
    }

    $productionFollowUp->update($request->all());
    $newValues = $productionFollowUp->refresh()->toArray(); // Capture des nouvelles valeurs

    // Log de la mise à jour
    $this->recordLog(
        'mise_a_jour_suivi_production',
        'production_follow_ups',
        $productionFollowUp->id,
        $oldValues,
        $newValues
    );

    return redirect()->route('production_follow_ups.index')
                     ->with('success', 'Suivi de production mis à jour avec succès.');
}


    /**
     * Supprime un suivi de production de la base de données.
     */
    public function destroy(ProductionFollowUp $productionFollowUp)
    {
        $oldValues = $productionFollowUp->toArray(); // Capture des valeurs avant la suppression
        $followUpId = $productionFollowUp->id;

        if ($productionFollowUp->estimatedHarvestDates()->count() > 0) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_suivi_production',
                'production_follow_ups',
                $followUpId,
                ['error' => 'Le suivi a des dates de récolte estimées'],
                null
            );
            return redirect()->route('production_follow_ups.index')
                             ->with('error', 'Impossible de supprimer ce suivi car il a des dates de récolte estimées.');
        }

        $productionFollowUp->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_suivi_production',
            'production_follow_ups',
            $followUpId,
            $oldValues,
            null
        );

        return redirect()->route('production_follow_ups.index')
                         ->with('success', 'Suivi de production supprimé avec succès.');
    }
}