<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Partner;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class AdminController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    public function index()
    {
        // Log de l'accès au tableau de bord
        $this->recordLog(
            'acces_tableau_de_bord_admin',
            null,
            null,
            null,
            null
        );

        $totalProducts = Product::count();
        $totalPartnerProducts = Product::where('provenance_type', 'producteur_partenaire')->count();
        $totalUsers = User::count();
        $totalPartners = Partner::count();
        
        // Calcul des commandes en attente
        $pendingOrders = Order::whereIn('status', ['En attente de validation'])->count();

        // Calcul des produits en rupture de stock en fonction du seuil d'alerte
        // On utilise un sous-select pour la somme des stocks et on filtre
        // Produits en rupture de stock selon le seuil d'alerte
        $outOfStockProducts = Product::whereNotNull('alert_threshold')
                                        ->whereColumn('current_stock_quantity', '<=', 'alert_threshold')
                                        ->count();

        
        $yearlyOrderData = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('SUM(order_items.quantity) as total_quantity_sold')
            )
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();




        return view('admin.dashboard', compact('totalUsers', 'totalPartners', 'pendingOrders', 'outOfStockProducts', 'totalPartnerProducts', 'totalProducts', 'yearlyOrderData'));
    }
}