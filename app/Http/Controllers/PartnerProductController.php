<?php

namespace App\Http\Controllers;

use App\Models\PartnerProduct;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerProductController extends Controller
{
    /**
     * Affiche la liste des associations partenaire-produit avec des options de filtrage.
     */
    public function index(Request $request)
    {
        $query = PartnerProduct::with(['partner', 'product']);

        // Filtrer par partenaire
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        // Filtrer par produit
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $partnerProducts = $query->paginate(10)->withQueryString();

        // Récupérer toutes les listes pour les filtres et les modales
        $partners = Partner::all();
        $products = Product::all();

        return view('partner_products.index', compact('partnerProducts', 'partners', 'products'));
    }

    /**
     * Affiche le formulaire pour associer un produit à un partenaire.
     */
    public function create()
    {
        $partners = Partner::all();
        $products = Product::all();
        return view('partner_products.create', compact('partners', 'products'));
    }

    /**
     * Associe un produit à un partenaire.
     */
    public function store(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'product_id' => [
                'required',
                'exists:products,id',
                // Règle d'unicité combinée pour éviter les doublons (partner_id et product_id ensemble)
                Rule::unique('partner_products')->where(function ($query) use ($request) {
                    return $query->where('partner_id', $request->partner_id);
                }),
            ],
        ]);

        PartnerProduct::create($request->all());

        return redirect()->route('partner_products.index')
                         ->with('success', 'Association partenaire-produit créée avec succès.');
    }

    /**
     * Affiche les détails d'une association spécifique.
     * (Généralement pas utilisée pour les tables pivots simples, mais maintenue pour la complétude)
     */
    public function show(PartnerProduct $partnerProduct)
    {
        $partnerProduct->load('partner', 'product');
        return view('partner_products.show', compact('partnerProduct'));
    }

    // Pas de méthodes 'edit' ou 'update' typiques pour les tables pivots simples,
    // car une association est soit créée, soit supprimée.

    /**
     * Supprime une association partenaire-produit.
     */
    public function destroy(PartnerProduct $partnerProduct)
    {
        $partnerProduct->delete();

        return redirect()->route('partner_products.index')
                         ->with('success', 'Association partenaire-produit supprimée avec succès.');
    }
}
