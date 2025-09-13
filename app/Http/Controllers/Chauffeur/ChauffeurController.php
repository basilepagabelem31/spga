<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryRoute;
use App\Services\StockService; // 1. AJOUTER l'import du StockService
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // 2. AJOUTER l'import de DB
use Illuminate\Support\Facades\Log; // 3. AJOUTER l'import de Log


class ChauffeurController extends Controller
{
    protected $stockService; // 4. AJOUTER la propriété du service

    // 5. MODIFIER le constructeur pour injecter StockService
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

        // Trouver la tournée du chauffeur pour la date du jour
        $deliveryRoute = DeliveryRoute::where('driver_id', $driverId)
                                     ->whereDate('delivery_date', $today)
                                     ->first();

        // Initialisation des variables
        $deliveries = collect();
        $totalDeliveries = 0;
        $completedDeliveries = 0;
        $pendingDeliveries = 0;
        $nextDelivery = null;

        if ($deliveryRoute) {
            // Récupérer toutes les livraisons de cette tournée
            $deliveries = $deliveryRoute->deliveries;
            
            // Calcul des indicateurs
            $totalDeliveries = $deliveries->count();
            $completedDeliveries = $deliveries->where('status', 'Terminée')->count();
            // Les livraisons en attente sont toutes les livraisons qui ne sont pas terminées ou annulées
            $pendingDeliveries = $deliveries->whereNotIn('status', ['Terminée', 'Annulée'])->count();
            
            // Trouver la prochaine livraison à effectuer, triée par heure planifiée
            $nextDelivery = $deliveries->whereNotIn('status', ['Terminée', 'Annulée'])
                                     ->sortBy('planned_delivery_time')
                                     ->first();
        }

        return view('chauffeur.dashboard', compact('deliveries', 'totalDeliveries', 'completedDeliveries', 'pendingDeliveries', 'nextDelivery'));
    }

    /**
     * Affiche la liste complète des livraisons du chauffeur avec pagination.
     */
    public function deliveries(Request $request)
    {
        $driverId = Auth::id();
        $query = Delivery::query();

        // Récupérer les livraisons de l'utilisateur connecté
        $query->whereHas('deliveryRoute', function ($q) use ($driverId) {
            $q->where('driver_id', $driverId);
        });

        // Filtrer par route si un 'route_id' est présent dans la requête
        if ($request->has('route_id')) {
            $query->where('delivery_route_id', $request->input('route_id'));
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

        // ✅ Le modèle se chargera de déduire le stock automatiquement
        $delivery->status = 'Terminée';
        $delivery->delivered_at = Carbon::now();
        $delivery->save();

        // Mise à jour de la commande (statut uniquement)
        $order = $delivery->order;
        if ($order && $order->status !== 'Terminée') {
            $order->status = 'Terminée';
            $order->save();
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