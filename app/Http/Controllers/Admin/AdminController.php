<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Partner;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
       
         // 3. On compte le nombre total de produits.
        $totalProducts = Product::count();
        // 2. Déclaration et définition de la variable pour les produits de producteurs partenaires
        // Nous comptons le nombre de produits dont le type de provenance est 'producteur_partenaire'
        $totalPartnerProducts = Product::where('provenance_type', 'producteur_partenaire')->count();
        $totalUsers = User::count();
        $totalPartners = Partner::count();
        $pendingOrders = Order::where('status', 'pending')->count();
  // On compte le nombre de produits dont le stock total est égal à 0.
        // Cela nécessite une requête plus complexe sur la table des stocks.
        $outOfStockProducts = Stock::select('product_id')
            // On regroupe les mouvements par produit
            ->groupBy('product_id')
            // On calcule la somme de la colonne 'quantity' et on filtre pour les totaux de 0
            ->havingRaw('SUM(quantity) = 0')
            // On compte le nombre de produits qui correspondent à ce critère
            ->count();

         // 4. On récupère les données de commandes agrégées par année.
        // Correction ici : la quantité se trouve dans la table order_items.
        // Nous devons donc faire une jointure pour la sommer.
        $yearlyOrderData = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->select(
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('SUM(order_items.quantity) as total_quantity_sold')
            )
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get();



        return view('admin.dashboard' , compact('totalUsers' , 'totalPartners' , 'pendingOrders', 'outOfStockProducts' , 'totalPartnerProducts' , 'totalProducts' , 'yearlyOrderData'));
    }
}