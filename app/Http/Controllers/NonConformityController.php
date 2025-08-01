<?php

namespace App\Http\Controllers;

use App\Models\NonConformity;
use App\Models\Product;
use App\Models\QualityControl;
use App\Models\User; // Pour l'utilisateur qui prend la décision
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NonConformityController extends Controller
{
    /**
     * Affiche la liste des non-conformités.
     */
    public function index()
    {
        $nonConformities = NonConformity::with(['product', 'qualityControl', 'decisionTakenBy'])->paginate(10);
        return view('non_conformities.index', compact('nonConformities'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle non-conformité.
     */
    public function create()
    {
        $products = Product::all();
        $qualityControls = QualityControl::all();
        $decisionMakers = User::whereHas('role', function ($query) {
            // Ex: rôle de superviseur_production ou admin
            $query->whereIn('name', ['admin_principal', 'superviseur_production']);
        })->get();
        return view('non_conformities.create', compact('products', 'qualityControls', 'decisionMakers'));
    }

    /**
     * Stocke une nouvelle non-conformité dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quality_control_id' => 'required|exists:quality_controls,id',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['en attente de décision', 'rejeté', 'reconditionné'])],
            'decision_taken_by' => 'nullable|exists:users,id',
            'decision_date' => 'nullable|date',
        ]);

        NonConformity::create($request->all());

        return redirect()->route('non_conformities.index')
                         ->with('success', 'Non-conformité créée avec succès.');
    }

    /**
     * Affiche les détails d'une non-conformité spécifique.
     */
    public function show(NonConformity $nonConformity)
    {
        $nonConformity->load('product', 'qualityControl', 'decisionTakenBy');
        return view('non_conformities.show', compact('nonConformity'));
    }

    /**
     * Affiche le formulaire d'édition d'une non-conformité.
     */
    public function edit(NonConformity $nonConformity)
    {
        $products = Product::all();
        $qualityControls = QualityControl::all();
        $decisionMakers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_production']);
        })->get();
        return view('non_conformities.edit', compact('nonConformity', 'products', 'qualityControls', 'decisionMakers'));
    }

    /**
     * Met à jour une non-conformité existante dans la base de données.
     */
    public function update(Request $request, NonConformity $nonConformity)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quality_control_id' => 'required|exists:quality_controls,id',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['en attente de décision', 'rejeté', 'reconditionné'])],
            'decision_taken_by' => 'nullable|exists:users,id',
            'decision_date' => 'nullable|date',
        ]);

        $nonConformity->update($request->all());

        return redirect()->route('non_conformities.index')
                         ->with('success', 'Non-conformité mise à jour avec succès.');
    }

    /**
     * Supprime une non-conformité de la base de données.
     */
    public function destroy(NonConformity $nonConformity)
    {
        $nonConformity->delete();

        return redirect()->route('non_conformities.index')
                         ->with('success', 'Non-conformité supprimée avec succès.');
    }
}