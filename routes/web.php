<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Contrôleurs principaux
use App\Http\Controllers\PartnerManagedProductController; // <-- Nouveau contrôleur
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleHasPermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Models\Order;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PartnerProductController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\ProductionFollowUpController;
use App\Http\Controllers\EstimatedHarvestDateController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\DeliveryRouteController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\QualityControlController;
use App\Http\Controllers\NonConformityController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Partner\PartnerOrdersController ;

// Dashboards par rôle
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Partner\PartnerDashboardController;
use App\Http\Controllers\Chauffeur\ChauffeurController;

// Page d'accueil
Route::get('/', function () {
    return view('welcome');
});

// Dashboard générique (redirection potentielle selon rôle)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Gestion de profil (tous utilisateurs connectés)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Nouvelle route pour la mise à jour du mot de passe
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
});


// --- Admins & Superviseurs ---
Route::middleware(['auth', 'role:admin_principal,superviseur_commercial,superviseur_production'])->group(function () {


    Route::post('/orders/{order}/validate', [OrderController::class, 'validateOrder'])
    ->name('orders.validate');
    
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
       // Déclarez d'abord les routes spécifiques.
    Route::get('/users/clients', [UserController::class, 'index'])->name('users.clients');
    Route::get('/users/chauffeurs', [UserController::class, 'index'])->name('users.chauffeurs');
    Route::get('/users/admins', [UserController::class, 'index'])->name('users.admins');
    Route::resource('users', UserController::class);
    
    Route::put('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    // Route::resource('role-has-permissions', RoleHasPermissionController::class);

    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('partners', PartnerController::class);
    Route::resource('contracts', ContractController::class);
     Route::resource('production_follow_ups', ProductionFollowUpController::class)->parameters([
        'production_follow_ups' => 'productionFollowUp'
    ]);
    
    // Routes imbriquées pour EstimatedHarvestDate sous ProductionFollowUp
    // Routes imbriquées pour les dates de récolte estimées
    Route::prefix('production_follow_ups/{productionFollowUp}')->name('production_follow_ups.')->group(function () {
        Route::resource('estimated_harvest_dates', EstimatedHarvestDateController::class)->parameters([
            'estimated_harvest_dates' => 'estimatedHarvestDate'
        ]);
    });
    Route::resource('stocks', StockController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('order-items', OrderItemController::class)->parameters([
        'order-items' => 'orderItem'
    ]);
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('delivery-routes', DeliveryRouteController::class)->parameters([
        'delivery-routes' => 'deliveryRoute'
    ]);
    Route::resource('quality_controls', QualityControlController::class)->parameters([
        'quality_controls' => 'qualityControl'
    ]);
    Route::resource('non_conformities', NonConformityController::class)->parameters([
        'non_conformities' => 'nonConformity'
    ]);

    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'destroy']);
    Route::post('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show'])->parameters([
        'activity-logs' => 'activityLog'
    ]); 

    // Tables pivots
    Route::prefix('role-permissions')->name('role_has_permissions.')->group(function () {
        Route::get('/', [RoleHasPermissionController::class, 'index'])->name('index');
        Route::get('/create', [RoleHasPermissionController::class, 'create'])->name('create');
        Route::post('/', [RoleHasPermissionController::class, 'store'])->name('store');
        Route::get('/{role_id}/{permission_id}', [RoleHasPermissionController::class, 'show'])->name('show');
        Route::delete('/{role_id}/{permission_id}', [RoleHasPermissionController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('partner-products')->name('partner_products.')->group(function () {
        Route::get('/', [PartnerProductController::class, 'index'])->name('index');
        Route::get('/create', [PartnerProductController::class, 'create'])->name('create');
        Route::post('/', [PartnerProductController::class, 'store'])->name('store');
        Route::get('/{partnerProduct}', [PartnerProductController::class, 'show'])->name('show');
        Route::delete('/{partnerProduct}', [PartnerProductController::class, 'destroy'])->name('destroy');
    });



     

    
});


// --- Partenaires ---
Route::middleware(['auth', 'role:partenaire'])->group(function () {
    Route::get('/partenaire/dashboard', [PartnerDashboardController::class, 'index'])->name('partenaire.dashboard');
    Route::get('partenaire/products', [PartnerDashboardController::class, 'products'])->name('partenaire.products');
    Route::get('partenaire/contracts', [PartnerDashboardController::class, 'contracts'])->name('partenaire.contracts');
       // Correction : Ajout de la route pour les commandes
    Route::get('partenaire/orders', [PartnerOrdersController::class, 'index'])->name('partenaire.orders');
    Route::get('partenaire/orders/details/{order}', [PartnerOrdersController::class, 'show'])->name('partenaire.orders.show');

    Route::get('partenaire/products', [PartnerDashboardController::class, 'products'])->name('partenaire.products');
Route::get('partenaire/contracts', [PartnerDashboardController::class, 'contracts'])->name('partenaire.contracts');


 Route::resource('partenaire/products', PartnerManagedProductController::class)->names([
        'index' => 'partenaire.products',
        'store' => 'partenaire.products.store',
        'update' => 'partenaire.products.update',
        'destroy' => 'partenaire.products.destroy'
    ])->parameters(['products' => 'product']);


    Route::resource('partenaire/categories', App\Http\Controllers\PartnerCategoryController::class)->names('partenaire.categories');

    Route::put('/products/{product}/stock', [PartnerManagedProductController::class, 'updateStock'])->name('partenaire.products.updateStock');

   

});


Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientController::class, 'index'])->name('dashboard');

    // Routes des produits - LES PLUS SPÉCIFIQUES D'ABORD
    Route::get('/products/{product}/show_json', [ClientController::class, 'showProductJson'])->name('products.show.json');
    Route::get('/products/{product}', [ClientController::class, 'showProduct'])->name('products.show');
    Route::get('/products', [ClientController::class, 'products'])->name('products');

    // Routes des commandes
    Route::get('/orders', [ClientController::class, 'orders'])->name('orders');
    Route::get('/orders/create', [ClientController::class, 'createOrder'])->name('orders.create');
    Route::post('/orders', [ClientController::class, 'storeOrder'])->name('orders.store');
    Route::get('/orders/{order}', [ClientController::class, 'showOrder'])->name('orders.show');
    // Annuler une commande
    Route::post('/orders/{order}/cancel', [ClientController::class, 'cancelOrder'])->name('orders.cancel');

});


// --- Chauffeurs ---

Route::middleware(['auth'])->group(function () {
    Route::resource('delivery_routes', DeliveryRouteController::class);
    Route::resource('deliveries', DeliveryController::class);
});

Route::middleware(['auth', 'role:chauffeur'])->group(function () {
    Route::get('/chauffeur/dashboard', [ChauffeurController::class, 'index'])->name('chauffeur.dashboard');
    Route::get('chauffeur/livraisons', [ChauffeurController::class, 'deliveries'])->name('chauffeur.deliveries');
    Route::get('chauffeur/planning', [ChauffeurController::class, 'planning'])->name('chauffeur.planning');
    Route::put('chauffeur/deliveries/{delivery}/complete', [ChauffeurController::class, 'completeDelivery'])->name('chauffeur.deliveries.complete');

});




// Dans routes/web.php
Route::get('/test-stock-deduction/{orderId}', function($orderId) {
    $order = Order::with('orderItems.product')->findOrFail($orderId);
    $stockService = new App\Services\StockService();
    
    Log::info("=== TEST DÉDUCTION STOCK ===");
    Log::info("Commande: {$order->order_code}");
    Log::info("Statut: {$order->status}");
    Log::info("Nombre d'items: " . $order->orderItems->count());
    
    foreach ($order->orderItems as $item) {
        Log::info("Item: {$item->product->name} - {$item->quantity}");
        Log::info("Stock avant: {$item->product->current_stock_quantity}");
    }
    
    try {
        $stockService->deductStockForOrder($order);
        
        // Recharger les produits pour voir les nouvelles valeurs
        $order->load('orderItems.product');
        
        $results = [];
        foreach ($order->orderItems as $item) {
            $results[] = [
                'product' => $item->product->name,
                'quantity_deducted' => $item->quantity,
                'stock_after' => $item->product->current_stock_quantity
            ];
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Déduction effectuée',
            'results' => $results
        ]);
        
    } catch (\Exception $e) {
        Log::error("Erreur test: " . $e->getMessage());
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
});




// Dans routes/web.php
Route::get('/fix-stock/{productId}', function($productId) {
    $product = App\Models\Product::findOrFail($productId);
    
    // Calculer le stock réel basé sur les mouvements
    $entrees = App\Models\Stock::where('product_id', $productId)
        ->where('movement_type', 'entrée')
        ->sum('quantity');
    
    $sorties = App\Models\Stock::where('product_id', $productId)
        ->where('movement_type', 'sortie')
        ->sum('quantity');
    
    $stock_reel = $entrees - $sorties;
    
    // Corriger le stock
    $product->current_stock_quantity = $stock_reel;
    $product->updateAvailabilityStatus();
    $product->save();
    
    return response()->json([
        'success' => true,
        'product' => $product->name,
        'ancien_stock' => $product->current_stock_quantity,
        'nouveau_stock' => $stock_reel,
        'entrees' => $entrees,
        'sorties' => $sorties
    ]);
});

require __DIR__.'/auth.php';
