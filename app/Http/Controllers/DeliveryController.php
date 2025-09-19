<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Order;
use App\Services\StockService ;
use App\Models\DeliveryRoute;
use Illuminate\Support\Facades\Log;
use App\Traits\LogsActivity; // Ajout de l'importation du trait

use App\Models\User;
use App\Notifications\DeliveryCompletedNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }
    
    /**
    * Affiche la liste des livraisons et passe les données pour le filtrage.
    */
    public function index(Request $request)
    {
        $orders = Order::whereDoesntHave('delivery')
                       ->whereIn('status', ['Validée', 'En préparation'])
                       ->get();
                    
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
            ->paginate(8)
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

        // Mise à jour du statut de la commande associée en "En livraison"
        try {
            $order = Order::find($request->order_id);
            if ($order) {
                $oldOrderValues = $order->toArray(); // Capture des anciennes valeurs de la commande
                $order->status = 'En livraison';
                $order->save();
                $newOrderValues = $order->toArray(); // Capture des nouvelles valeurs de la commande
                
                // Log de la mise à jour du statut de la commande
                $this->recordLog(
                    'mise_a_jour_statut_commande',
                    'orders',
                    $order->id,
                    $oldOrderValues,
                    $newOrderValues
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut de la commande dans DeliveryController@store: ' . $e->getMessage());
        }
        
        // Log de la création de la livraison
        $this->recordLog(
            'creation_livraison',
            'deliveries',
            $delivery->id,
            null,
            $delivery->toArray()
        );

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
        $oldValues = $delivery->toArray(); // Capture des valeurs de la livraison avant la mise à jour
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

        $newStatus = $request->status;

        $delivery->update($request->all());
        $newValues = $delivery->refresh()->toArray(); // Capture des nouvelles valeurs de la livraison

        // Log de la mise à jour de la livraison
        $this->recordLog(
            'mise_a_jour_livraison',
            'deliveries',
            $delivery->id,
            $oldValues,
            $newValues
        );
        
        // --- Synchronisation de la commande (uniquement statut logique, stock géré ailleurs) ---
        $order = $delivery->order;
        if ($order) {
            $oldOrderValues = $order->toArray(); // Capture des anciennes valeurs de la commande
            switch ($newStatus) {
                case 'Terminée':
                    $order->status = 'Terminée';
                    break;
                case 'Annulée':
                    $order->status = 'Annulée';
                    break;
                case 'En cours':
                    $order->status = 'En livraison';
                    break;
            }
            $order->save();
            $newOrderValues = $order->toArray(); // Capture des nouvelles valeurs de la commande
            
            // Log de la mise à jour du statut de la commande
            $this->recordLog(
                'mise_a_jour_statut_commande',
                'orders',
                $order->id,
                $oldOrderValues,
                $newOrderValues
            );
        }

        // Si la livraison passe en Terminée
        if ($oldStatus !== 'Terminée' && $newStatus === 'Terminée') {
            $order = $delivery->order;

            // 1) Notifier le client
            $client = $order->user ?? null;
            if ($client) {
                $client->notify(new DeliveryCompletedNotification($delivery));
            }

            // 2) Déduire le stock
            if ($order) {
                $this->stockService->deductStockForOrder($order);

                // 3) Notifier les fournisseurs
                $order->load('orderItems.product.partner.user', 'client');
                $this->stockService->notifySuppliers($order);
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
        $oldValues = $delivery->toArray(); // Capture des valeurs de la livraison avant la suppression
        $deliveryId = $delivery->id;
        
        // Récupérer l'ID de la commande associée pour le log
        $orderId = $delivery->order_id;
        $order = Order::find($orderId);
        
        $delivery->delete();
        
        // Optionnel : remet la commande dans un état "Validée" ou "En préparation"
        // si la livraison est annulée ou supprimée avant d'être terminée.
        if ($order && ($order->status === 'En livraison' || $order->status === 'En préparation')) {
             $oldOrderValues = $order->toArray(); // Capture des anciennes valeurs
             $order->status = 'Validée';
             $order->save();
             $newOrderValues = $order->toArray(); // Capture des nouvelles valeurs
             
             // Log de la mise à jour du statut de la commande
             $this->recordLog(
                 'mise_a_jour_statut_commande',
                 'orders',
                 $order->id,
                 $oldOrderValues,
                 $newOrderValues
             );
        }

        // Log de la suppression de la livraison
        $this->recordLog(
            'suppression_livraison',
            'deliveries',
            $deliveryId,
            $oldValues,
            null
        );

        return redirect()->route('deliveries.index')
                         ->with('success', 'Livraison supprimée avec succès.');
    }
}