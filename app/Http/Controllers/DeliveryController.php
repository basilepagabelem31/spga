<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Models\DeliveryRoute;
use App\Models\User;
use App\Notifications\DeliveryCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
   /**
 * Affiche la liste des livraisons et passe les données pour le filtrage.
 */
public function index(Request $request)
{
    // Récupère uniquement les commandes qui ne sont pas encore associées à une livraison
    // et dont le statut n'est pas "En attente de validation".
    $orders = Order::whereDoesntHave('delivery')
                   ->where('status', '!=', 'En attente de validation')
                   ->get(); 
                   
    // Chargement de la relation 'driver' pour chaque tournée de livraison
    $deliveryRoutes = DeliveryRoute::where('status', '!=', 'Terminée')->with('driver')->get();

    $deliveries = Delivery::with(['order', 'deliveryRoute.driver'])
        ->when($request->filled('order_id'), function ($query) use ($request) {
            $query->where('order_id', $request->order_id);
        })
        ->when($request->filled('delivery_route_id'), function ($query) use ($request) {
            $query->where('delivery_route_id', $request->delivery_route_id);
        })
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->when($request->filled('delivered_at'), function ($query) use ($request) {
            $query->whereDate('delivered_at', $request->delivered_at);
        })
        ->orderBy('delivered_at', 'desc')
        ->paginate(10)
        ->withQueryString();

    return view('deliveries.index', compact('deliveries', 'orders', 'deliveryRoutes'));
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

        $delivery = Delivery::create($request->all());

        // Mise à jour du statut de la commande associée en "En Livraison"
        try {
            $order = Order::find($request->order_id);
            if ($order) {
                $order->status = 'En Livraison';
                $order->save();
            }
        } catch (\Exception $e) {
            // Vous pouvez ajouter une gestion d'erreur ici si nécessaire
        }

        return redirect()->route('deliveries.index')
                         ->with('success', 'Livraison créée avec succès et statut de la commande mis à jour.');
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
 * Met à jour une livraison existante dans la base de données.
 */
public function update(Request $request, Delivery $delivery)
{
    $oldStatus = $delivery->status;

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

    // --- Synchronisation de la commande ---
    $order = $delivery->order;
    if ($order && $oldStatus !== $delivery->status) {
        switch ($delivery->status) {
            case 'Terminée':
                $order->status = 'Terminée';
                break;
            case 'Annulée':
                $order->status = 'Annulée';
                break;
            case 'En cours':
                $order->status = 'En Livraison';
                break;
        }
        $order->save();
    }

    // Notification si nécessaire
    if ($oldStatus !== 'Terminée' && $delivery->status === 'Terminée') {
        $client = $delivery->order->user;
        if ($client) {
            $client->notify(new DeliveryCompletedNotification($delivery));
        }
    }

    // Mise à jour du statut de la tournée
    $deliveryRoute = DeliveryRoute::find($delivery->delivery_route_id);
    if ($deliveryRoute) {
        if ($deliveryRoute->status === 'Planifiée' && $delivery->status === 'En cours') {
            $deliveryRoute->update(['status' => 'En cours']);
        }

        $remainingDeliveriesCount = Delivery::where('delivery_route_id', $deliveryRoute->id)
            ->whereNotIn('status', ['Terminée', 'Annulée'])
            ->count();

        if ($remainingDeliveriesCount === 0 && $deliveryRoute->status !== 'Terminée') {
            $deliveryRoute->update(['status' => 'Terminée']);
        }
    }

    return redirect()->route('deliveries.index')->with('success', 'Livraison mise à jour avec succès.');
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