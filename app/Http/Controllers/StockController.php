<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Notifications\LowStockAlertNotification;
use App\Models\Product;
use App\Models\User;
use App\Models\Notification; // Ajouté
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    /**
     * Affiche la liste des stocks avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Stock::with('product');

        // Filtrer par produit
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filtrer par type de mouvement
        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        // Recherche par référence
        if ($request->filled('reference_id_search')) {
            $query->where('reference_id', 'like', '%' . $request->reference_id_search . '%');
        }

        // Filtrer par date de mouvement (plage)
        if ($request->filled('movement_date_from')) {
            $query->whereDate('movement_date', '>=', $request->movement_date_from);
        }
        if ($request->filled('movement_date_to')) {
            $query->whereDate('movement_date', '<=', $request->movement_date_to);
        }

        $stocks = $query->paginate(10)->withQueryString();

        // Récupérer les produits pour les filtres et les modales
        $products = Product::all();
        
        // Définir les types de mouvement possibles pour le filtre
        $movementTypes = ['entrée', 'sortie', 'future_recolte'];

        return view('stocks.index', compact('stocks', 'products', 'movementTypes'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau mouvement de stock.
     */
    public function create()
    {
        $products = Product::all();
        return view('stocks.create', compact('products'));
    }

    /**
     * Stocke un nouveau mouvement de stock dans la base de données.
     */
    public function store(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0',
        'movement_type' => ['required', Rule::in(['entrée', 'sortie', 'future_recolte'])],
        'reference_id' => 'nullable|string|max:255',
        'movement_date' => 'nullable|date',
    ]);

    // Récupérer le produit
    $product = Product::find($request->input('product_id'));
    
    // Vérification du stock pour les mouvements de type 'sortie'
    if ($request->input('movement_type') === 'sortie' && $product->current_stock_quantity < $request->input('quantity')) {
        return back()->withInput()->with('error', 'La quantité en stock est insuffisante pour ce mouvement de sortie. ❌');
    }

    $stock = Stock::create($request->all());

    // Mettre à jour la quantité de stock actuelle du produit
    $this->updateProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type);

    return redirect()->route('stocks.index')->with('success', 'Mouvement de stock créé avec succès. ✅');
}

    /**
     * Affiche les détails d'un mouvement de stock spécifique.
     */
    public function show(Stock $stock)
    {
        $stock->load('product');
        return view('stocks.show', compact('stock'));
    }

    /**
     * Affiche le formulaire d'édition d'un mouvement de stock.
     */
    public function edit(Stock $stock)
    {
        $products = Product::all();
        return view('stocks.edit', compact('stock', 'products'));
    }

    /**
     * Met à jour un mouvement de stock existant dans la base de données.
     */
    public function update(Request $request, Stock $stock)
{
    $oldQuantity = $stock->quantity;
    $oldMovementType = $stock->movement_type;
    $oldProductId = $stock->product_id;

    $request->validate([
        'product_id' => 'required|exists:products,id',
        'quantity' => 'required|numeric|min:0',
        'movement_type' => ['required', Rule::in(['entrée', 'sortie', 'future_recolte'])],
        'reference_id' => 'nullable|string|max:255',
        'movement_date' => 'nullable|date',
    ]);

    $newProductId = $request->input('product_id');
    $newQuantity = $request->input('quantity');
    $newMovementType = $request->input('movement_type');

    // Récupérer le produit d'origine et le nouveau produit si le produit_id a changé
    $oldProduct = Product::find($oldProductId);
    $newProduct = Product::find($newProductId);

    // Calculer le stock final théorique
    $finalStock = $oldProduct->current_stock_quantity;

    // Annuler l'ancien mouvement
    if ($oldMovementType === 'entrée' || $oldMovementType === 'future_recolte') {
        $finalStock -= $oldQuantity;
    } elseif ($oldMovementType === 'sortie') {
        $finalStock += $oldQuantity;
    }

    // Appliquer le nouveau mouvement sur le stock final
    if ($newMovementType === 'entrée' || $newMovementType === 'future_recolte') {
        $finalStock += $newQuantity;
    } elseif ($newMovementType === 'sortie') {
        $finalStock -= $newQuantity;
    }

    // Vérification finale : le stock ne doit pas être négatif
    if ($finalStock < 0) {
        return back()->withInput()->with('error', 'Cette modification rendrait le stock négatif. L\'opération a été annulée. ❌');
    }
    
    // Si le produit n'a pas changé, mettre à jour directement
    if ($oldProductId === $newProductId) {
        $oldProduct->current_stock_quantity = $finalStock;
        $oldProduct->save();
        $oldProduct->updateAvailabilityStatus(); // On met à jour le statut
        $this->checkAndNotifyLowStock($oldProduct); // On vérifie si une alerte est nécessaire
    } else {
        // Mettre à jour l'ancien produit
        $oldProduct->current_stock_quantity = $finalStock;
        $oldProduct->save();
        $oldProduct->updateAvailabilityStatus();
        $this->checkAndNotifyLowStock($oldProduct);

        // Mettre à jour le nouveau produit
        $newProduct->current_stock_quantity += ($newMovementType === 'entrée' || $newMovementType === 'future_recolte') ? $newQuantity : -$newQuantity;
        $newProduct->save();
        $newProduct->updateAvailabilityStatus();
        $this->checkAndNotifyLowStock($newProduct);
    }
    
    $stock->update($request->all());

    return redirect()->route('stocks.index')->with('success', 'Mouvement de stock mis à jour avec succès. ✅');
}

    /**
     * Supprime un mouvement de stock de la base de données.
     */
    public function destroy(Stock $stock)
    {
        // Revertir la quantité de stock actuelle du produit avant suppression
        $this->revertProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type);

        $stock->delete();

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock supprimé avec succès.');
    }

    /**
     * Met à jour la quantité de stock actuelle d'un produit et son statut de disponibilité.
     */
    protected function updateProductStockQuantity(int $productId, float $quantity, string $movementType)
    {
        $product = Product::find($productId);
        if ($product) {
            if ($movementType === 'entrée' || $movementType === 'future_recolte') {
                $product->increment('current_stock_quantity', $quantity);
            } elseif ($movementType === 'sortie') {
                $product->decrement('current_stock_quantity', $quantity);
            }
            $product->refresh();
            $product->updateAvailabilityStatus();
            
            $this->checkAndNotifyLowStock($product);
        }
    }

    /**
     * Annule l'impact d'un mouvement de stock sur la quantité actuelle d'un produit et son statut.
     */
    protected function revertProductStockQuantity(int $productId, float $quantity, string $movementType)
    {
        $product = Product::find($productId);
        if ($product) {
            if ($movementType === 'entrée' || $movementType === 'future_recolte') {
                $product->decrement('current_stock_quantity', $quantity);
            } elseif ($movementType === 'sortie') {
                $product->increment('current_stock_quantity', $quantity);
            }
            $product->refresh();
            $product->updateAvailabilityStatus();
            
            $this->checkAndNotifyLowStock($product);
        }
    }

     /**
     * Vérifie si le stock d'un produit est bas et envoie une notification aux utilisateurs concernés.
     */
    private function checkAndNotifyLowStock(Product $product)
    {
        // On vérifie que le seuil d'alerte est bien défini et que le stock est inférieur ou égal à ce seuil
        if ($product->alert_threshold !== null && $product->current_stock_quantity <= $product->alert_threshold) {
            
            // Récupérer les utilisateurs ayant les rôles à notifier
            $usersToNotify = User::whereHas('role', function ($query) {
                $query->whereIn('name', ['admin_principal', 'superviseur_production']);
            })->get();

            foreach ($usersToNotify as $user) {
                // Utiliser la nouvelle classe de notification pour notifier l'utilisateur
                $user->notify(new LowStockAlertNotification($product));
            }
        }
    }
}