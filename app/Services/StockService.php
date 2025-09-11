<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Order;
use App\Mail\NewOrderSupplierNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Crée un mouvement de stock et met à jour le produit associé.
     * Ajout d'un paramètre $notes pour rendre le mouvement plus explicite.
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
            $product->decrement('current_stock_quantity', $quantity);
        } elseif ($movementType === 'entrée') {
            $product->increment('current_stock_quantity', $quantity);
        }

        $product->refresh();
        $product->updateAvailabilityStatus();
        $product->sendLowStockNotification();
    }

    /**
     * Déduit le stock pour une commande.
     */
    public function deductStockForOrder(Order $order)
    {
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
     * Notifie les fournisseurs d’une commande validée ou en préparation.
     */
    public function notifySuppliers(Order $order)
    {
        $client = $order->client;
        foreach ($order->orderItems as $item) {
            $product = $item->product;
            if (!$product) continue;

            $supplierPartner = $product->getSupplierPartner();
            if ($supplierPartner && $supplierPartner->email) {
                try {
                    Mail::to($supplierPartner->email)->send(new NewOrderSupplierNotification($order, $item, $client));
                    Log::info("Notification envoyée à {$supplierPartner->establishment_name} pour {$product->name}");
                } catch (\Exception $e) {
                    Log::error("Erreur notification fournisseur ({$product->name}) : " . $e->getMessage());
                }
            } else {
                Log::warning("Pas de fournisseur/email pour {$product->name}");
            }
        }
    }
}