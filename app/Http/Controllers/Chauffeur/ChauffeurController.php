<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryRoute;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChauffeurController extends Controller
{
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
    if ($delivery->deliveryRoute->driver_id !== Auth::id()) {
        return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette livraison.');
    }

    $delivery->status = 'Terminée';
    $delivery->delivered_at = Carbon::now();
    $delivery->save();

    // ---- AJOUT : mise à jour de la tournée ----
    $deliveryRoute = $delivery->deliveryRoute;

    if ($deliveryRoute) {
        // Si la tournée était Planifiée, la mettre en En cours dès qu’une livraison bouge
        if ($deliveryRoute->status === 'Planifiée') {
            $deliveryRoute->update(['status' => 'En cours']);
        }

        // Vérifier si toutes les livraisons sont terminées ou annulées
        $remaining = $deliveryRoute->deliveries()->whereNotIn('status', ['Terminée', 'Annulée'])->count();

        if ($remaining === 0) {
            $deliveryRoute->update(['status' => 'Terminée']);
        }
    }
    // ---- FIN AJOUT ----

    return redirect()->back()->with('success', 'Livraison marquée comme terminée.');
}




     public function planning()
    {
        $driverId = Auth::id();
        
        // Récupérer toutes les tournées (delivery_routes) du chauffeur triées par date
        $deliveryRoutes = DeliveryRoute::where('driver_id', $driverId)
                                       ->orderBy('delivery_date', 'asc')
                                       ->with('deliveries') // Chargement anticipé des livraisons
                                       ->get();

        return view('chauffeur.planning', compact('deliveryRoutes'));
    }
}