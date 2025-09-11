<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\User; // Pour le chauffeur
use App\Notifications\DeliveryRouteAssigned;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryRouteController extends Controller
{
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
            ->paginate(10)
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
        $oldStatus = $deliveryRoute->status;

        $request->validate([
            'delivery_date' => 'required|date',
            'driver_id' => 'required|exists:users,id',
            'vehicle_info' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['Planifiée', 'En cours', 'Terminée', 'Annulée'])],
            'temporary_deliverers' => 'nullable|array',
        ]);

        $deliveryRoute->update($request->all());

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
        $deliveryRoute->delete();

        return redirect()->route('delivery_routes.index')
                         ->with('success', 'Tournée de livraison supprimée avec succès.');
    }
}