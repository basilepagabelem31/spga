<?php

namespace App\Http\Controllers\Chauffeur;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\DeliveryRoute;
use App\Services\StockService;
use App\Traits\LogsActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChauffeurController extends Controller
{
    use LogsActivity;

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

        $this->recordLog(
            'acces_tableau_de_bord_chauffeur',
            null,
            null,
            ['driver_id' => $driverId],
            null
        );

        $deliveries = Delivery::whereHas('deliveryRoute', function ($q) use ($driverId, $today) {
                $q->where('driver_id', $driverId)
                  ->whereDate('delivery_date', $today);
            })
            ->with('order.client')
            ->get();

        $totalDeliveries = $deliveries->count();
        $completedDeliveries = $deliveries->where('status', 'Terminée')->count();
        $pendingDeliveries = $deliveries->whereNotIn('status', ['Terminée', 'Annulée'])->count();

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

    /**
     * Affiche la liste complète des livraisons du chauffeur avec filtres.
     */
    public function deliveries(Request $request)
    {
        $driverId = Auth::id();

        $this->recordLog(
            'acces_liste_livraisons_chauffeur',
            null,
            null,
            ['driver_id' => $driverId, 'filters' => $request->all()],
            null
        );





        $query = Delivery::query();

        // On s'assure que seules les livraisons du chauffeur connecté sont affichées
        $query->whereHas('deliveryRoute', function ($q) use ($driverId) {
            $q->where('driver_id', $driverId);
        })->with('order.client', 'deliveryRoute'); // On charge la relation deliveryRoute


                // Filtrer par route_id si présent
        if ($request->filled('route_id')) {
        $query->where('delivery_route_id', $request->input('route_id'));
        }

        // Filtre par statut (inchangé)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }


        // CORRECTION : Le filtre de date pour les LIVRAISONS se fait sur la date de livraison effective (delivered_at)
        if ($request->filled('delivered_at')) {
            $query->whereDate('delivered_at', $request->input('delivered_at'));
        }

        $deliveries = $query->orderBy('created_at', 'desc')
            ->paginate(3)
            ->withQueryString();

        return view('chauffeur.deliveries', compact('deliveries'));
    }



    public function deliveriesModal(DeliveryRoute $route)
    {
    $driverId = Auth::id();

    // Vérifier que la tournée appartient bien au chauffeur connecté
    if ($route->driver_id !== $driverId) {
        abort(403, "Accès non autorisé");
    }

    $deliveries = $route->deliveries()->with('order.client')->get();

    return view('chauffeur.partials.deliveries_modal', compact('route', 'deliveries'));
    }


    /**
     * Marque une livraison comme terminée.
     */
    public function completeDelivery(Delivery $delivery)
    {
        $driverId = Auth::id();
        
        if ($delivery->status === 'Terminée') {
            $this->recordLog(
                'echec_mise_a_jour_livraison',
                'deliveries',
                $delivery->id,
                ['error' => 'Livraison déjà terminée', 'driver_id' => $driverId],
                null
            );
            return redirect()->back()->with('info', 'Cette livraison est déjà terminée. Aucune action n\'est requise.');
        }

        if ($delivery->deliveryRoute->driver_id !== $driverId) {
            $this->recordLog(
                'echec_mise_a_jour_livraison',
                'deliveries',
                $delivery->id,
                ['error' => 'Accès non autorisé', 'driver_id' => $driverId],
                null
            );
            return redirect()->back()->with('error', 'Vous n\'êtes pas autorisé à modifier cette livraison.');
        }

        try {
            DB::beginTransaction();

            $oldStatus = $delivery->status;
            $oldDeliveredAt = $delivery->delivered_at;

            $delivery->status = 'Terminée';
            $delivery->delivered_at = Carbon::now();
            $delivery->save();

            $order = $delivery->order;
            if ($order && $order->status !== 'Terminée') {
                $order->status = 'Terminée';
                $order->save();

                $order->load('orderItems.product', 'client');
                $this->stockService->deductStockForOrder($order);
                $this->stockService->notifySuppliers($order);
            }

            $deliveryRoute = $delivery->deliveryRoute;
            if ($deliveryRoute) {
                if ($deliveryRoute->status === 'planifiée') {
                    $deliveryRoute->update(['status' => 'en_cours']);
                }

                $remaining = $deliveryRoute->deliveries()->whereNotIn('status', ['Terminée', 'Annulée'])->count();
                if ($remaining === 0) {
                    $deliveryRoute->update(['status' => 'terminée']);
                }
            }

            DB::commit();

            $this->recordLog(
                'mise_a_jour_livraison',
                'deliveries',
                $delivery->id,
                ['old_status' => $oldStatus, 'old_delivered_at' => $oldDeliveredAt],
                ['new_status' => $delivery->status, 'new_delivered_at' => $delivery->delivered_at]
            );

            return redirect()->back()->with('success', 'Livraison marquée comme terminée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la mise à jour du statut de livraison: ' . $e->getMessage());
            
            $this->recordLog(
                'echec_mise_a_jour_livraison',
                'deliveries',
                $delivery->id,
                ['error' => 'Erreur de base de données', 'exception' => $e->getMessage()],
                null
            );
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de la livraison.');
        }
    }

    /**
     * Affiche le planning des tournées du chauffeur.
     */
    public function planning(Request $request)
    {
        $driverId = Auth::id();

        $this->recordLog(
            'acces_planning_chauffeur',
            'delivery_routes',
            null,
            ['driver_id' => $driverId, 'filters' => $request->all()],
            null
        );

        $query = DeliveryRoute::where('driver_id', $driverId)
            ->with('deliveries');

        // Filtre par statut (inchangé)
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Le filtre de date pour le PLANNING se fait aussi sur la date de la TOURNEE (delivery_date)
        if ($request->filled('delivery_date')) {
            $query->whereDate('delivery_date', $request->input('delivery_date'));
        }

        $deliveryRoutes = $query->orderBy('delivery_date', 'asc')
            ->paginate(4)
            ->withQueryString();

        return view('chauffeur.planning', compact('deliveryRoutes'));
    }
}