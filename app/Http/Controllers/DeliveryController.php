<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\DeliveryRoute;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryController extends Controller
{
    /**
     * Affiche la liste des livraisons.
     */
    public function index()
    {
        $deliveries = Delivery::with(['order', 'deliveryRoute'])->paginate(10);
        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle livraison.
     */
    public function create()
    {
        $orders = Order::all(); // Vous pouvez filtrer les commandes non livrées
        $deliveryRoutes = DeliveryRoute::all(); // Vous pouvez filtrer les tournées non terminées
        return view('deliveries.create', compact('orders', 'deliveryRoutes'));
    }

    /**
     * Stocke une nouvelle livraison dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_route_id' => 'required|exists:delivery_routes,id',
            'status' => ['required', Rule::in(['En cours', 'Terminée', 'Annulée'])],
            'delivery_proof_type' => ['nullable', Rule::in(['bouton_confirmation', 'signature_numerique', 'photo', 'bordereau_signe'])],
            'delivery_proof_data' => 'nullable|string',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_signature' => 'nullable|string',
            'delivery_person_signature' => 'nullable|string',
            'delivered_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        Delivery::create($request->all());

        return redirect()->route('deliveries.index')
                         ->with('success', 'Livraison créée avec succès.');
    }

    /**
     * Affiche les détails d'une livraison spécifique.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load('order', 'deliveryRoute');
        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Affiche le formulaire d'édition d'une livraison.
     */
    public function edit(Delivery $delivery)
    {
        $orders = Order::all();
        $deliveryRoutes = DeliveryRoute::all();
        return view('deliveries.edit', compact('delivery', 'orders', 'deliveryRoutes'));
    }

    /**
     * Met à jour une livraison existante dans la base de données.
     */
    public function update(Request $request, Delivery $delivery)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'delivery_route_id' => 'required|exists:delivery_routes,id',
            'status' => ['required', Rule::in(['En cours', 'Terminée', 'Annulée'])],
            'delivery_proof_type' => ['nullable', Rule::in(['bouton_confirmation', 'signature_numerique', 'photo', 'bordereau_signe'])],
            'delivery_proof_data' => 'nullable|string',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_signature' => 'nullable|string',
            'delivery_person_signature' => 'nullable|string',
            'delivered_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $delivery->update($request->all());

        return redirect()->route('deliveries.index')
                         ->with('success', 'Livraison mise à jour avec succès.');
    }

    /**
     * Supprime une livraison de la base de données.
     */
    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return redirect()->route('deliveries.index')
                         ->with('success', 'Livraison supprimée avec succès.');
    }
}