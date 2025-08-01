<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRoute;
use App\Models\User; // Pour le chauffeur
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DeliveryRouteController extends Controller
{
    /**
     * Affiche la liste des tournées de livraison.
     */
    public function index()
    {
        $deliveryRoutes = DeliveryRoute::with('driver')->paginate(10);
        return view('delivery_routes.index', compact('deliveryRoutes'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle tournée de livraison.
     */
    public function create()
    {
        $drivers = User::whereHas('role', function ($query) {
            $query->where('name', 'chauffeur');
        })->get();
        return view('delivery_routes.create', compact('drivers'));
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
            'status' => ['required', Rule::in(['planifiée', 'en_cours', 'terminée', 'annulée'])],
            'temporary_deliverers' => 'nullable|array',
        ]);

        DeliveryRoute::create($request->all());

        return redirect()->route('delivery_routes.index')
                         ->with('success', 'Tournée de livraison créée avec succès.');
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
     * Affiche le formulaire d'édition d'une tournée de livraison.
     */
    public function edit(DeliveryRoute $deliveryRoute)
    {
        $drivers = User::whereHas('role', function ($query) {
            $query->where('name', 'chauffeur');
        })->get();
        return view('delivery_routes.edit', compact('deliveryRoute', 'drivers'));
    }

    /**
     * Met à jour une tournée de livraison existante dans la base de données.
     */
    public function update(Request $request, DeliveryRoute $deliveryRoute)
    {
        $request->validate([
            'delivery_date' => 'required|date',
            'driver_id' => 'required|exists:users,id',
            'vehicle_info' => 'nullable|string|max:255',
            'status' => ['required', Rule::in(['planifiée', 'en_cours', 'terminée', 'annulée'])],
            'temporary_deliverers' => 'nullable|array',
        ]);

        $deliveryRoute->update($request->all());

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