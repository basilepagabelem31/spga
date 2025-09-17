<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Services\StockService;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    /**
     * Affiche le tableau de bord du client.
     * 
     * 
     * 
     */


 protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }


   public function index()
    {
        $user = Auth::user();

        $totalOrders = Order::where('client_id', $user->id)->count();
        // Ligne corrigée pour compter correctement les commandes en attente
        $pendingOrders = Order::where('client_id', $user->id)->whereIn('status', ['En attente de validation', 'Validée', 'En préparation', 'En livraison'])->count();
        $availableProducts = Product::available()->count();

        $recentOrders = Order::where('client_id', $user->id)->latest()->take(5)->get();

        return view('client.dashboard', compact('totalOrders', 'pendingOrders', 'availableProducts', 'recentOrders'));
    }

   /**
     * Affiche la liste de toutes les commandes du client avec des options de filtrage et de recherche.
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function orders(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('client_id', $user->id);

        // Ajout de la recherche par numéro de commande
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where('order_code', 'like', $searchTerm);
        }

        // Ajout du filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        // Ajout du filtre par date
        if ($request->filled('from_date')) {
            $query->whereDate('order_date', '>=', $request->input('from_date'));
        }
        if ($request->filled('to_date')) {
            $query->whereDate('order_date', '<=', $request->input('to_date'));
        }


        $orders = $query->latest()->paginate(10);

        // Liste des statuts pour la dropdown du filtre
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
            abort(403, 'Accès non autorisé à cette commande.');
        }
        $order->load('orderItems.product');
        
        return view('client.orders.show', compact('order'));
    }

    /**
     * Affiche le catalogue de produits.
     */
       public function products(Request $request)
    {
        $query = Product::available();

        // Ajout de la recherche par nom de produit
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->input('search') . '%';
            $query->where('name', 'like', $searchTerm);
        }

        $products = $query->paginate(12);

        return view('client.products.index', compact('products'));
    }







    /**
     * Affiche le formulaire de création d'une nouvelle commande.
     */
    public function createOrder()
    {
        $products = Product::available()->get();

        return view('client.orders.create', compact('products'));
    }

    /**
     * Traite et enregistre une nouvelle commande.
     */
    /**
 * Traite et enregistre une nouvelle commande.
 */
/**
 * Traite et enregistre une nouvelle commande.
 */
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

        // Vérification du stock et de la quantité minimale
        $productQuantities = collect($validatedData['products'])->keyBy('id');
        $productsInOrder = Product::whereIn('id', $productQuantities->keys())->get();

        foreach ($productsInOrder as $product) {
            $requestedQuantity = (float) $productQuantities[$product->id]['quantity'];

            // AJOUT de la vérification de la quantité minimale de commande
            if ($product->min_order_quantity && $requestedQuantity < $product->min_order_quantity) {
                DB::rollBack();
                return redirect()->back()->with(
                    'error',
                    "La quantité demandée pour le produit '{$product->name}' est inférieure à la quantité minimale de commande requise ({$product->min_order_quantity} {$product->sale_unit})."
                )->withInput();
            }

            // Vérification de stock existante
            if ($product->current_stock_quantity < $requestedQuantity) {
                DB::rollBack();
                return redirect()->back()->with(
                    'error',
                    'Le stock pour le produit ' . $product->name .
                    ' est insuffisant. (Stock dispo : ' . $product->current_stock_quantity .
                    ', demandé : ' . $requestedQuantity . ')'
                )->withInput();
            }
        }

        // Création de la commande
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

        // Traitement des articles
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

        // Insertion des order items et mise à jour du total
        OrderItem::insert($orderItemsData);
        $order->update(['total_amount' => $totalAmount]);

        DB::commit();

        try {
            $order->load('orderItems.product', 'client');
            $this->stockService->notifySuppliers($order);
        } catch (\Throwable $e) {
            Log::error("Erreur notification fournisseurs (client) pour commande {$order->id} : " . $e->getMessage());
        }

        return redirect()->route('client.orders.show', $order)->with('success', 'Votre commande a été passée avec succès !');

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(
            'error',
            'Une erreur est survenue lors de la création de la commande. Veuillez réessayer. Détails : ' . $e->getMessage()
        )->withInput();
    }
}
    /**
    * Affiche les détails d'un produit spécifique.
    */
    // public function showProduct(Product $product)
    // {
    //     // Charger la catégorie et le stock pour la vue de détails
    //     $product->load('category', 'stocks');

    //     return view('client.products.show', compact('product'));
    // }




    public function cancelOrder(Order $order)
{
    if ($order->client_id !== Auth::id()) {
        abort(403, 'Accès non autorisé.');
    }

    // Seules certaines commandes peuvent être annulées
    if (!in_array($order->status, ['En attente de validation', 'Validée', 'En préparation'])) {
        return redirect()->back()->with('error', 'Cette commande ne peut plus être annulée.');
    }

    try {
        DB::beginTransaction();

        $order->status = 'Annulée';
        $order->save();

        // Réinjecter le stock
        $this->stockService->replenishStockForOrder($order);

        DB::commit();
        return redirect()->route('client.orders')->with('success', 'Commande annulée avec succès.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Erreur lors de l\'annulation : '.$e->getMessage());
    }
}




    /**
     * Retourne les détails d'un produit au format JSON.
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function showProductJson(Product $product)
{
    $product->load('category');
    
    return response()->json([
        'product' => [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'unit_price' => $product->unit_price, // Gardez le format numérique
            'sale_unit' => $product->sale_unit,
            'image' => $product->image,
            'status' => $product->status,
            'min_order_quantity'=>$product->min_order_quantity,
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