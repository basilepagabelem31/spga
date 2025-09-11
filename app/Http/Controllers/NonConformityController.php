<?php

namespace App\Http\Controllers;

use App\Models\NonConformity;
use App\Models\Product;
use App\Models\QualityControl;
use App\Models\User;
use App\Models\Notification; // N'oubliez pas d'importer la classe Notification
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
        $products = Product::all();
        $qualityControls = QualityControl::all();
        $decisionMakers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_production']);
        })->get();

        return view('non_conformities.index', compact('nonConformities', 'products', 'qualityControls', 'decisionMakers'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle non-conformité.
     */
    public function create()
    {
        $products = Product::all();
        $qualityControls = QualityControl::all();
        $decisionMakers = User::whereHas('role', function ($query) {
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

        $nonConformity = NonConformity::create($request->all());

        // Récupérer le produit associé pour les partenaires
        $product = Product::find($request->product_id);

        // Récupérer les utilisateurs à notifier
        $decisionMakers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_production']);
        })->get();

        // Récupérer les partenaires associés au produit (en supposant une relation 'partners' sur le modèle Product)
        // Note: Assurez-vous que votre modèle Product a bien une relation 'partners' ou une relation similaire.
        $partners = $product->partners; // Exemple : $product->partners

        // Fusionner les collections d'utilisateurs
        $usersToNotify = $decisionMakers->merge($partners)->unique('id');

        // Créer une notification pour chaque utilisateur
        foreach ($usersToNotify as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => 'Nouvelle non-conformité',
                'message' => "Une nouvelle non-conformité a été déclarée pour le produit '{$product->name}'. Une décision est requise.",
            ]);
        }

        return redirect()->route('non_conformities.index')
            ->with('success', 'Non-conformité créée avec succès.');
    }

    /**
     * Affiche les détails d'une non-conformité spécifique.
     */
    public function show(NonConformity $nonConformity)
    {
        $nonConformity->load('product', 'qualityControl', 'decisionTakenBy');
        
        $products = Product::all();
        $qualityControls = QualityControl::all();
        $decisionMakers = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_production']);
        })->get();

        return view('non_conformities.show', compact('nonConformity', 'products', 'qualityControls', 'decisionMakers'));
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
        $oldStatus = $nonConformity->status;

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quality_control_id' => 'required|exists:quality_controls,id',
            'description' => 'nullable|string',
            'status' => ['required', Rule::in(['en attente de décision', 'rejeté', 'reconditionné'])],
            'decision_taken_by' => 'nullable|exists:users,id',
            'decision_date' => 'nullable|date',
        ]);

        $nonConformity->update($request->all());

        // Envoyer une notification si le statut change vers une décision finale
        if ($oldStatus === 'en attente de décision' && in_array($nonConformity->status, ['rejeté', 'reconditionné'])) {
            $creator = User::find($nonConformity->qualityControl->user_id); // Supposons que le créateur est l'utilisateur du contrôle qualité

            if ($creator) {
                Notification::create([
                    'user_id' => $creator->id,
                    'type' => 'Décision de non-conformité',
                    'message' => "Une décision a été prise concernant la non-conformité #{$nonConformity->id} : '{$nonConformity->status}'.",
                ]);
            }
        }

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