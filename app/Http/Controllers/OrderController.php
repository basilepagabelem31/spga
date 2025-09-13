<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Services\StockService;
use App\Mail\NewOrderSupplierNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Affiche la liste des commandes avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Order::with(['client', 'validatedBy']);

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_code_search')) {
            $query->where('order_code', 'like', '%' . $request->order_code_search . '%');
        }

        if ($request->filled('order_date_from')) {
            $query->whereDate('order_date', '>=', $request->order_date_from);
        }
        if ($request->filled('order_date_to')) {
            $query->whereDate('order_date', '<=', $request->order_date_to);
        }

        $orders = $query->paginate(10)->withQueryString();

        $clients = User::whereHas('role', function ($query) {
            $query->where('name', 'client');
        })->get();

        $statuses = ['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Terminée', 'Annulée'];

        $products = Product::available()->get(); 
        
        $validators = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_commercial']);
        })->get();

        return view('orders.index', compact('orders', 'clients', 'statuses', 'products', 'validators'));
    }

    /**
     * Stocke une nouvelle commande dans la base de données.
     */
    /**
 * Stocke une nouvelle commande dans la base de données.
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
        'status' => ['required', Rule::in(['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Terminée', 'Annulée'])],
        'notes' => 'nullable|string',
        'validated_by' => 'nullable|exists:users,id',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:0.01',
    ]);

    $errors = [];
    foreach ($request->products as $item) {
        $product = Product::find($item['id']);
        if ($product) {
            // AJOUT de la vérification de la quantité minimale de commande
            if ($product->min_order_quantity && $item['quantity'] < $product->min_order_quantity) {
                $errors[] = "La quantité demandée pour le produit '{$product->name}' ({$item['quantity']} {$product->sale_unit}) est inférieure à la quantité minimale de commande requise ({$product->min_order_quantity} {$product->sale_unit}).";
            }

            // Vérification de stock existante
            if ($product->current_stock_quantity < $item['quantity']) {
                $errors[] = "La quantité demandée pour le produit '{$product->name}' ({$item['quantity']} {$product->sale_unit}) est supérieure au stock disponible ({$product->current_stock_quantity} {$product->sale_unit}).";
            }
        }
    }

    if (!empty($errors)) {
        return redirect()->back()->withInput()->withErrors($errors);
    }





    try {
        DB::beginTransaction();
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
            $this->stockService->notifySuppliers($order);
        }

        if ($order->status === 'Terminée') {
            $order->load('orderItems.product');
            $this->stockService->deductStockForOrder($order);
        }

        DB::commit();
        return redirect()->route('orders.index')->with('success', 'Commande créée avec succès.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de la création de la commande: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la commande.');
    }
}
    /**
     * Met à jour une commande existante dans la base de données.
     */
   public function update(Request $request, Order $order)
{
    $oldStatus = $order->status;
    $newStatus = $request->status;

    Log::info("Mise à jour commande {$order->id}: {$oldStatus} -> {$newStatus}");


        // 3. Gérer la déduction/remise du stock APRÈS mise à jour des items


    $request->validate([
        'client_id' => 'required|exists:users,id',
        'desired_delivery_date' => 'nullable|string|max:255',
        'delivery_location' => 'nullable|string',
        'geolocation' => 'nullable|string|max:255',
        'delivery_mode' => ['required', Rule::in(['standard_72h', 'express_6_12h'])],
        'payment_mode' => ['required', Rule::in(['paiement_mobile', 'paiement_a_la_livraison', 'virement_bancaire'])],
        'status' => ['required', Rule::in(['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Terminée', 'Annulée'])],
        'notes' => 'nullable|string',
        'validated_by' => 'nullable|exists:users,id',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|exists:products,id',
        'products.*.quantity' => 'required|numeric|min:0.01',
    ]);
    

    try {
        DB::beginTransaction();

        // 1. Supprimer et recréer les OrderItems
        $order->orderItems()->delete();
        $totalAmount = 0;
        foreach ($request->products as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $order->addItem($product, $item['quantity']);
                $totalAmount += $product->unit_price * $item['quantity'];
            }
        }

        // 2. Mettre à jour la commande (hors gestion du stock)
        $order->update([
            'total_amount' => $totalAmount,
            'status' => $newStatus,
            'notes' => $request->notes,
            'validated_by' => $request->validated_by,
        ]);

    
        $order->load('orderItems.product');
        Log::info("Order items count: " . $order->orderItems->count());

        if ($oldStatus !== $newStatus) {
    // Si la commande passe en Terminée => déduction du stock
            if ($newStatus === 'Terminée') {
                Log::info("Commande {$order->id} est maintenant Terminée, déduction du stock...");
                Log::info(">>> ITEMS pour déduction: " . json_encode($order->orderItems->toArray()));
                $this->stockService->deductStockForOrder($order);
            } 
    // Si elle repasse de Terminée vers autre chose => on remet le stock
            elseif ($oldStatus === 'Terminée' && $newStatus !== 'Terminée') {
                Log::info("Commande {$order->id} n'est plus Terminée, remise du stock...");
                $this->stockService->replenishStockForOrder($order);
            }
        }


        // 4. Envoyer notification fournisseur si besoin
        if (in_array($newStatus, ['Validée', 'En préparation']) && !in_array($oldStatus, ['Validée', 'En préparation'])) {
            $this->stockService->notifySuppliers($order);
        }

        DB::commit();
        return redirect()->route('orders.index')->with('success', 'Commande mise à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur lors de la mise à jour de la commande: ' . $e->getMessage());
        return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de la commande.');
    }
}


    /**
     * Supprime une commande de la base de données.
     */
    public function destroy(Order $order)
    {
        try {
            DB::beginTransaction();
            // Remettre le stock si la commande était terminée avant suppression
            if (in_array($order->status, ['Terminée'])) {
                $this->stockService->replenishStockForOrder($order);
            }

            $order->delete();
            DB::commit();

            return redirect()->route('orders.index')->with('success', 'Commande supprimée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression de la commande.');
        }
    }
}