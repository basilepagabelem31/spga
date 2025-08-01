<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Partner;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * Affiche la liste des contrats.
     */
    public function index()
    {
        $contracts = Contract::with('partner')->paginate(10);
        return view('contracts.index', compact('contracts'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau contrat.
     */
    public function create()
    {
        $partners = Partner::all();
        return view('contracts.create', compact('partners'));
    }

    /**
     * Stocke un nouveau contrat dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'file_path' => 'nullable|string|max:255', // Gérer le téléchargement de fichiers ici
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        Contract::create($request->all());

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat créé avec succès.');
    }

    /**
     * Affiche les détails d'un contrat spécifique.
     */
    public function show(Contract $contract)
    {
        $contract->load('partner');
        return view('contracts.show', compact('contract'));
    }

    /**
     * Affiche le formulaire d'édition d'un contrat.
     */
    public function edit(Contract $contract)
    {
        $partners = Partner::all();
        return view('contracts.edit', compact('contract', 'partners'));
    }

    /**
     * Met à jour un contrat existant dans la base de données.
     */
    public function update(Request $request, Contract $contract)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'file_path' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        $contract->update($request->all());

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * Supprime un contrat de la base de données.
     */
    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat supprimé avec succès.');
    }
}