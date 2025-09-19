<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderItemController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Affiche la liste des articles de commande.
     * Peut être filtré par commande.
     */
    public function index(Request $request)
    {
        $query = OrderItem::with(['order', 'product']);

        if ($request->has('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        $orderItems = $query->paginate(8);
        return view('order_items.index', compact('orderItems'));
    }

    /**
     * Affiche le formulaire de création d'un nouvel article de commande.
     */
    public function create()
    {
        $orders = Order::all();
        $products = Product::all();
        return view('order_items.create', compact('orders', 'products'));
    }

    /**
     * Stocke un nouvel article de commande dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $order = Order::findOrFail($request->order_id);
        $product = Product::findOrFail($request->product_id);

        try {
            DB::beginTransaction();
            $orderItem = OrderItem::create([
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'sale_unit_at_order' => $product->sale_unit,
                'unit_price_at_order' => $product->unit_price,
            ]);

            // Mise à jour du total de la commande
            $oldOrderValues = $order->toArray();
            $order->update(['total_amount' => $order->getTotalAmount()]);
            $newOrderValues = $order->toArray();

            // Log de la création de l'article de commande
            $this->recordLog(
                'creation_article_commande',
                'order_items',
                $orderItem->id,
                null,
                $orderItem->toArray()
            );

            // Log de la mise à jour du montant total de la commande
            $this->recordLog(
                'mise_a_jour_montant_total_commande',
                'orders',
                $order->id,
                $oldOrderValues,
                $newOrderValues
            );

            // Envoyer la notification au fournisseur si le statut le justifie
            if (in_array($order->status, ['Validée', 'En préparation'])) {
                $this->stockService->notifySuppliers($order);
            }
            
            DB::commit();
            return redirect()->route('order_items.index')
                             ->with('success', 'Article de commande créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création d\'un article de commande: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la création de l\'article.');
        }
    }

    /**
     * Affiche les détails d'un article de commande spécifique.
     */
    public function show(OrderItem $orderItem)
    {
        $orderItem->load('order', 'product');
        return view('order_items.show', compact('orderItem'));
    }

    /**
     * Affiche le formulaire d'édition d'un article de commande.
     */
    public function edit(OrderItem $orderItem)
    {
        $orders = Order::all();
        $products = Product::all();
        return view('order_items.edit', compact('orderItem', 'orders', 'products'));
    }

    /**
     * Met à jour un article de commande existant.
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        $oldValues = $orderItem->toArray(); // Capture des valeurs avant la mise à jour

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $order = Order::findOrFail($orderItem->order_id);
        $product = Product::findOrFail($request->product_id);

        try {
            DB::beginTransaction();

            $orderItem->update([
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'sale_unit_at_order' => $product->sale_unit,
                'unit_price_at_order' => $product->unit_price,
            ]);
            $newValues = $orderItem->refresh()->toArray(); // Capture des nouvelles valeurs
            
            // Mise à jour du total de la commande
            $oldOrderValues = $order->toArray();
            $order->update(['total_amount' => $order->getTotalAmount()]);
            $newOrderValues = $order->toArray();

            // Log de la mise à jour de l'article de commande
            $this->recordLog(
                'mise_a_jour_article_commande',
                'order_items',
                $orderItem->id,
                $oldValues,
                $newValues
            );

            // Log de la mise à jour du montant total de la commande
            $this->recordLog(
                'mise_a_jour_montant_total_commande',
                'orders',
                $order->id,
                $oldOrderValues,
                $newOrderValues
            );

            DB::commit();
            return redirect()->route('order_items.index')
                             ->with('success', 'Article de commande mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour d\'un article de commande: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour de l\'article.');
        }
    }

    /**
     * Supprime un article de commande.
     */
    public function destroy(OrderItem $orderItem)
    {
        $oldValues = $orderItem->toArray(); // Capture des valeurs avant la suppression
        $orderItem_id = $orderItem->id;

        $order = Order::findOrFail($orderItem->order_id);

        try {
            DB::beginTransaction();
            
            $orderItem->delete();
            
            // Mise à jour du total de la commande
            $oldOrderValues = $order->toArray();
            $order->update(['total_amount' => $order->getTotalAmount()]);
            $newOrderValues = $order->refresh()->toArray();

            // Log de la suppression de l'article de commande
            $this->recordLog(
                'suppression_article_commande',
                'order_items',
                $orderItem_id,
                $oldValues,
                null
            );

            // Log de la mise à jour du montant total de la commande
            $this->recordLog(
                'mise_a_jour_montant_total_commande',
                'orders',
                $order->id,
                $oldOrderValues,
                $newOrderValues
            );

            DB::commit();
            return redirect()->route('order_items.index')
                             ->with('success', 'Article de commande supprimé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la suppression d\'un article de commande: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la suppression de l\'article.');
        }
    }
}