<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\StockService;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartnerOrdersController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    protected $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Affiche la liste des commandes du partenaire avec filtres.
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->partner) {
            // Log de l'échec de l'accès (non partenaire)
            $this->recordLog(
                'echec_acces_liste_commandes_partenaire',
                null,
                null,
                ['error' => 'Accès non autorisé', 'user_id' => Auth::id()],
                null
            );
            abort(403, 'Accès non autorisé. Vous devez être un partenaire pour voir cette page.');
        }

        $partnerId = Auth::user()->partner->id;
        
        // Log de l'accès à la liste des commandes
        $this->recordLog(
            'acces_liste_commandes_partenaire',
            null,
            null,
            ['partner_id' => $partnerId, 'filters' => $request->all()],
            null
        );

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

        $orders = $query->latest()->paginate(8)->withQueryString();

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
     */
    public function show($orderId)
    {
        if (!Auth::check() || !Auth::user()->partner) {
            // Log de l'échec de l'accès (non partenaire)
            $this->recordLog(
                'echec_consultation_commande_partenaire',
                'orders',
                $orderId,
                ['error' => 'Accès non autorisé', 'user_id' => Auth::id()],
                null
            );
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
                // Log de l'échec de l'accès (commande n'appartient pas au partenaire)
                $this->recordLog(
                    'echec_consultation_commande_partenaire',
                    'orders',
                    $orderId,
                    ['error' => 'Commande non valide pour ce partenaire', 'partner_id' => $partnerId],
                    null
                );
                return response()->json(['error' => 'Accès non autorisé à cette commande'], 403);
            }
            
            // Log de la consultation de la commande
            $this->recordLog(
                'consultation_commande_partenaire',
                'orders',
                $orderId,
                null,
                null
            );

            return view('partner.orders.partials.show_modal', compact('order'));

        } catch (ModelNotFoundException $e) {
            $this->recordLog(
                'echec_consultation_commande_partenaire',
                'orders',
                $orderId,
                ['error' => 'Commande non trouvée'],
                null
            );
            return response()->json(['error' => 'Commande non trouvée.'], 404);
        } catch (\Exception $e) {
            Log::error('Erreur interne lors de la consultation de la commande: ' . $e->getMessage());
            $this->recordLog(
                'echec_consultation_commande_partenaire',
                'orders',
                $orderId,
                ['error' => 'Erreur interne', 'exception' => $e->getMessage()],
                null
            );
            return response()->json(['error' => 'Une erreur interne est survenue.'], 500);
        }
    }

    /**
     * Met à jour le statut d'une commande.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!Auth::check() || !Auth::user()->partner) {
            $this->recordLog(
                'echec_mise_a_jour_commande_partenaire',
                'orders',
                $order->id,
                ['error' => 'Accès non autorisé', 'user_id' => Auth::id()],
                null
            );
            return redirect()->back()->with('error', 'Non autorisé.');
        }

        $partnerId = Auth::user()->partner->id;
        $hasPartnerProducts = $order->orderItems->contains(function($item) use ($partnerId) {
            return $item->product->provenance_id === $partnerId;
        });

        if (!$hasPartnerProducts) {
            $this->recordLog(
                'echec_mise_a_jour_commande_partenaire',
                'orders',
                $order->id,
                ['error' => 'Commande non valide pour ce partenaire', 'partner_id' => $partnerId],
                null
            );
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
            
            if ($oldStatus !== $newStatus) {
                if ($newStatus === 'Terminée') {
                    $this->stockService->deductStockForOrder($order);
                } elseif ($oldStatus === 'Terminée') {
                    $this->stockService->replenishStockForOrder($order);
                }
            }

            $order->status = $newStatus;
            $order->notes = $request->notes;
            
            if ($newStatus === 'Validée') {
                $order->validated_by = Auth::id();
            }

            $order->save();
            DB::commit();

            // Log de la mise à jour réussie
            $this->recordLog(
                'mise_a_jour_statut_commande_partenaire',
                'orders',
                $order->id,
                ['old_status' => $oldStatus],
                ['new_status' => $newStatus, 'notes' => $request->notes]
            );

            return redirect()->route('partner.orders.index')->with('success', 'Statut de la commande mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log de l'échec de la mise à jour (erreur de transaction)
            $this->recordLog(
                'echec_mise_a_jour_commande_partenaire',
                'orders',
                $order->id,
                ['error' => 'Erreur de base de données', 'exception' => $e->getMessage()],
                $request->all()
            );
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour de la commande. ' . $e->getMessage());
        }
    }
}