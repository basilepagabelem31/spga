<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Affiche le tableau de bord du client.
     */
    public function index()
    {
        $user = Auth::user();

        // Log de l'accès au tableau de bord
        $this->recordLog(
            'acces_tableau_de_bord_client',
            null,
            null,
            ['client_id' => $user->id],
            null
        );

        $totalOrders = Order::where('client_id', $user->id)->count();
        $pendingOrders = Order::where('client_id', $user->id)->whereIn('status', ['En attente de validation', 'Validée', 'En préparation', 'En livraison'])->count();
        $availableProducts = Product::available()->count();

        $recentOrders = Order::where('client_id', $user->id)->latest()->take(5)->get();

        return view('client.dashboard', compact('totalOrders', 'pendingOrders', 'availableProducts', 'recentOrders'));
    }

    /**
     * Affiche la liste de toutes les commandes du client avec des options de filtrage et de recherche.
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        // Log de l'accès à la liste des commandes
        $this->recordLog(
            'acces_liste_commandes_client',
            null,
            null,
            ['client_id' => $user->id, 'filters' => $request->all()],
            null
        );

        $query = Order::where('client_id', $user->id);

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where('order_code', 'like', $searchTerm);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->input('to_date'));
        }

        $orders = $query->latest()->paginate(8);

        $statuses = [
            'En attente de validation',
            'Validée',
            'En préparation',
            'En livraison',
            'Terminée',
            'Annulée',
        ];

        return view('client.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Affiche les détails d'une commande spécifique.
     */
    public function showOrder(Order $order)
    {
        if ($order->client_id !== Auth::id()) {
            // Log de l'échec de l'accès (autorisation)
            $this->recordLog(
                'echec_acces_commande',
                'orders',
                $order->id,
                ['error' => 'Accès non autorisé', 'client_id' => Auth::id()],
                null
            );
            abort(403, 'Accès non autorisé à cette commande.');
        }

        // Log de la consultation de la commande
        $this->recordLog(
            'consultation_commande',
            'orders',
            $order->id,
            null,
            null
        );

        $order->load('orderItems.product');
        
        return view('client.orders.show', compact('order'));
    }

    /**
     * Affiche le catalogue de produits.
     */
    public function products(Request $request)
    {
        $query = Product::available()->get()
            ->filter(function ($product) {
            return $product->current_stock_quantity > ($product->alert_threshold ?? 0);
        });

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where('name', 'like', $searchTerm);
        }

        $products = Product::where('status', 'disponible')
            ->whereColumn('current_stock_quantity', '>', 'alert_threshold')
            ->paginate(10);
        // Log de l'accès au catalogue
        $this->recordLog(
            'acces_catalogue_produits',
            'products',
            null,
            ['client_id' => Auth::id(), 'search_term' => $request->input('search')],
            null
        );

        return view('client.products.index', compact('products'));
    }

    /**
     * Traite et enregistre une nouvelle commande.
     * 
     * 
     * 
     */


    public function createOrder()
    {
        $products = Product::where('status', 'disponible')
            ->whereColumn('current_stock_quantity', '>', 'alert_threshold')
            ->get();        
        // Journalisation de l'accès au formulaire de création de commande
        $this->recordLog(
            'acces_formulaire_commande',
            null,
            null,
            ['client_id' => Auth::id()],
            null
        );

        return view('client.orders.create', compact('products'));
    }

    
    public function storeOrder(Request $request)
    {
        $validatedData = $request->validate([
            'payment_mode' => 'required|string|in:paiement_mobile,paiement_a_la_livraison,virement_bancaire',
            'delivery_mode' => 'required|string|in:standard_72h,express_6_12h',
            'notes' => 'nullable|string|max:1000',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
            'desired_delivery_date' => 'required|date',
            'delivery_location' => 'required|string|max:255',
            'geolocation' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $orderItemsData = [];

            $productQuantities = collect($validatedData['products'])->keyBy('id');
            $productsInOrder = Product::whereIn('id', $productQuantities->keys())->get();

            foreach ($productsInOrder as $product) {
                $requestedQuantity = (float) $productQuantities[$product->id]['quantity'];

                if ($product->min_order_quantity && $requestedQuantity < $product->min_order_quantity) {
                    DB::rollBack();
                    // Log de l'échec de la création (quantité minimale non respectée)
                    $this->recordLog(
                        'echec_creation_commande',
                        'orders',
                        null,
                        ['error' => 'Quantité minimale non atteinte', 'product_id' => $product->id, 'requested_quantity' => $requestedQuantity],
                        $validatedData
                    );
                    return redirect()->back()->with(
                        'error',
                        "La quantité demandée pour le produit '{$product->name}' est inférieure à la quantité minimale de commande requise ({$product->min_order_quantity} {$product->sale_unit})."
                    )->withInput();
                }

                if ($product->current_stock_quantity < $requestedQuantity) {
                    DB::rollBack();
                    // Log de l'échec de la création (stock insuffisant)
                    $this->recordLog(
                        'echec_creation_commande',
                        'orders',
                        null,
                        ['error' => 'Stock insuffisant', 'product_id' => $product->id, 'requested_quantity' => $requestedQuantity, 'available_stock' => $product->current_stock_quantity],
                        $validatedData
                    );
                    return redirect()->back()->with(
                        'error',
                        'Le stock pour le produit ' . $product->name .
                        ' est insuffisant. (Stock dispo : ' . $product->current_stock_quantity .
                        ', demandé : ' . $requestedQuantity . ')'
                    )->withInput();
                }
            }

            $order = Order::create([
                'client_id' => Auth::id(),
                'order_code' => 'ORD-' . Str::random(8),
                'order_date' => now(),
                'desired_delivery_date' => $validatedData['desired_delivery_date'] ?? null,
                'delivery_location' => $validatedData['delivery_location'] ?? null,
                'geolocation' => $validatedData['geolocation'] ?? null,
                'delivery_mode' => $validatedData['delivery_mode'],
                'payment_mode' => $validatedData['payment_mode'],
                'status' => 'En attente de validation',
                'total_amount' => 0,
                'notes' => $validatedData['notes'] ?? null,
            ]);

            foreach ($productsInOrder as $product) {
                $requestedQuantity = (float) $productQuantities[$product->id]['quantity'];
                $lineTotal = $product->unit_price * $requestedQuantity;
                $totalAmount += $lineTotal;

                $orderItemsData[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $requestedQuantity,
                    'sale_unit_at_order' => $product->sale_unit,
                    'unit_price_at_order' => $product->unit_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            OrderItem::insert($orderItemsData);
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Log de la création de la commande
            $this->recordLog(
                'creation_commande',
                'orders',
                $order->id,
                null,
                $order->toArray()
            );

            try {
                $order->load('orderItems.product', 'client');
                $this->stockService->notifySuppliers($order);
            } catch (\Throwable $e) {
                Log::error("Erreur notification fournisseurs (client) pour commande {$order->id} : " . $e->getMessage());
            }

            return redirect()->route('client.orders.show', $order)->with('success', 'Votre commande a été passée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log de l'échec de la création (erreur de transaction)
            $this->recordLog(
                'echec_creation_commande',
                'orders',
                null,
                ['error' => 'Erreur de base de données', 'exception' => $e->getMessage()],
                $validatedData
            );
            return redirect()->back()->with(
                'error',
                'Une erreur est survenue lors de la création de la commande. Veuillez réessayer. Détails : ' . $e->getMessage()
            )->withInput();
        }
    }

    /**
     * Annule une commande.
     */
    public function cancelOrder(Order $order)
    {
        if ($order->client_id !== Auth::id()) {
            // Log de l'échec de l'annulation (autorisation)
            $this->recordLog(
                'echec_annulation_commande',
                'orders',
                $order->id,
                ['error' => 'Accès non autorisé', 'client_id' => Auth::id()],
                null
            );
            abort(403, 'Accès non autorisé.');
        }

        if (!in_array($order->status, ['En attente de validation', 'Validée', 'En préparation'])) {
            // Log de l'échec de l'annulation (statut incorrect)
            $this->recordLog(
                'echec_annulation_commande',
                'orders',
                $order->id,
                ['error' => 'Commande ne peut plus être annulée', 'status' => $order->status],
                null
            );
            return redirect()->back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        $oldStatus = $order->status;
        $orderId = $order->id;

        try {
            DB::beginTransaction();

            $order->status = 'Annulée';
            $order->save();

            $this->stockService->replenishStockForOrder($order);

            DB::commit();

            // Log de l'annulation réussie
            $this->recordLog(
                'annulation_commande',
                'orders',
                $orderId,
                ['old_status' => $oldStatus],
                ['new_status' => $order->status]
            );

            return redirect()->route('client.orders')->with('success', 'Commande annulée avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log de l'échec de l'annulation (erreur de transaction)
            $this->recordLog(
                'echec_annulation_commande',
                'orders',
                $orderId,
                ['error' => 'Erreur de base de données', 'exception' => $e->getMessage()],
                null
            );
            return redirect()->back()->with('error', 'Erreur lors de l\'annulation : ' . $e->getMessage());
        }
    }

    /**
     * Retourne les détails d'un produit au format JSON.
     */
    public function showProductJson(Product $product)
    {
        $product->load('category');
        
        // Log de la consultation des détails d'un produit via API
        $this->recordLog(
            'consultation_details_produit_json',
            'products',
            $product->id,
            null,
            null
        );

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'unit_price' => $product->unit_price,
                'sale_unit' => $product->sale_unit,
                'image' => $product->image,
                'status' => $product->status,
                'min_order_quantity' => $product->min_order_quantity,
                'current_stock_quantity' => $product->current_stock_quantity,
                'category' => $product->category ? [
                    'name' => $product->category->name
                ] : null,
                'production_mode' => $product->production_mode,
                'packaging_format' => $product->packaging_format
            ]
        ]);
    }
}