<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Partner; // Pour la provenance
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage; // Importez le facade Storage

class ProductController extends Controller
{
    /**
     * Affiche la liste des produits avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Product::with('category', 'partners'); // Chargez aussi les partenaires liés si nécessaire pour l'affichage

        // Recherche par nom ou description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filtrer par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrer par type de provenance
        if ($request->filled('provenance_type')) {
            $query->where('provenance_type', $request->provenance_type);
        }

        // Filtrer par mode de production
        if ($request->filled('production_mode')) {
            $query->where('production_mode', $request->production_mode);
        }

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(10)->withQueryString();
        
        $categories = Category::all();
        $partners = Partner::all(); // Pour le filtre de provenance si applicable

        return view('products.index', compact('products', 'categories', 'partners'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau produit.
     */
    public function create()
    {
        $categories = Category::all();
        $partners = Partner::all();
        return view('products.create', compact('categories', 'partners'));
    }

    /**
     * Stocke un nouveau produit dans la base de données.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
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
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validation pour l'image
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0', // NOUVEAU : Ajout de la validation
        ]);

        // Logique pour gérer provenance_id
        if ($validatedData['provenance_type'] === 'ferme_propre') {
            $validatedData['provenance_id'] = null;
        }

        // Gérer le téléchargement de l'image
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('products', 'public'); // Stocke dans storage/app/public/products
            $validatedData['image'] = $imagePath;
        }

        // Convertir payment_modalities en JSON si ce n'est pas déjà fait
        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }

        Product::create($validatedData);

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
        $validatedData = $request->validate([
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
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validation pour l'image
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0', // NOUVEAU : Ajout de la validation
        ]);

        // Logique pour gérer provenance_id
        if ($validatedData['provenance_type'] === 'ferme_propre') {
            $validatedData['provenance_id'] = null;
        }

        // Gérer le remplacement/suppression de l'image
        if ($request->hasFile('product_image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('product_image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        } elseif ($request->input('clear_image')) { // Si la case 'supprimer l'image' est cochée
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
                $validatedData['image'] = null;
            }
        } else {
            // Conserver le chemin de l'image existante si aucune nouvelle image n'est téléchargée et qu'elle n'est pas effacée
            $validatedData['image'] = $product->image;
        }

        // Convertir payment_modalities en JSON si ce n'est pas déjà fait
        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }

        $product->update($validatedData);

        return redirect()->route('products.index')
                         ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit de la base de données.
     */
    public function destroy(Product $product)
    {
        // Vérifier les dépendances avant suppression (ex: dans OrderItems, Stocks, etc.)
        if ($product->orderItems()->count() > 0) {
            return redirect()->route('products.index')
                             ->with('error', 'Impossible de supprimer ce produit car il est lié à des commandes.');
        }
        if ($product->stocks()->count() > 0) {
            return redirect()->route('products.index')
                             ->with('error', 'Impossible de supprimer ce produit car il est lié à des stocks.');
        }
        // Vous pouvez ajouter d'autres vérifications ici si nécessaire (ex: qualityControls, nonConformities)

        // Supprimer l'image associée avant de supprimer le produit
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Produit supprimé avec succès.');
    }
}
