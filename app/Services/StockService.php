<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Order;
use App\Mail\LowStockAlertMail;
use App\Mail\NewOrderSupplierNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Crée un mouvement de stock et met à jour le produit associé.
     */
    public function createStockMovement($productId, $quantity, $movementType, $referenceId, $movementDate, $notes = null)
    {
        $product = Product::find($productId);
        if (!$product) {
            Log::error("Tentative de création d'un mouvement de stock pour un produit inexistant (ID: {$productId}).");
            return;
        }

        // Création du mouvement
        Stock::create([
            'product_id' => $productId,
            'quantity' => $quantity,
            'movement_type' => $movementType,
            'reference_id' => $referenceId,
            'movement_date' => $movementDate,
            'notes' => $notes,
        ]);

        // Mise à jour du stock produit
        if ($movementType === 'sortie') {
            $product->current_stock_quantity -= $quantity;
        } elseif ($movementType === 'entrée' || $movementType === 'future_recolte') {
            $product->current_stock_quantity += $quantity;
        }

        $product->save();

        // Mettre à jour le statut (si la méthode existe)
        if (method_exists($product, 'updateAvailabilityStatus')) {
            $product->updateAvailabilityStatus();
        }

        // Vérifier le stock bas (actuellement log only)
        $this->checkLowStock($product);

        Log::info("Mouvement de stock: {$movementType} de {$quantity} pour produit ID: {$productId}. Nouveau stock: {$product->current_stock_quantity}");
    }

protected function checkLowStock(Product $product)
{
    if ($product->alert_threshold !== null && 
        $product->current_stock_quantity <= $product->alert_threshold) {

        Log::info("⚠️ Stock bas pour le produit: {$product->name} (stock actuel: {$product->current_stock_quantity}, seuil: {$product->alert_threshold})");

        // 🔔 Choisir les destinataires (admins / superviseurs par ex.)
        $usersToNotify = \App\Models\User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin_principal', 'superviseur_production']);
        })->get();

        foreach ($usersToNotify as $user) {
            if (!empty($user->email)) {
                try {
                    Mail::to($user->email)->send(new LowStockAlertMail($product));
                    Log::info("📧 Alerte stock envoyée à {$user->email} pour {$product->name}");
                } catch (\Throwable $e) {
                    Log::error("Erreur envoi alerte stock ({$product->name}) : " . $e->getMessage());
                }
            }
        }
    }
}

    /**
     * Déduit le stock pour une commande.
     */
    public function deductStockForOrder(Order $order)
    {
        Log::info("Déduction du stock pour la commande: {$order->order_code}");
        Log::info("Nombre d'items trouvés: " . $order->orderItems->count());

        foreach ($order->orderItems as $item) {
            $this->createStockMovement(
                $item->product_id,
                $item->quantity,
                'sortie',
                $order->order_code,
                now(),
                'Déduction pour commande terminée'
            );
        }

        Log::info("Déduction du stock terminée pour la commande: {$order->order_code}");
    }

    /**
     * Réinjecte le stock pour une commande annulée/supprimée.
     */
    public function replenishStockForOrder(Order $order)
    {
        foreach ($order->orderItems as $item) {
            $this->createStockMovement(
                $item->product_id,
                $item->quantity,
                'entrée',
                'ANNULATION_' . $order->order_code,
                now(),
                'Annulation de commande'
            );
        }
    }

    /**
     * Ajuste le stock lors d’une modification d’articles de commande.
     */
    public function adjustStockForOrderItemsChange(Order $order, $oldOrderItems, $newOrderItems)
    {
        // Produits retirés / réduits
        foreach ($oldOrderItems as $productId => $oldItem) {
            $newItem = $newOrderItems->get($productId);
            if (!$newItem) {
                $this->createStockMovement(
                    $productId,
                    $oldItem->quantity,
                    'entrée',
                    'MODIF_CMD_RETRAIT_' . $order->order_code,
                    now(),
                    'Réajustement : retrait de produit'
                );
            } elseif ($newItem['quantity'] < $oldItem->quantity) {
                $diff = $oldItem->quantity - $newItem['quantity'];
                $this->createStockMovement(
                    $productId,
                    $diff,
                    'entrée',
                    'MODIF_CMD_REDUCTION_' . $order->order_code,
                    now(),
                    'Réajustement : réduction de quantité'
                );
            }
        }

        // Produits ajoutés / augmentés
        foreach ($newOrderItems as $productId => $newItem) {
            $oldItem = $oldOrderItems->get($productId);
            if (!$oldItem || $newItem['quantity'] > $oldItem->quantity) {
                $diff = $newItem['quantity'] - ($oldItem ? $oldItem->quantity : 0);
                $this->createStockMovement(
                    $productId,
                    $diff,
                    'sortie',
                    'MODIF_CMD_AJOUT_' . $order->order_code,
                    now(),
                    'Réajustement : ajout de produit/quantité'
                );
            }
        }
    }

    /**
     * Notifie les fournisseurs d’une commande.
     * Robustesse + logs pour debug.
     */
    public function notifySuppliers(Order $order)
{
    // Charger toutes les relations nécessaires
    $order->loadMissing('orderItems.product.partners', 'client');

    $client = $order->client;
    Log::info("notifySuppliers called for order {$order->order_code}, items: " . $order->orderItems->count());

    foreach ($order->orderItems as $item) {
        $product = $item->product;

        if (!$product) {
            Log::warning("❌ OrderItem id {$item->id} n'a pas de produit.");
            continue;
        }

        // --- Étape 1 : chercher fournisseur principal ---
        $supplierPartner = null;

        // a) Méthode préférée
        if (method_exists($product, 'getSupplierPartner')) {
            try {
                $supplierPartner = $product->getSupplierPartner();
                if ($supplierPartner) {
                    Log::info("✅ Fournisseur trouvé via getSupplierPartner: {$supplierPartner->establishment_name}");
                }
            } catch (\Throwable $e) {
                Log::error("Erreur getSupplierPartner() pour produit {$product->id} : " . $e->getMessage());
            }
        }

        // b) Relation supplier directe
        if (!$supplierPartner && isset($product->supplier)) {
            $supplierPartner = $product->supplier;
            Log::info("✅ Fournisseur trouvé via relation supplier: {$supplierPartner->establishment_name}");
        }

        // c) Fallback sur la relation partners()
        if (!$supplierPartner && $product->partners()->exists()) {
            $supplierPartner = $product->partners()->first();
            Log::info("✅ Fournisseur trouvé via relation partners(): {$supplierPartner->establishment_name}");
        }

        // --- Étape 2 : vérification ---
        if (!$supplierPartner) {
            Log::warning("❌ Aucun fournisseur trouvé pour produit {$product->name} (ID: {$product->id}).");
            continue;
        }

        if (empty($supplierPartner->email)) {
            Log::warning("❌ Pas d'email fournisseur pour {$supplierPartner->establishment_name} (produit: {$product->name}).");
            continue;
        }

        // --- Étape 3 : envoi email ---
        try {
            Log::info("📧 Envoi mail à {$supplierPartner->email} pour produit {$product->name}, commande {$order->order_code}");
            Mail::to($supplierPartner->email)->send(
                new NewOrderSupplierNotification($order, $item, $client)
            );
            Log::info("✅ Mail envoyé à {$supplierPartner->email} pour {$product->name}");
        } catch (\Throwable $e) {
            Log::error("❌ Erreur envoi mail fournisseur ({$product->name}) à {$supplierPartner->email} : " . $e->getMessage());
            Log::error($e->getTraceAsString());
        }
    }
}

}
