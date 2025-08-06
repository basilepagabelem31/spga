<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

// Contrôleurs principaux
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleHasPermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
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
});


// --- Admins & Superviseurs ---
Route::middleware(['auth', 'role:admin_principal,superviseur_commercial,superviseur_production'])->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

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
    Route::resource('production-follow-ups', ProductionFollowUpController::class)->parameters([
        'production-follow-ups' => 'productionFollowUp'
    ]);
    Route::resource('estimated-harvest-dates', EstimatedHarvestDateController::class)->parameters([
        'estimated-harvest-dates' => 'estimatedHarvestDate'
    ]);
    Route::resource('stocks', StockController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('order-items', OrderItemController::class)->parameters([
        'order-items' => 'orderItem'
    ]);
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('delivery-routes', DeliveryRouteController::class)->parameters([
        'delivery-routes' => 'deliveryRoute'
    ]);
    Route::resource('quality-controls', QualityControlController::class)->parameters([
        'quality-controls' => 'qualityControl'
    ]);
    Route::resource('non-conformities', NonConformityController::class)->parameters([
        'non-conformities' => 'nonConformity'
    ]);

    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'destroy']);
    Route::put('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

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
});


// --- Clients ---
Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/client/dashboard', [ClientController::class, 'index'])->name('client.dashboard');
    Route::get('client/orders', [ClientController::class, 'orders'])->name('client.orders');
    Route::post('client/orders', [ClientController::class, 'storeOrder'])->name('client.orders.store');
    Route::get('client/products', [ClientController::class, 'products'])->name('client.products');
});


// --- Chauffeurs ---
Route::middleware(['auth', 'role:chauffeur'])->group(function () {
    Route::get('/chauffeur/dashboard', [ChauffeurController::class, 'index'])->name('chauffeur.dashboard');
    Route::get('chauffeur/livraisons', [ChauffeurController::class, 'deliveries'])->name('chauffeur.deliveries');
    Route::get('chauffeur/planning', [ChauffeurController::class, 'planning'])->name('chauffeur.planning');
});

require __DIR__.'/auth.php';
