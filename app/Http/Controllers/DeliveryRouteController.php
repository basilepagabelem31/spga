<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\User; // Pour le chauffeur
use App\Notifications\DeliveryRouteAssigned;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class DeliveryRouteController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des tournées de livraison avec filtrage.
     */
    public function index(Request $request)
    {
        $drivers = User::whereHas('role', function ($query) {
            $query->where('name', 'chauffeur');
        })->get();

        $deliveryRoutes = DeliveryRoute::with('driver')
            ->when($request->filled('driver_id'), function ($query) use ($request) {
                $query->where('driver_id', $request->driver_id);
            })
            ->when($request->filled('delivery_date'), function ($query) use ($request) {
                $query->whereDate('delivery_date', $request->delivery_date);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->whereHas('driver', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('delivery_date', 'desc')
            ->paginate(8)
            ->withQueryString();

        return view('delivery_routes.index', compact('deliveryRoutes', 'drivers'));
    }

    /**
     * Stocke une nouvelle tournée de livraison dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_date' => 'required|date',
            'driver_id' => 'required|exists:users,id',
            'vehicle_info' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['Planifiée', 'En cours', 'Terminée', 'Annulée'])],
            'temporary_deliverers' => 'nullable|array',
        ]);

        $deliveryRoute = DeliveryRoute::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_tournee_livraison',
            'delivery_routes',
            $deliveryRoute->id,
            null,
            $deliveryRoute->toArray()
        );

        $driver = User::find($request->driver_id);
        if ($driver) {
            $driver->notify(new DeliveryRouteAssigned($deliveryRoute));
        }

        return redirect()->route('delivery_routes.index')
                         ->with('success', 'Tournée de livraison créée avec succès et chauffeur notifié.');
    }

    /**
     * Affiche les détails d'une tournée de livraison spécifique.
     */
    public function show(DeliveryRoute $deliveryRoute)
    {
        $deliveryRoute->load('driver', 'deliveries');
        return view('delivery_routes.show', compact('deliveryRoute'));
    }

    /**
     * Met à jour une tournée de livraison existante dans la base de données.
     */
    public function update(Request $request, DeliveryRoute $deliveryRoute)
    {
        $oldValues = $deliveryRoute->toArray(); // Capture des valeurs avant la mise à jour
        $oldStatus = $deliveryRoute->status;

        $request->validate([
            'delivery_date' => 'required|date',
            'driver_id' => 'required|exists:users,id',
            'vehicle_info' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['Planifiée', 'En cours', 'Terminée', 'Annulée'])],
            'temporary_deliverers' => 'nullable|array',
        ]);

        $deliveryRoute->update($request->all());

        $newValues = $deliveryRoute->refresh()->toArray(); // Capture des nouvelles valeurs
        
        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_tournee_livraison',
            'delivery_routes',
            $deliveryRoute->id,
            $oldValues,
            $newValues
        );

        if ($oldStatus === 'Planifiée' && $deliveryRoute->status === 'En cours') {
            $driver = $deliveryRoute->driver;
            if ($driver) {
                $driver->notify(new DeliveryRouteAssigned($deliveryRoute));
            }
        }

        return redirect()->route('delivery_routes.index')
                         ->with('success', 'Tournée de livraison mise à jour avec succès.');
    }

    /**
     * Supprime une tournée de livraison de la base de données.
     */
    public function destroy(DeliveryRoute $deliveryRoute)
    {
        if ($deliveryRoute->deliveries()->count() > 0) {
            return redirect()->route('delivery_routes.index')
                             ->with('error', 'Impossible de supprimer cette tournée car elle contient des livraisons.');
        }

        $oldValues = $deliveryRoute->toArray(); // Capture des valeurs avant la suppression
        $deliveryRouteId = $deliveryRoute->id;

        $deliveryRoute->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_tournee_livraison',
            'delivery_routes',
            $deliveryRouteId,
            $oldValues,
            null
        );

        return redirect()->route('delivery_routes.index')
                         ->with('success', 'Tournée de livraison supprimée avec succès.');
    }
}