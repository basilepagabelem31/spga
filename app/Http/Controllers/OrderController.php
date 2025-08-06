<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Order::with(['client', 'validatedBy']);

        // Filtrer par client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filtrer par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Recherche par code de commande
        if ($request->filled('order_code_search')) {
            $query->where('order_code', 'like', '%' . $request->order_code_search . '%');
        }

        // Filtrer par date de commande (plage)
        if ($request->filled('order_date_from')) {
            $query->whereDate('order_date', '>=', $request->order_date_from);
        }
        if ($request->filled('order_date_to')) {
            $query->whereDate('order_date', '<=', $request->order_date_to);
        }

        $orders = $query->paginate(10)->withQueryString();

        // Récupérer les listes pour les filtres et les modales
        $clients = User::whereHas('role', function ($query) {
            $query->where('name', 'client');
        })->get();
        
        // Définir les statuts possibles pour le filtre
        $statuses = ['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée'];

        // Récupérer les produits disponibles pour les modales (création/édition)
        $products = Product::available()->get(); 
        
        // Récupérer les validateurs pour les modales (création/édition)
        $validators = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_commercial']);
        })->get();

        return view('orders.index', compact('orders', 'clients', 'statuses', 'products', 'validators'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle commande.
     */
    public function create()
    {
        $clients = User::whereHas('role', function ($query) {
            $query->where('name', 'client');
        })->get();
        $products = Product::available()->get();
        $validators = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_commercial']);
        })->get();

        return view('orders.create', compact('clients', 'products', 'validators'));
    }

    /**
     * Stocke un nouveau commande dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'desired_delivery_date' => 'nullable|string|max:255',
            'delivery_location' => 'nullable|string',
            'geolocation' => 'nullable|string|max:255',
            'delivery_mode' => ['required', Rule::in(['standard_72h', 'express_6_12h'])],
            'payment_mode' => ['required', Rule::in(['paiement_mobile', 'paiement_a_la_livraison', 'virement_bancaire'])],
            'status' => ['required', Rule::in(['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée'])],
            'notes' => 'nullable|string',
            'validated_by' => 'nullable|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $order_code = 'CMD-' . strtoupper(Str::random(8));

        $order = Order::create([
            'client_id' => $request->client_id,
            'order_code' => $order_code,
            'order_date' => now(),
            'desired_delivery_date' => $request->desired_delivery_date,
            'delivery_location' => $request->delivery_location,
            'geolocation' => $request->geolocation,
            'delivery_mode' => $request->delivery_mode,
            'payment_mode' => $request->payment_mode,
            'status' => $request->status,
            'notes' => $request->notes,
            'validated_by' => $request->validated_by,
            'total_amount' => 0,
        ]);

        $totalAmount = 0;
        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $order->addItem($product, $item['quantity']);
                $totalAmount += $product->unit_price * $item['quantity'];
            }
        }

        $order->update(['total_amount' => $totalAmount]);

        if (in_array($order->status, ['Validée', 'En préparation'])) {
            $this->deductStockForOrder($order);
        }

        return redirect()->route('orders.index')
                         ->with('success', 'Commande créée avec succès.');
    }

    /**
     * Affiche les détails d'une commande spécifique.
     */
    public function show(Order $order)
    {
        $order->load('client', 'validatedBy', 'orderItems.product', 'deliveries');
        return view('orders.show', compact('order'));
    }

    /**
     * Affiche le formulaire d'édition d'une commande.
     */
    public function edit(Order $order)
    {
        $clients = User::whereHas('role', function ($query) {
            $query->where('name', 'client');
        })->get();
        $products = Product::available()->get();
        $validators = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_commercial']);
        })->get();

        $order->load('orderItems.product');

        return view('orders.edit', compact('order', 'clients', 'products', 'validators'));
    }

    /**
     * Met à jour une commande existante dans la base de données.
     */
    public function update(Request $request, Order $order)
    {
        $oldStatus = $order->status;
        $oldOrderItems = $order->orderItems->keyBy('product_id'); // Clé par product_id pour comparaison facile

        $request->validate([
            'client_id' => 'required|exists:users,id',
            'desired_delivery_date' => 'nullable|string|max:255',
            'delivery_location' => 'nullable|string',
            'geolocation' => 'nullable|string|max:255',
            'delivery_mode' => ['required', Rule::in(['standard_72h', 'express_6_12h'])],
            'payment_mode' => ['required', Rule::in(['paiement_mobile', 'paiement_a_la_livraison', 'virement_bancaire'])],
            'status' => ['required', Rule::in(['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée'])],
            'notes' => 'nullable|string',
            'validated_by' => 'nullable|exists:users,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $orderData = $request->except('products');
        $order->update($orderData);

        $newStatus = $request->status;
        $newOrderItems = collect($request->products)->keyBy('id');

        // NOUVEAU : Gérer les ajustements de stock basés sur les changements d'articles
        $this->adjustStockForOrderItemsChange($order, $oldOrderItems, $newOrderItems);

        // NOUVEAU : Gérer la déduction/remise en stock en fonction du changement de statut
        if (in_array($newStatus, ['Validée', 'En préparation']) && !in_array($oldStatus, ['Validée', 'En préparation'])) {
            $this->deductStockForOrder($order);
        } elseif ($newStatus === 'Annulée' && $oldStatus !== 'Annulée') {
            if (in_array($oldStatus, ['Validée', 'En préparation', 'En livraison', 'Livrée'])) {
                 $this->replenishStockForOrder($order);
            }
        } elseif ($newStatus === 'En attente de validation' && in_array($oldStatus, ['Validée', 'En préparation', 'En livraison', 'Livrée'])) {
            // Si la commande repasse en attente après avoir été validée/préparée, on remet le stock
            $this->replenishStockForOrder($order);
        }


        // Mettre à jour les OrderItems
        $order->orderItems()->delete(); // Supprime les anciens articles
        $totalAmount = 0;
        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $order->addItem($product, $item['quantity']);
                $totalAmount += $product->unit_price * $item['quantity'];
            }
        }
        $order->update(['total_amount' => $totalAmount]);


        return redirect()->route('orders.index')
                         ->with('success', 'Commande mise à jour avec succès.');
    }

    /**
     * Supprime une commande de la base de données.
     */
    public function destroy(Order $order)
    {
        // NOUVEAU : Remettre le stock si la commande était validée/en préparation avant suppression
        if (in_array($order->status, ['Validée', 'En préparation', 'En livraison', 'Livrée'])) {
            $this->replenishStockForOrder($order);
        }

        $order->delete();

        return redirect()->route('orders.index')
                         ->with('success', 'Commande supprimée avec succès.');
    }

    /**
     * Déduit les produits du stock pour une commande donnée.
     * @param Order $order
     */
    protected function deductStockForOrder(Order $order)
    {
        foreach ($order->orderItems as $item) {
            Stock::create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'movement_type' => 'sortie',
                'reference_id' => $order->order_code,
                'movement_date' => now(),
            ]);
            // NOUVEAU : Décrémenter la quantité de stock actuelle du produit
            $product = Product::find($item->product_id);
            if ($product) {
                $product->decrement('current_stock_quantity', $item->quantity);
            }
        }
    }

    /**
     * Remet les produits en stock pour une commande annulée ou supprimée.
     * @param Order $order
     */
    protected function replenishStockForOrder(Order $order)
    {
        foreach ($order->orderItems as $item) {
            Stock::create([
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'movement_type' => 'entrée',
                'reference_id' => 'ANNULATION_CMD_' . $order->order_code, // Référence spécifique
                'movement_date' => now(),
            ]);
            // NOUVEAU : Incrémenter la quantité de stock actuelle du produit
            $product = Product::find($item->product_id);
            if ($product) {
                $product->increment('current_stock_quantity', $item->quantity);
            }
        }
    }

    /**
     * Ajuste le stock lorsque les articles d'une commande sont modifiés.
     * @param Order $order L'instance de la commande
     * @param \Illuminate\Support\Collection $oldOrderItems Les anciens articles de commande (clé par product_id)
     * @param \Illuminate\Support\Collection $newOrderItems Les nouveaux articles de commande (clé par product_id)
     */
    protected function adjustStockForOrderItemsChange(Order $order, $oldOrderItems, $newOrderItems)
    {
        // Si la commande n'a pas un statut qui déduit le stock, ne rien faire
        if (!in_array($order->status, ['Validée', 'En préparation', 'En livraison', 'Livrée'])) {
            return;
        }

        // Produits retirés ou dont la quantité a diminué
        foreach ($oldOrderItems as $productId => $oldItem) {
            $newItem = $newOrderItems->get($productId);
            if (!$newItem) {
                // Produit complètement retiré de la commande
                $product = Product::find($productId);
                if ($product) {
                    $product->increment('current_stock_quantity', $oldItem->quantity);
                    Stock::create([
                        'product_id' => $productId,
                        'quantity' => $oldItem->quantity,
                        'movement_type' => 'entrée',
                        'reference_id' => 'MODIF_CMD_RETRAIT_' . $order->order_code,
                        'movement_date' => now(),
                    ]);
                }
            } elseif ($newItem['quantity'] < $oldItem->quantity) {
                // Quantité du produit diminuée
                $diffQuantity = $oldItem->quantity - $newItem['quantity'];
                $product = Product::find($productId);
                if ($product) {
                    $product->increment('current_stock_quantity', $diffQuantity);
                    Stock::create([
                        'product_id' => $productId,
                        'quantity' => $diffQuantity,
                        'movement_type' => 'entrée',
                        'reference_id' => 'MODIF_CMD_REDUCTION_' . $order->order_code,
                        'movement_date' => now(),
                    ]);
                }
            }
        }

        // Produits ajoutés ou dont la quantité a augmenté
        foreach ($newOrderItems as $productId => $newItem) {
            $oldItem = $oldOrderItems->get($productId);
            if (!$oldItem) {
                // Nouveau produit ajouté à la commande
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('current_stock_quantity', $newItem['quantity']);
                    Stock::create([
                        'product_id' => $productId,
                        'quantity' => $newItem['quantity'],
                        'movement_type' => 'sortie',
                        'reference_id' => 'MODIF_CMD_AJOUT_' . $order->order_code,
                        'movement_date' => now(),
                    ]);
                }
            } elseif ($newItem['quantity'] > $oldItem->quantity) {
                // Quantité du produit augmentée
                $diffQuantity = $newItem['quantity'] - $oldItem->quantity;
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('current_stock_quantity', $diffQuantity);
                    Stock::create([
                        'product_id' => $productId,
                        'quantity' => $diffQuantity,
                        'movement_type' => 'sortie',
                        'reference_id' => 'MODIF_CMD_AUGMENTATION_' . $order->order_code,
                        'movement_date' => now(),
                    ]);
                }
            }
        }
    }
}
