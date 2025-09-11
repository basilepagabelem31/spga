<?php

namespace App\Http\Controllers;

use App\Models\QualityControl;
use App\Models\User; // Pour le contrôleur
use App\Models\Product; // Pour le produit
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QualityControlController extends Controller
{
    /**
     * Affiche la liste des contrôles qualité.
     */
    public function index()
{
    // Chargement des contrôles qualité avec les relations pour l'affichage
    $qualityControls = QualityControl::with(['controller', 'product'])->paginate(10);

    // Récupération des données nécessaires pour les modales
    // Assurez-vous que le rôle 'superviseur_production' existe
    $controllers = User::whereHas('role', function ($query) {
        $query->where('name', 'superviseur_production');
    })->get();
    
    $products = Product::all();

    // On passe toutes les variables à la vue
    return view('quality_controls.index', compact('qualityControls', 'controllers', 'products'));
}

    /**
     * Affiche le formulaire de création d'un nouveau contrôle qualité.
     */
    public function create()
    {
        $controllers = User::whereHas('role', function ($query) {
            // Filtrer par rôle approprié, ex: superviseur_production ou autre rôle dédié au QC
            $query->where('name', 'superviseur_production');
        })->get();
        $products = Product::all();
        return view('quality_controls.create', compact('controllers', 'products'));
    }

    /**
     * Stocke un nouveau contrôle qualité dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'control_date' => 'required|date',
            'controller_id' => 'required|exists:users,id',
            'production_unit' => 'nullable|string|max:255',
            'product_id' => 'required|exists:products,id',
            'lot_reference' => 'nullable|string|max:255',
            'control_type' => ['nullable', Rule::in(['Visuel', 'Physico-chimique', 'Microbiologique', 'Poids', 'Température'])],
            'method_used' => 'nullable|string|max:255',
            'control_result' => ['required', Rule::in(['Conforme', 'Non conforme', 'À réévaluer'])],
            'observed_non_conformities' => 'nullable|string',
            'proposed_corrective_actions' => 'nullable|string',
            'responsible_signature_qc' => 'nullable|string',
        ]);

        QualityControl::create($request->all());

        return redirect()->route('quality_controls.index')
                         ->with('success', 'Contrôle qualité créé avec succès.');
    }

    /**
     * Affiche les détails d'un contrôle qualité spécifique.
     */
    public function show(QualityControl $qualityControl)
    {
        $qualityControl->load('controller', 'product', 'nonConformities');
        return view('quality_controls.show', compact('qualityControl'));
    }

    /**
     * Affiche le formulaire d'édition d'un contrôle qualité.
     */
    public function edit(QualityControl $qualityControl)
    {
        $controllers = User::whereHas('role', function ($query) {
            $query->where('name', 'superviseur_production');
        })->get();
        $products = Product::all();
        return view('quality_controls.edit', compact('qualityControl', 'controllers', 'products'));
    }

    /**
     * Met à jour un contrôle qualité existant dans la base de données.
     */
    public function update(Request $request, QualityControl $qualityControl)
    {
        $request->validate([
            'control_date' => 'required|date',
            'controller_id' => 'required|exists:users,id',
            'production_unit' => 'nullable|string|max:255',
            'product_id' => 'required|exists:products,id',
            'lot_reference' => 'nullable|string|max:255',
            'control_type' => ['nullable', Rule::in(['Visuel', 'Physico-chimique', 'Microbiologique', 'Poids', 'Température'])],
            'method_used' => 'nullable|string|max:255',
            'control_result' => ['required', Rule::in(['Conforme', 'Non conforme', 'À réévaluer'])],
            'observed_non_conformities' => 'nullable|string',
            'proposed_corrective_actions' => 'nullable|string',
            'responsible_signature_qc' => 'nullable|string',
        ]);

        $qualityControl->update($request->all());

        return redirect()->route('quality_controls.index')
                         ->with('success', 'Contrôle qualité mis à jour avec succès.');
    }

    /**
     * Supprime un contrôle qualité de la base de données.
     */
    public function destroy(QualityControl $qualityControl)
    {
        if ($qualityControl->nonConformities()->count() > 0) {
            return redirect()->route('quality_controls.index')
                             ->with('error', 'Impossible de supprimer ce contrôle qualité car des non-conformités y sont liées.');
        }

        $qualityControl->delete();

        return redirect()->route('quality_controls.index')
                         ->with('success', 'Contrôle qualité supprimé avec succès.');
    }
}