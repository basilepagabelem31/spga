<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class PartnerOrdersController extends Controller
{
    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Affiche la liste des commandes du partenaire avec filtres.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->partner) {
            abort(403, 'Accès non autorisé. Vous devez être un partenaire pour voir cette page.');
        }

        $partnerId = Auth::user()->partner->id;
        $myProductIds = Product::where('provenance_type', 'producteur_partenaire')
                               ->where('provenance_id', $partnerId)
                               ->pluck('id');

        $query = Order::whereHas('orderItems.product', function ($subQuery) use ($myProductIds) {
                                $subQuery->whereIn('id', $myProductIds);
                           })
                           ->with(['client', 'validatedBy', 'orderItems' => function ($subQuery) use ($myProductIds) {
                               $subQuery->whereIn('product_id', $myProductIds)
                                       ->with('product');
                           }]);

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($subQuery) use ($searchTerm) {
                $subQuery->where('order_code', 'like', '%' . $searchTerm . '%')
                         ->orWhereHas('client', function ($clientQuery) use ($searchTerm) {
                             $clientQuery->where('name', 'like', '%' . $searchTerm . '%');
                         });
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        $statuses = [
            'all' => 'Tous les statuts',
            'En attente de validation' => 'En attente de validation',
            'Validée' => 'Validée',
            'En préparation' => 'En préparation',
            'En livraison' => 'En livraison',
            'Terminée' => 'Terminée',
            'Annulée' => 'Annulée',
        ];

        return view('partner.orders.index', compact('orders', 'statuses'));
    }

    /**
     * Affiche les détails d'une commande spécifique avec une vérification d'accès.
     *
     * @param int $orderId
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function show($orderId)
    {
        if (!Auth::check() || !Auth::user()->partner) {
            return response()->json(['error' => 'Accès non autorisé.'], 403);
        }

        try {
            $order = Order::with(['client', 'validatedBy', 'orderItems.product'])->findOrFail($orderId);

            $partnerId = Auth::user()->partner->id;
            $myProductIds = Product::where('provenance_type', 'producteur_partenaire')
                                   ->where('provenance_id', $partnerId)
                                   ->pluck('id');

            $hasPartnerProducts = $order->orderItems->contains(function($item) use ($myProductIds) {
                return $myProductIds->contains($item->product_id);
            });

            if (!$hasPartnerProducts) {
                return response()->json(['error' => 'Accès non autorisé à cette commande'], 403);
            }
            
            return view('partner.orders.partials.show_modal', compact('order'));

        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Commande non trouvée.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
        }
    }

    /**
     * Met à jour le statut d'une commande.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Vérification de sécurité
        if (!Auth::check() || !Auth::user()->partner) {
            return redirect()->back()->with('error', 'Non autorisé.');
        }

        $partnerId = Auth::user()->partner->id;
        $hasPartnerProducts = $order->orderItems->contains(function($item) use ($partnerId) {
            return $item->product->provenance_id === $partnerId;
        });

        if (!$hasPartnerProducts) {
            return redirect()->back()->with('error', 'Commande non valide pour ce partenaire.');
        }

        $oldStatus = $order->status;
        $request->validate([
            'status' => ['required', Rule::in(['En attente de validation', 'Validée', 'En préparation', 'En livraison', 'Terminée', 'Annulée'])],
            'notes' => 'nullable|string',
        ]);
        
        $newStatus = $request->status;
        
        try {
            DB::beginTransaction();
            
            // Gestion du stock en fonction des changements de statut, en utilisant le service.
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'Terminée') {
                    // Déduction si le statut passe à "Terminée" pour la première fois
                    $this->stockService->deductStockForOrder($order);
                } elseif ($oldStatus === 'Terminée') {
                    // Remise en stock si on quitte l'état "Terminée"
                    $this->stockService->replenishStockForOrder($order);
                }
            }

            // Mise à jour de l'état de la commande
            $order->status = $newStatus;
            $order->notes = $request->notes;
            
            if ($newStatus === 'Validée') {
                $order->validated_by = Auth::id();
            }

            $order->save();
            DB::commit();

            return redirect()->route('partner.orders.index')->with('success', 'Statut de la commande mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de la commande. ' . $e->getMessage());
        }
    }
}