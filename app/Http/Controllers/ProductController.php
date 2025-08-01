<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Partner; // Pour la provenance
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits.
     */
    public function index()
    {
        $products = Product::with('category')->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau produit.
     */
    public function create()
    {
        $categories = Category::all();
        $partners = Partner::all(); // Pour la liste des partenaires si provenance_type est 'producteur_partenaire'
        return view('products.create', compact('categories', 'partners'));
    }

    /**
     * Stocke un nouveau produit dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'provenance_type' => ['required', Rule::in(['ferme_propre', 'producteur_partenaire'])],
            'provenance_id' => 'nullable|sometimes|exists:partners,id', // Conditionnel selon provenance_type
            'production_mode' => ['required', Rule::in(['bio', 'agroécologie', 'conventionnel'])],
            'packaging_format' => 'nullable|string|max:255',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'sale_unit' => 'required|string|max:50',
            'image' => 'nullable|string|max:255', // Gérer le téléchargement de fichiers ici si image réelle
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
        ]);

        // Logique pour gérer provenance_id
        if ($request->provenance_type === 'ferme_propre') {
            $request->merge(['provenance_id' => null]); // ou un ID de ferme par défaut si vous avez une table 'farms'
        }

        Product::create($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affiche les détails d'un produit spécifique.
     */
    public function show(Product $product)
    {
        $product->load('category', 'partners'); // Charge les relations
        return view('products.show', compact('product'));
    }

    /**
     * Affiche le formulaire d'édition d'un produit.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $partners = Partner::all();
        return view('products.edit', compact('product', 'categories', 'partners'));
    }

    /**
     * Met à jour un produit existant dans la base de données.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'provenance_type' => ['required', Rule::in(['ferme_propre', 'producteur_partenaire'])],
            'provenance_id' => 'nullable|sometimes|exists:partners,id', // Conditionnel
            'production_mode' => ['required', Rule::in(['bio', 'agroécologie', 'conventionnel'])],
            'packaging_format' => 'nullable|string|max:255',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'sale_unit' => 'required|string|max:50',
            'image' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
        ]);

        if ($request->provenance_type === 'ferme_propre') {
            $request->merge(['provenance_id' => null]);
        }

        $product->update($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit de la base de données.
     */
    public function destroy(Product $product)
    {
        // Optionnel: Vérifier les dépendances avant suppression (ex: dans OrderItems, Stocks, etc.)
        if ($product->orderItems()->count() > 0 || $product->stocks()->count() > 0) {
            return redirect()->route('products.index')
                             ->with('error', 'Impossible de supprimer ce produit car il est lié à des commandes ou des stocks.');
        }

        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Produit supprimé avec succès.');
    }
}