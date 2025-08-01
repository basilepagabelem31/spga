<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
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



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');





// Gestion des Rôles
    Route::resource('roles', RoleController::class);

    // Gestion des Permissions
    Route::resource('permissions', PermissionController::class);

    // Gestion des Utilisateurs
    Route::resource('users', UserController::class);
    // Routes spécifiques pour l'activation/désactivation d'utilisateurs
    Route::put('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::put('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    // Gestion des Catégories
    Route::resource('categories', CategoryController::class);

    // Gestion des Produits
    Route::resource('products', ProductController::class);

    // Gestion des Partenaires
    Route::resource('partners', PartnerController::class);

    // Gestion des Contrats
    Route::resource('contracts', ContractController::class);

    // Gestion du Suivi de Production
    Route::resource('production-follow-ups', ProductionFollowUpController::class)->parameters([
        'production-follow-ups' => 'productionFollowUp' // Pour correspondre au nom de variable dans le contrôleur
    ]);

    // Gestion des Dates de Récolte Estimées
    Route::resource('estimated-harvest-dates', EstimatedHarvestDateController::class)->parameters([
        'estimated-harvest-dates' => 'estimatedHarvestDate' // Pour correspondre au nom de variable dans le contrôleur
    ]);

    // Gestion des Stocks
    Route::resource('stocks', StockController::class);

    // Gestion des Commandes
    Route::resource('orders', OrderController::class);

    // Gestion des Livraisons
    Route::resource('deliveries', DeliveryController::class);

    // Gestion des Tournées de Livraison
    Route::resource('delivery-routes', DeliveryRouteController::class)->parameters([
        'delivery-routes' => 'deliveryRoute'
    ]);

    // Gestion des Contrôles Qualité
    Route::resource('quality-controls', QualityControlController::class)->parameters([
        'quality-controls' => 'qualityControl'
    ]);

    // Gestion des Non-Conformités
    Route::resource('non-conformities', NonConformityController::class)->parameters([
        'non-conformities' => 'nonConformity'
    ]);

    // Gestion des Notifications
    // Les notifications ne sont généralement pas créées ou modifiées via un formulaire utilisateur standard.
    // Nous limitons donc les actions à celles qui ont du sens.
    Route::resource('notifications', NotificationController::class)->only(['index', 'show', 'destroy']);
    Route::put('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Journalisation des Activités (lecture seule)
    // Les journaux d'activités ne sont pas modifiables/supprimables par l'utilisateur.
    Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show'])->parameters([
        'activity-logs' => 'activityLog'
    ]);


    // --- Routes spécifiques pour les tables pivots (Clés composites) ---
    // Pour RoleHasPermission et PartnerProduct, les routes resource par défaut de Laravel
    // ne gèrent pas bien les clés composites. Il est plus clair de définir des routes explicites
    // ou de gérer ces associations via les contrôleurs des modèles parents.
    // Cependant, si un CRUD direct est requis pour ces tables pivots, voici comment les définir:

    // Gestion des Associations Rôle-Permission
    Route::prefix('role-permissions')->name('role_has_permissions.')->group(function () {
        Route::get('/', [RoleHasPermissionController::class, 'index'])->name('index');
        Route::get('/create', [RoleHasPermissionController::class, 'create'])->name('create');
        Route::post('/', [RoleHasPermissionController::class, 'store'])->name('store');
        // Pour show et destroy, nous passons les deux IDs
        Route::get('/{role_id}/{permission_id}', [RoleHasPermissionController::class, 'show'])->name('show');
        Route::delete('/{role_id}/{permission_id}', [RoleHasPermissionController::class, 'destroy'])->name('destroy');
        // Pas de edit/update typiques pour les associations binaires
    });

    // Gestion des Associations Produit-Partenaire
    // (Similaire à RoleHasPermission, gérer l'association directe ou via les modèles parents)
    Route::prefix('partner-products')->name('partner_products.')->group(function () {
        Route::get('/', [PartnerProductController::class, 'index'])->name('index');
        Route::get('/create', [PartnerProductController::class, 'create'])->name('create');
        Route::post('/', [PartnerProductController::class, 'store'])->name('store');
        Route::get('/{partnerProduct}', [PartnerProductController::class, 'show'])->name('show'); // Peut utiliser une seule ID si une PK auto-incrémentée existe
        Route::delete('/{partnerProduct}', [PartnerProductController::class, 'destroy'])->name('destroy'); // Peut utiliser une seule ID si une PK auto-incrémentée existe
    });

    // Gestion des Articles de Commande (OrderItems)
    // Bien que souvent gérés via le contrôleur Order, un accès direct peut être utile.
    Route::resource('order-items', OrderItemController::class)->parameters([
        'order-items' => 'orderItem'
    ]);




});

require __DIR__.'/auth.php';
