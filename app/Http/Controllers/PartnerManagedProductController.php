<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock; // Importez le modèle Stock
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class PartnerManagedProductController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the products managed by the authenticated partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $partnerId = Auth::user()->partner->id;
        
        // Commencer par une requête de base pour les produits du partenaire
        $query = Product::where('provenance_type', 'producteur_partenaire')
                         ->where('provenance_id', $partnerId)
                         ->with('category'); // Charge la relation 'category' pour l'affichage
        
        // Appliquer les filtres si la requête les contient
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }
        if ($request->filled('production_mode')) {
            $query->where('production_mode', $request->input('production_mode'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Paginer les résultats pour éviter de charger trop de données à la fois
        $products = $query->paginate(10);
        $partnerId = Auth::user()->partner->id;
        $categories = Category::where('provenance_id', $partnerId)->get();

        return view('partners.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $partnerId = Auth::user()->partner->id;
        $categories = Category::where('provenance_id', $partnerId)->get();
        return view('partners.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'production_mode' => ['required', Rule::in(['bio', 'agroécologie', 'conventionnel'])],
            'packaging_format' => 'nullable|string|max:255',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'sale_unit' => 'required|string|max:50',
            'current_stock_quantity' => 'nullable|numeric|min:0', // Ajout de la validation pour le stock
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array', // On attend un tableau
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0', // Ajout de la validation pour le seuil d'alerte
        ]);
    
        // Gérer le téléchargement de l'image
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('products', 'public');
        }

        // Convertir payment_modalities en JSON si c'est un tableau
        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }

        $validatedData['provenance_type'] = 'producteur_partenaire';
        $validatedData['provenance_id'] = Auth::user()->partner->id;
        
        Product::create($validatedData);

        return redirect()->route('partenaire.products')
                         ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        // Vérification de la propriété du produit
        $this->authorize('update', $product);

        $partnerId = Auth::user()->partner->id;
        $categories = Category::where('provenance_id', $partnerId)->get();
        return view('partners.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        // Vérification de la propriété du produit via une politique
        $this->authorize('update', $product);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'production_mode' => ['required', Rule::in(['bio', 'agroécologie', 'conventionnel'])],
            'packaging_format' => 'nullable|string|max:255',
            'min_order_quantity' => 'nullable|numeric|min:0',
            'unit_price' => 'required|numeric|min:0',
            'sale_unit' => 'required|string|max:50',
            'current_stock_quantity' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0',
        ]);
        
        // Gérer le remplacement/suppression de l'image
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validatedData['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->input('clear_image')) {
            // Si la case 'supprimer l'image' est cochée
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validatedData['image'] = null;
        } else {
            // Conserver le chemin de l'image existante
            $validatedData['image'] = $product->image;
        }

        // Convertir payment_modalities en JSON si c'est un tableau
        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }
        
        $product->update($validatedData);

        return redirect()->route('partenaire.products')
                         ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        // Vérification de la propriété du produit via une politique
        $this->authorize('delete', $product);

        // Supprimer l'image associée si elle existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('partenaire.products')
                         ->with('success', 'Produit supprimé avec succès.');
    }




   public function updateStock(Request $request, Product $product)
{
    // 1. Vérification de la propriété du produit via une politique
    $this->authorize('update', $product);

    // 2. Validation de la quantité de stock
    $validatedData = $request->validate([
        'quantity' => 'required|numeric|min:0', // <-- Le nom a été corrigé ici
    ]);

    // 3. Récupérer l'ancienne quantité pour déterminer le mouvement
    $oldQuantity = $product->current_stock_quantity;
    $newQuantity = $validatedData['quantity']; // <-- Et ici aussi

    // 4. Déterminer la différence et le type de mouvement
    $difference = $newQuantity - $oldQuantity;

    // 5. Mettre à jour le statut du produit en fonction du nouveau stock
    if ($newQuantity > 0) {
        $product->status = 'disponible';
    } else {
        $product->status = 'indisponible';
    }

    // 6. Mettre à jour le stock et le statut du produit en une seule fois
    $product->current_stock_quantity = $newQuantity;
    $product->save();

    if ($difference !== 0) {
        $movementType = ($difference > 0) ? 'entrée' : 'sortie';
        $quantity = abs($difference);

        // 7. Créer un mouvement de stock dans la table 'stocks'
        Stock::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'movement_type' => $movementType,
            'user_id' => Auth::id(),
            'reason' => 'Ajustement de stock par le partenaire',
            'movement_date' => now(),
        ]);
    }

    return back()->with('success', 'Stock mis à jour avec succès.');
}
}