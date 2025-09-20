<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class ProductController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des produits avec des options de filtrage et de recherche.
     */
public function index(Request $request)
{
    $query = Product::with('category', 'partners');

    // Filtre de recherche
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    // Filtrage par catégorie
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }

    // Filtrage par provenance
    if ($request->filled('provenance_type')) {
        $query->where('provenance_type', $request->provenance_type);
    }

    // Filtrage par mode de production
    if ($request->filled('production_mode')) {
        $query->where('production_mode', $request->production_mode);
    }

    // Filtrage par statut
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ✅ Filtre produits en rupture de stock
    if ($request->input('filter') === 'out_of_stock') {
        $query->whereNotNull('alert_threshold')
              ->whereColumn('current_stock_quantity', '<=', 'alert_threshold');
    }

    $products = $query->paginate(8)->withQueryString();
    
    $categories = Category::all();
    $partners = Partner::all();

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
            'provenance_id' => 'nullable|sometimes|exists:partners,id',
            'production_mode' => ['required', Rule::in(['bio', 'agroécologie', 'conventionnel'])],
            'packaging_format' => 'nullable|string|max:255',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'sale_unit' => 'required|string|max:50',
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0',
        ]);

        if ($validatedData['provenance_type'] === 'ferme_propre') {
            $validatedData['provenance_id'] = null;
        }

        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        }

        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }

        $product = Product::create($validatedData);
        
        // Log de la création
        $this->recordLog(
            'creation_produit',
            'products',
            $product->id,
            null,
            $product->toArray()
        );

        return redirect()->route('products.index')
                         ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Affiche les détails d'un produit spécifique.
     */
    public function show(Product $product)
    {
        $product->load('category', 'partners');
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
        $oldValues = $product->toArray(); // Capture des valeurs avant la mise à jour

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'provenance_type' => ['required', Rule::in(['ferme_propre', 'producteur_partenaire'])],
            'provenance_id' => 'nullable|sometimes|exists:partners,id',
            'production_mode' => ['required', Rule::in(['bio', 'agroécologie', 'conventionnel'])],
            'packaging_format' => 'nullable|string|max:255',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'sale_unit' => 'required|string|max:50',
            'product_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0',
        ]);

        if ($validatedData['provenance_type'] === 'ferme_propre') {
            $validatedData['provenance_id'] = null;
        }

        if ($request->hasFile('product_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('product_image')->store('products', 'public');
            $validatedData['image'] = $imagePath;
        } elseif ($request->input('clear_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
                $validatedData['image'] = null;
            }
        } else {
            $validatedData['image'] = $product->image;
        }

        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }

        $product->update($validatedData);
        $newValues = $product->refresh()->toArray(); // Capture des nouvelles valeurs

        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_produit',
            'products',
            $product->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('products.index')
                         ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprime un produit de la base de données.
     */
    public function destroy(Product $product)
    {
        $oldValues = $product->toArray(); // Capture des valeurs avant la suppression
        $productId = $product->id;

        if ($product->orderItems()->count() > 0) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_produit',
                'products',
                $productId,
                ['error' => 'Produit lié à des commandes'],
                null
            );
            return redirect()->route('products.index')
                             ->with('error', 'Impossible de supprimer ce produit car il est lié à des commandes.');
        }
        if ($product->stocks()->count() > 0) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_produit',
                'products',
                $productId,
                ['error' => 'Produit lié à des stocks'],
                null
            );
            return redirect()->route('products.index')
                             ->with('error', 'Impossible de supprimer ce produit car il est lié à des stocks.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        
        // Log de la suppression réussie
        $this->recordLog(
            'suppression_produit',
            'products',
            $productId,
            $oldValues,
            null
        );

        return redirect()->route('products.index')
                         ->with('success', 'Produit supprimé avec succès.');
    }
}