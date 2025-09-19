<?php

namespace App\Http\Controllers;

use App\Models\QualityControl;
use App\Models\User;
use App\Models\Product;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class QualityControlController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des contrôles qualité.
     */
    public function index()
    {
        $qualityControls = QualityControl::with(['controller', 'product'])->paginate(8);

        $controllers = User::whereHas('role', function ($query) {
            $query->where('name', 'superviseur_production');
        })->get();
        
        $products = Product::all();

        return view('quality_controls.index', compact('qualityControls', 'controllers', 'products'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau contrôle qualité.
     */
    public function create()
    {
        $controllers = User::whereHas('role', function ($query) {
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

        $qualityControl = QualityControl::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_controle_qualite',
            'quality_controls',
            $qualityControl->id,
            null,
            $qualityControl->toArray()
        );

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
        $oldValues = $qualityControl->toArray(); // Capture des valeurs avant la mise à jour

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
        $newValues = $qualityControl->refresh()->toArray(); // Capture des nouvelles valeurs

        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_controle_qualite',
            'quality_controls',
            $qualityControl->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('quality_controls.index')
                         ->with('success', 'Contrôle qualité mis à jour avec succès.');
    }

    /**
     * Supprime un contrôle qualité de la base de données.
     */
    public function destroy(QualityControl $qualityControl)
    {
        $oldValues = $qualityControl->toArray(); // Capture des valeurs avant la suppression
        $controlId = $qualityControl->id;

        if ($qualityControl->nonConformities()->count() > 0) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_controle_qualite',
                'quality_controls',
                $controlId,
                ['error' => 'Contrôle qualité lié à des non-conformités'],
                null
            );
            return redirect()->route('quality_controls.index')
                             ->with('error', 'Impossible de supprimer ce contrôle qualité car des non-conformités y sont liées.');
        }

        $qualityControl->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_controle_qualite',
            'quality_controls',
            $controlId,
            $oldValues,
            null
        );

        return redirect()->route('quality_controls.index')
                         ->with('success', 'Contrôle qualité supprimé avec succès.');
    }
}