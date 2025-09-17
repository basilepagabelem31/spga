<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryRoute;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChauffeurController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Affiche le tableau de bord du chauffeur.
     */
public function index()
{
    $driverId = Auth::id();
    $today = Carbon::today();

    // Récupérer toutes les livraisons du chauffeur pour aujourd'hui
    $deliveries = Delivery::whereHas('deliveryRoute', function ($q) use ($driverId, $today) {
            $q->where('driver_id', $driverId)
              ->whereDate('delivery_date', $today);
        })
        ->with('order.client')
        ->get();

    $totalDeliveries = $deliveries->count();
    $completedDeliveries = $deliveries->where('status', 'Terminée')->count();
    $pendingDeliveries = $deliveries->whereNotIn('status', ['Terminée', 'Annulée'])->count();

    // Prochaine livraison (triée par heure planifiée si dispo)
    $nextDelivery = $deliveries->whereNotIn('status', ['Terminée', 'Annulée'])
                               ->sortBy('planned_delivery_time')
                               ->first();

    return view('chauffeur.dashboard', compact(
        'deliveries',
        'totalDeliveries',
        'completedDeliveries',
        'pendingDeliveries',
        'nextDelivery'
    ));
}



    /* Affiche la liste complète des livraisons du chauffeur avec filtres.
     */
    public function deliveries(Request $request)
    {
        $driverId = Auth::id();
        $query = Delivery::query();

        // Récupérer les livraisons de l'utilisateur connecté
        $query->whereHas('deliveryRoute', function ($q) use ($driverId) {
            $q->where('driver_id', $driverId);
        })
        ->with('order.client'); // 👈 L'ajout clé est ici pour charger les relations

        // 🟢 NOUVEAU: Filtrage par statut si 'status' est présent
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // 🟢 NOUVEAU: Filtrage par date si 'date' est présent
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->input('date'));
        }

        // Ajout de la pagination et du tri par date de création (les plus récentes en premier)
        $deliveries = $query->orderBy('created_at', 'desc')
                           ->paginate(10)
                           ->withQueryString();

        return view('chauffeur.deliveries', compact('deliveries'));
    }

    public function completeDelivery(Delivery $delivery)
    {
        if ($delivery->status === 'Terminée') {
            return redirect()->back()->with('info', 'Cette livraison est déjà terminée. Aucune action n\'est requise.');
        }

        if ($delivery->deliveryRoute->driver_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette livraison.');
        }

        try {
            DB::beginTransaction();

            // Le modèle se chargera de déduire le stock automatiquement
            $delivery->status = 'Terminée';
            $delivery->delivered_at = Carbon::now();
            $delivery->save();

            // Mise à jour de la commande (statut uniquement)
            $order = $delivery->order;
            if ($order && $order->status !== 'Terminée') {
                $order->status = 'Terminée';
                $order->save();

                    // Déduire le stock + notifier
                $order->load('orderItems.product', 'client');
                $this->stockService->deductStockForOrder($order);
                $this->stockService->notifySuppliers($order);
            }

            // Mise à jour de la tournée
            $deliveryRoute = $delivery->deliveryRoute;
            if ($deliveryRoute) {
                if ($deliveryRoute->status === 'Planifiée') {
                    $deliveryRoute->update(['status' => 'En cours']);
                }

                $remaining = $deliveryRoute->deliveries()->whereNotIn('status', ['Terminée', 'Annulée'])->count();
                if ($remaining === 0) {
                    $deliveryRoute->update(['status' => 'Terminée']);
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Livraison marquée comme terminée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du statut de livraison: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de la livraison.');
        }
    }

    public function planning()
    {
        $driverId = Auth::id();
        
        $deliveryRoutes = DeliveryRoute::where('driver_id', $driverId)
                                     ->orderBy('delivery_date', 'asc')
                                     ->with('deliveries')
                                     ->get();

        return view('chauffeur.planning', compact('deliveryRoutes'));
    }
}