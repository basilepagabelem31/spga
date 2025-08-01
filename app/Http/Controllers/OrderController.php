<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User; // Pour client_id et validated_by
use App\Models\Product; // Pour l'ajout d'articles de commande
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Affiche la liste des commandes.
     */
    public function index()
    {
        $orders = Order::with(['client', 'validatedBy'])->paginate(10);
        return view('orders.index', compact('orders'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle commande.
     */
    public function create()
    {
        $clients = User::whereHas('role', function ($query) {
            $query->where('name', 'client'); // Filtrer pour n'afficher que les utilisateurs ayant le rôle 'client'
        })->get();
        $products = Product::available()->get(); // Seuls les produits disponibles
        $validators = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_commercial']); // Ex: rôles pouvant valider
        })->get();

        return view('orders.create', compact('clients', 'products', 'validators'));
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
            'status' => ['required', Rule::in(['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Livrée', 'Annulée'])],
            'notes' => 'nullable|string',
            'validated_by' => 'nullable|exists:users,id',
            'products' => 'required|array', // Attendu un tableau de produits avec quantité
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $order_code = 'CMD-' . strtoupper(Str::random(8)); // Générer un code de commande unique

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
            'total_amount' => 0, // Sera mis à jour après l'ajout des articles
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

        return view('orders.edit', compact('order', 'clients', 'products', 'validators'));
    }

    /**
     * Met à jour une commande existante dans la base de données.
     */
    public function update(Request $request, Order $order)
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
            'products' => 'required|array', // Attendu un tableau de produits avec quantité
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:0.01',
        ]);

        $orderData = $request->except('products');
        $order->update($orderData);

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
        $order->delete(); // Les OrderItems et Deliveries liés seront supprimés en cascade grâce aux relations

        return redirect()->route('orders.index')
                         ->with('success', 'Commande supprimée avec succès.');
    }
}