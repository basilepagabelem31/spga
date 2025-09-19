<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Contract;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche le tableau de bord partenaire avec les données dynamiques.
     */
    public function index()
    {
        $partnerId = Auth::user()->partner->id;

        // Log de l'accès au tableau de bord partenaire
        $this->recordLog(
            'acces_tableau_de_bord_partenaire',
            'partners',
            $partnerId,
            null,
            null
        );

        $myTotalProducts = Product::where('provenance_type', 'producteur_partenaire')
                                 ->where('provenance_id', $partnerId)
                                 ->count();

        $activeContractsCount = Contract::where('partner_id', $partnerId)
                                       ->where('start_date', '<=', now())
                                       ->where(function ($query) {
                                           $query->whereNull('end_date')
                                                 ->orWhere('end_date', '>=', now());
                                       })
                                       ->count();
        
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
     */
    public function products()
    {
        $partnerId = Auth::user()->partner->id;

        // Log de l'accès à la liste des produits
        $this->recordLog(
            'acces_liste_produits_partenaire',
            'products',
            null,
            ['partner_id' => $partnerId],
            null
        );
        
        $products = Product::where('provenance_type', 'producteur_partenaire')
                           ->where('provenance_id', $partnerId)
                           ->get();
                           
        return view('partner.products.index', compact('products'));
    }

    /**
     * Affiche la liste des contrats du partenaire.
     */
    public function contracts()
    {
        $partnerId = Auth::user()->partner->id;

        // Log de l'accès à la liste des contrats
        $this->recordLog(
            'acces_liste_contrats_partenaire',
            'contracts',
            null,
            ['partner_id' => $partnerId],
            null
        );
        
        $contracts = Contract::where('partner_id', $partnerId)->get();
        
        return view('partner.contracts.index', compact('contracts'));
    }
}