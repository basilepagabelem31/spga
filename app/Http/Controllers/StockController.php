<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Notifications\LowStockAlertNotification;
use App\Models\Product;
use App\Models\User;
use App\Models\Notification;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class StockController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des stocks avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Stock::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('reference_id_search')) {
            $query->where('reference_id', 'like', '%' . $request->reference_id_search . '%');
        }

        if ($request->filled('movement_date_from')) {
            $query->whereDate('movement_date', '>=', $request->movement_date_from);
        }
        if ($request->filled('movement_date_to')) {
            $query->whereDate('movement_date', '<=', $request->movement_date_to);
        }

        $stocks = $query->paginate(8)->withQueryString();

        $products = Product::all();
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

        $product = Product::find($request->input('product_id'));
        
        if ($request->input('movement_type') === 'sortie' && $product->current_stock_quantity < $request->input('quantity')) {
            // Log de l'échec de la création
            $this->recordLog(
                'echec_creation_mouvement_stock',
                'stocks',
                null,
                ['error' => 'Quantité insuffisante', 'product_id' => $request->product_id, 'requested_quantity' => $request->quantity],
                null
            );
            return back()->withInput()->with('error', 'La quantité en stock est insuffisante pour ce mouvement de sortie. ❌');
        }

        $stock = Stock::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_mouvement_stock',
            'stocks',
            $stock->id,
            null,
            $stock->toArray()
        );

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
        $oldValues = $stock->toArray(); // Capture des valeurs avant la mise à jour

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

        $oldProduct = Product::find($oldProductId);
        $newProduct = Product::find($newProductId);

        $finalStock = $oldProduct->current_stock_quantity;

        if ($oldMovementType === 'entrée' || $oldMovementType === 'future_recolte') {
            $finalStock -= $oldQuantity;
        } elseif ($oldMovementType === 'sortie') {
            $finalStock += $oldQuantity;
        }

        if ($newMovementType === 'entrée' || $newMovementType === 'future_recolte') {
            $finalStock += $newQuantity;
        } elseif ($newMovementType === 'sortie') {
            $finalStock -= $newQuantity;
        }

        if ($finalStock < 0) {
            // Log de l'échec de la mise à jour
            $this->recordLog(
                'echec_mise_a_jour_mouvement_stock',
                'stocks',
                $stock->id,
                ['error' => 'Stock final négatif', 'old_values' => $oldValues, 'new_data' => $request->all()],
                null
            );
            return back()->withInput()->with('error', 'Cette modification rendrait le stock négatif. L\'opération a été annulée. ❌');
        }
        
        if ($oldProductId === $newProductId) {
            $oldProduct->current_stock_quantity = $finalStock;
            $oldProduct->save();
            $oldProduct->updateAvailabilityStatus();
            $this->checkAndNotifyLowStock($oldProduct);
        } else {
            $oldProduct->current_stock_quantity = $finalStock;
            $oldProduct->save();
            $oldProduct->updateAvailabilityStatus();
            $this->checkAndNotifyLowStock($oldProduct);

            $newProduct->current_stock_quantity += ($newMovementType === 'entrée' || $newMovementType === 'future_recolte') ? $newQuantity : -$newQuantity;
            $newProduct->save();
            $newProduct->updateAvailabilityStatus();
            $this->checkAndNotifyLowStock($newProduct);
        }
        
        $stock->update($request->all());
        $newValues = $stock->refresh()->toArray();

        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_mouvement_stock',
            'stocks',
            $stock->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('stocks.index')->with('success', 'Mouvement de stock mis à jour avec succès. ✅');
    }

    /**
     * Supprime un mouvement de stock de la base de données.
     */
    public function destroy(Stock $stock)
    {
        $oldValues = $stock->toArray(); // Capture des valeurs avant la suppression
        $stockId = $stock->id;

        // On vérifie que la suppression ne rendra pas le stock négatif
        $product = Product::find($stock->product_id);
        $tempStock = $product->current_stock_quantity;
        if ($stock->movement_type === 'entrée' || $stock->movement_type === 'future_recolte') {
            $tempStock -= $stock->quantity;
        } elseif ($stock->movement_type === 'sortie') {
            $tempStock += $stock->quantity;
        }

        if ($tempStock < 0) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_mouvement_stock',
                'stocks',
                $stockId,
                ['error' => 'Suppression rendrait le stock négatif'],
                null
            );
            return redirect()->route('stocks.index')->with('error', 'Impossible de supprimer ce mouvement car cela rendrait le stock du produit négatif. ❌');
        }

        $this->revertProductStockQuantity($stock->product_id, $stock->quantity, $stock->movement_type);

        $stock->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_mouvement_stock',
            'stocks',
            $stockId,
            $oldValues,
            null
        );

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
        if ($product->alert_threshold !== null && $product->current_stock_quantity <= $product->alert_threshold) {
            // Log de l'alerte de stock bas
            $this->recordLog(
                'alerte_stock_bas',
                'products',
                $product->id,
                ['message' => 'Stock bas atteint ou dépassé', 'stock_actuel' => $product->current_stock_quantity, 'seuil_alerte' => $product->alert_threshold],
                null
            );

            $usersToNotify = User::whereHas('role', function ($query) {
                $query->whereIn('name', ['admin_principal', 'superviseur_production']);
            })->get();

            foreach ($usersToNotify as $user) {
                $user->notify(new LowStockAlertNotification($product));
            }
        }
    }
}