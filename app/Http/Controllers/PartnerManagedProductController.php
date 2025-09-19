<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Traits\LogsActivity; // Ajout du trait pour le logging
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class PartnerManagedProductController extends Controller
{
    use AuthorizesRequests, LogsActivity; // Utilisation des traits

    /**
     * Display a listing of the products managed by the authenticated partner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $partnerId = Auth::user()->partner->id;
        
        $query = Product::where('provenance_type', 'producteur_partenaire')
                        ->where('provenance_id', $partnerId)
                        ->with('category');
        
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
        
        $products = $query->paginate(8);
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
            'current_stock_quantity' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'status' => ['required', Rule::in(['disponible', 'indisponible'])],
            'payment_modalities' => 'nullable|array',
            'estimated_harvest_quantity' => 'nullable|numeric|min:0',
            'estimated_harvest_period' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0',
        ]);
    
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('products', 'public');
        }

        if (isset($validatedData['payment_modalities']) && is_array($validatedData['payment_modalities'])) {
            $validatedData['payment_modalities'] = json_encode($validatedData['payment_modalities']);
        } else {
            $validatedData['payment_modalities'] = null;
        }

        $validatedData['provenance_type'] = 'producteur_partenaire';
        $validatedData['provenance_id'] = Auth::user()->partner->id;
        
        $product = Product::create($validatedData);

        // Log de la création
        $this->recordLog(
            'creation_produit_partenaire',
            'products',
            $product->id,
            null,
            $product->toArray()
        );

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
        $this->authorize('update', $product);
        $oldValues = $product->toArray(); // Capture des valeurs avant la mise à jour
        
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
        
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validatedData['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->input('clear_image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validatedData['image'] = null;
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
            'mise_a_jour_produit_partenaire',
            'products',
            $product->id,
            $oldValues,
            $newValues
        );

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
        $this->authorize('delete', $product);

        $oldValues = $product->toArray(); // Capture des valeurs avant la suppression
        $productId = $product->id;

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_produit_partenaire',
            'products',
            $productId,
            $oldValues,
            null
        );

        return redirect()->route('partenaire.products')
                         ->with('success', 'Produit supprimé avec succès.');
    }

    /**
     * Met à jour le stock d'un produit spécifique.
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function updateStock(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $validatedData = $request->validate([
            'quantity' => 'required|numeric|min:0',
        ]);

        $oldQuantity = $product->current_stock_quantity;
        $newQuantity = $validatedData['quantity'];
        $difference = $newQuantity - $oldQuantity;

        $product->current_stock_quantity = $newQuantity;
        $product->status = ($newQuantity > 0) ? 'disponible' : 'indisponible';
        $product->save();

        // Log de la mise à jour du stock du produit
        $this->recordLog(
            'mise_a_jour_stock_produit_partenaire',
            'products',
            $product->id,
            ['old_stock' => $oldQuantity],
            ['new_stock' => $newQuantity]
        );

        if ($difference !== 0) {
            $movementType = ($difference > 0) ? 'entrée' : 'sortie';
            $quantity = abs($difference);

            $stock = Stock::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'movement_type' => $movementType,
                'user_id' => Auth::id(),
                'reason' => 'Ajustement de stock par le partenaire',
                'movement_date' => now(),
            ]);

            // Log de la création du mouvement de stock
            $this->recordLog(
                'creation_mouvement_stock_produit',
                'stocks',
                $stock->id,
                null,
                $stock->toArray()
            );
        }

        return back()->with('success', 'Stock mis à jour avec succès.');
    }
}