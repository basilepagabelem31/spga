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
            if ($product && $product->current_stock_quantity < $item['quantity']) {
                $errors[] = "La quantité demandée pour le produit '{$product->name}' ({$item['quantity']} {$product->sale_unit}) est supérieure au stock disponible ({$product->current_stock_quantity} {$product->sale_unit}).";
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
            
            // SUPPRIMER la déduction de stock ici - elle ne se fera que lorsque le statut sera "Terminée"
            // La vérification de stock reste pour s'assurer qu'il y a assez de stock au moment de la commande

            if (in_array($order->status, ['Validée', 'En préparation'])) {
                $this->stockService->notifySuppliers($order);
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
        $oldOrderItems = $order->orderItems->keyBy('product_id');

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
        
        $newStatus = $request->status;
        $newOrderItems = collect($request->products)->keyBy('id');

        try {
            DB::beginTransaction();

            // Gérer la déduction/remise en stock en fonction du changement de statut
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'Terminée') {
                    // Déduction SEULEMENT si le statut passe à "Terminée"
                    $this->stockService->deductStockForOrder($order);
                } elseif ($oldStatus === 'Terminée') {
                    // Remise en stock si on quitte l'état "Terminée"
                    $this->stockService->replenishStockForOrder($order);
                }
            }

            // SUPPRIMER la gestion de l'ajustement du stock pour les modifications d'articles
            // Le stock n'est ajusté que lors des changements de statut vers/depuis "Terminée"
            // if ($oldStatus === 'Terminée' && $newStatus === 'Terminée' && $oldOrderItems->count() > 0) {
            //     $this->stockService->adjustStockForOrderItemsChange($order, $oldOrderItems, $newOrderItems);
            // }

            // Mettre à jour les OrderItems après la gestion du stock
            $order->orderItems()->delete();
            $totalAmount = 0;
            foreach ($request->products as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $order->addItem($product, $item['quantity']);
                    $totalAmount += $product->unit_price * $item['quantity'];
                }
            }
            $order->update(['total_amount' => $totalAmount, 'status' => $newStatus, 'notes' => $request->notes, 'validated_by' => $request->validated_by]);
            
            // Envoyer la notification au fournisseur si le statut passe à 'Validée' ou 'En préparation'
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