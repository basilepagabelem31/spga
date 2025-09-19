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
        $pendingOrders = Order::where('status', 'pending')->count();
        $outOfStockProducts = Stock::select('product_id')
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) = 0')
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