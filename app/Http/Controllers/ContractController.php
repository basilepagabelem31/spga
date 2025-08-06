<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importez le facade Storage
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    /**
     * Affiche la liste des contrats avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Contract::with('partner');

        // Filtrer par partenaire
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        // Recherche par titre ou description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filtrer par statut (actif / expiré)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where(function($q) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', now()->toDateString());
                });
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('end_date')
                      ->where('end_date', '<', now()->toDateString());
            }
        }

        // Filtrer par date de début
        if ($request->filled('start_date_filter')) {
            $query->whereDate('start_date', '>=', $request->start_date_filter);
        }

        // Filtrer par date de fin
        if ($request->filled('end_date_filter')) {
            $query->whereDate('end_date', '<=', $request->end_date_filter);
        }

        $contracts = $query->paginate(10)->withQueryString();
        
        $partners = Partner::all();

        return view('contracts.index', compact('contracts', 'partners'));
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
        $validatedData = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Validation pour le fichier
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        // Gérer le téléchargement du fichier
        if ($request->hasFile('contract_file')) {
            $filePath = $request->file('contract_file')->store('contracts', 'public'); // Stocke dans storage/app/public/contracts
            $validatedData['file_path'] = $filePath;
        }

        Contract::create($validatedData);

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
        $validatedData = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'title' => 'required|string|max:255',
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Validation pour le fichier
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        // Gérer le remplacement du fichier
        if ($request->hasFile('contract_file')) {
            // Supprimer l'ancien fichier si un nouveau est téléchargé
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
            }
            $filePath = $request->file('contract_file')->store('contracts', 'public');
            $validatedData['file_path'] = $filePath;
        } elseif ($request->input('clear_file')) { // Si la case 'supprimer le fichier' est cochée
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
                $validatedData['file_path'] = null;
            }
        } else {
            // Conserver le chemin du fichier existant si aucun nouveau fichier n'est téléchargé et qu'il n'est pas effacé
            $validatedData['file_path'] = $contract->file_path;
        }

        $contract->update($validatedData);

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * Supprime un contrat de la base de données.
     */
    public function destroy(Contract $contract)
    {
        // Supprimer le fichier associé avant de supprimer le contrat
        if ($contract->file_path) {
            Storage::disk('public')->delete($contract->file_path);
        }

        $contract->delete();

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat supprimé avec succès.');
    }
}
