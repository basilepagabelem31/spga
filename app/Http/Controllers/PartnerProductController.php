<?php

namespace App\Http\Controllers;

use App\Models\PartnerProduct;
use App\Models\Partner;
use App\Models\Product;
use Illuminate\Http\Request;

class PartnerProductController extends Controller
{
    /**
     * Affiche la liste des associations partenaire-produit.
     */
    public function index()
    {
        $partnerProducts = PartnerProduct::with(['partner', 'product'])->paginate(10);
        return view('partner_products.index', compact('partnerProducts'));
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
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = PartnerProduct::where('partner_id', $request->partner_id)
                                ->where('product_id', $request->product_id)
                                ->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['message' => 'Cette association existe déjà.']);
        }

        PartnerProduct::create($request->all());

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

    // Pas de méthodes 'edit' ou 'update' typiques pour les tables pivots simples.

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