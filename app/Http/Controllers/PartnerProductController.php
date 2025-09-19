<?php

namespace App\Http\Controllers;

use App\Models\PartnerProduct;
use App\Models\Partner;
use App\Models\Product;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PartnerProductController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des associations partenaire-produit avec des options de filtrage.
     */
    public function index(Request $request)
    {
        $query = PartnerProduct::with(['partner', 'product']);

        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $partnerProducts = $query->paginate(8)->withQueryString();

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
                Rule::unique('partner_products')->where(function ($query) use ($request) {
                    return $query->where('partner_id', $request->partner_id);
                }),
            ],
        ]);

        $partnerProduct = PartnerProduct::create($request->all());

        // Log de la création de l'association
        $this->recordLog(
            'creation_association_partenaire_produit',
            'partner_products',
            $partnerProduct->id,
            null,
            $partnerProduct->toArray()
        );

        return redirect()->route('partner_products.index')
                         ->with('success', 'Association partenaire-produit créée avec succès.');
    }

    /**
     * Affiche les détails d'une association spécifique.
     */
    public function show(PartnerProduct $partnerProduct)
    {
        $partnerProduct->load('partner', 'product');
        return view('partner_products.show', compact('partnerProduct'));
    }

    /**
     * Supprime une association partenaire-produit.
     */
    public function destroy(PartnerProduct $partnerProduct)
    {
        $oldValues = $partnerProduct->toArray(); // Capture des valeurs avant la suppression
        $partnerProductId = $partnerProduct->id;

        $partnerProduct->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_association_partenaire_produit',
            'partner_products',
            $partnerProductId,
            $oldValues,
            null
        );

        return redirect()->route('partner_products.index')
                         ->with('success', 'Association partenaire-produit supprimée avec succès.');
    }
}