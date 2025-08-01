<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
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

        $orderItems = $query->paginate(10);
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
            // sale_unit_at_order et unit_price_at_order peuvent être remplis automatiquement
        ]);

        $product = Product::findOrFail($request->product_id);

        OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'sale_unit_at_order' => $product->sale_unit,
            'unit_price_at_order' => $product->unit_price,
        ]);

        // Mettre à jour le montant total de la commande
        $order = Order::find($request->order_id);
        if ($order) {
            $order->update(['total_amount' => $order->getTotalAmount()]);
        }


        return redirect()->route('order_items.index')
                         ->with('success', 'Article de commande créé avec succès.');
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
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $product = Product::findOrFail($request->product_id);

        $orderItem->update([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'sale_unit_at_order' => $product->sale_unit,
            'unit_price_at_order' => $product->unit_price,
        ]);

        // Mettre à jour le montant total de la commande
        $order = Order::find($orderItem->order_id);
        if ($order) {
            $order->update(['total_amount' => $order->getTotalAmount()]);
        }

        return redirect()->route('order_items.index')
                         ->with('success', 'Article de commande mis à jour avec succès.');
    }

    /**
     * Supprime un article de commande.
     */
    public function destroy(OrderItem $orderItem)
    {
        $orderId = $orderItem->order_id;
        $orderItem->delete();

        // Mettre à jour le montant total de la commande après suppression d'un article
        $order = Order::find($orderId);
        if ($order) {
            $order->update(['total_amount' => $order->getTotalAmount()]);
        }

        return redirect()->route('order_items.index')
                         ->with('success', 'Article de commande supprimé avec succès.');
    }
}