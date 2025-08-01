<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StockController extends Controller
{
    /**
     * Affiche la liste des stocks.
     */
    public function index()
    {
        $stocks = Stock::with('product')->paginate(10);
        return view('stocks.index', compact('stocks'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau mouvement de stock.
     */
    public function create()
    {
        $products = Product::all();
        return view('stocks.create', compact('products'));
    }

    /**
     * Stocke un nouveau mouvement de stock dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'movement_type' => ['required', Rule::in(['entrée', 'sortie', 'future_recolte'])],
            'reference_id' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0',
            'movement_date' => 'nullable|date',
        ]);

        Stock::create($request->all());

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock créé avec succès.');
    }

    /**
     * Affiche les détails d'un mouvement de stock spécifique.
     */
    public function show(Stock $stock)
    {
        $stock->load('product');
        return view('stocks.show', compact('stock'));
    }

    /**
     * Affiche le formulaire d'édition d'un mouvement de stock.
     */
    public function edit(Stock $stock)
    {
        $products = Product::all();
        return view('stocks.edit', compact('stock', 'products'));
    }

    /**
     * Met à jour un mouvement de stock existant dans la base de données.
     */
    public function update(Request $request, Stock $stock)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0',
            'movement_type' => ['required', Rule::in(['entrée', 'sortie', 'future_recolte'])],
            'reference_id' => 'nullable|string|max:255',
            'alert_threshold' => 'nullable|numeric|min:0',
            'movement_date' => 'nullable|date',
        ]);

        $stock->update($request->all());

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock mis à jour avec succès.');
    }

    /**
     * Supprime un mouvement de stock de la base de données.
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return redirect()->route('stocks.index')
                         ->with('success', 'Mouvement de stock supprimé avec succès.');
    }
}