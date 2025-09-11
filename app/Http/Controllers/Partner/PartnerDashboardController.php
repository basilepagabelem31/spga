<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord partenaire avec les données dynamiques.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // On récupère l'ID du partenaire à partir de l'utilisateur authentifié
        $partnerId = Auth::user()->partner->id;

        // 1. Calcul du nombre total de produits du partenaire
        $myTotalProducts = Product::where('provenance_type', 'producteur_partenaire')
                                 ->where('provenance_id', $partnerId)
                                 ->count();

        // 2. Calcul du nombre de contrats actifs pour le partenaire
        $activeContractsCount = Contract::where('partner_id', $partnerId)
                                       ->where('start_date', '<=', now())
                                       ->where(function ($query) {
                                           $query->whereNull('end_date')
                                                 ->orWhere('end_date', '>=', now());
                                       })
                                       ->count();
        
        // 3. Récupération des commandes récentes pour les produits de ce partenaire
        $myProductIds = Product::where('provenance_type', 'producteur_partenaire')
                               ->where('provenance_id', $partnerId)
                               ->pluck('id');

        $recentOrdersCount = Order::whereHas('orderItems.product', function ($query) use ($myProductIds) {
            $query->whereIn('id', $myProductIds);
        })->count();

        $recentOrders = Order::whereHas('orderItems.product', function ($query) use ($myProductIds) {
            $query->whereIn('id', $myProductIds);
        })
        ->with(['orderItems' => function ($query) use ($myProductIds) {
            $query->whereIn('product_id', $myProductIds)
                  ->with('product');
        }])
        ->latest()
        ->take(10)
        ->get();
        
        return view('partner.dashboard', compact(
            'myTotalProducts',
            'activeContractsCount',
            'recentOrdersCount',
            'recentOrders'
        ));
    }

    /**
     * Affiche la liste des produits du partenaire.
     *
     * @return \Illuminate\View\View
     */
    public function products()
    {
        $partnerId = Auth::user()->partner->id;
        $products = Product::where('provenance_type', 'producteur_partenaire')
                           ->where('provenance_id', $partnerId)
                           ->get();
                           
        return view('partner.products.index', compact('products'));
    }

    /**
     * Affiche la liste des contrats du partenaire.
     *
     * @return \Illuminate\View\View
     */
    public function contracts()
    {
        $partnerId = Auth::user()->partner->id;
        $contracts = Contract::where('partner_id', $partnerId)->get();
        
        return view('partner.contracts.index', compact('contracts'));
    }
}
