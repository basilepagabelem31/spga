<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
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
            'alert_threshold' => 'nullable|numeric|min:0',
            'movement_date' => 'nullable|date',
        ]);

        $stock = Stock::create($request->all());

        // NOUVEAU : Mettre à jour la quantité de stock actuelle du produit
        $this->updateProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type);

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock créé avec succès.');
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
            'alert_threshold' => 'nullable|numeric|min:0',
            'movement_date' => 'nullable|date',
        ]);

        $stock->update($request->all());

        // NOUVEAU : Ajuster la quantité de stock actuelle du produit après modification
        if ($oldProductId === $stock->product_id) {
            // Même produit, ajuster la quantité en fonction de l'ancien et du nouveau mouvement
            $this->revertProductStockQuantity($oldProductId, $oldQuantity, $oldMovementType); // Annuler l'ancien impact
            $this->updateProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type); // Appliquer le nouvel impact
        } else {
            // Produit changé, annuler l'impact sur l'ancien produit et appliquer sur le nouveau
            $this->revertProductStockQuantity($oldProductId, $oldQuantity, $oldMovementType);
            $this->updateProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type);
        }

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock mis à jour avec succès.');
    }

    /**
     * Supprime un mouvement de stock de la base de données.
     */
    public function destroy(Stock $stock)
    {
        // NOUVEAU : Revertir la quantité de stock actuelle du produit avant suppression
        $this->revertProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type);

        $stock->delete();

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock supprimé avec succès.');
    }

    /**
     * Met à jour la quantité de stock actuelle d'un produit.
     * @param int $productId
     * @param float $quantity
     * @param string $movementType
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
        }
    }

    /**
     * Annule l'impact d'un mouvement de stock sur la quantité actuelle d'un produit.
     * Utilisé avant la mise à jour ou la suppression d'un mouvement.
     * @param int $productId
     * @param float $quantity
     * @param string $movementType
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
        }
    }
}
