<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Partner;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

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

        $contracts = $query->paginate(8)->withQueryString();
        
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
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);

        if ($request->hasFile('contract_file')) {
            $filePath = $request->file('contract_file')->store('contracts', 'public');
            $validatedData['file_path'] = $filePath;
        }

        $contract = Contract::create($validatedData);

        // Ajout du log pour la création du contrat
        $this->recordLog(
            'creation_contrat',
            'contracts',
            $contract->id,
            null,
            $contract->toArray()
        );

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
            'contract_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
        ]);
        
        $oldValues = $contract->toArray(); // Capture des valeurs avant la mise à jour

        // Gérer le remplacement du fichier
        if ($request->hasFile('contract_file')) {
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
            }
            $filePath = $request->file('contract_file')->store('contracts', 'public');
            $validatedData['file_path'] = $filePath;
        } elseif ($request->input('clear_file')) {
            if ($contract->file_path) {
                Storage::disk('public')->delete($contract->file_path);
                $validatedData['file_path'] = null;
            }
        } else {
            $validatedData['file_path'] = $contract->file_path;
        }

        $contract->update($validatedData);

        // Ajout du log pour la mise à jour du contrat
        $newValues = $contract->refresh()->toArray();
        $this->recordLog(
            'mise_a_jour_contrat',
            'contracts',
            $contract->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat mis à jour avec succès.');
    }

    /**
     * Supprime un contrat de la base de données.
     */
    public function destroy(Contract $contract)
    {
        $oldValues = $contract->toArray(); // Capture des valeurs avant la suppression
        $contractId = $contract->id;

        // Supprimer le fichier associé avant de supprimer le contrat
        if ($contract->file_path) {
            Storage::disk('public')->delete($contract->file_path);
        }

        $contract->delete();

        // Ajout du log pour la suppression du contrat
        $this->recordLog(
            'suppression_contrat',
            'contracts',
            $contractId,
            $oldValues,
            null
        );

        return redirect()->route('contracts.index')
                         ->with('success', 'Contrat supprimé avec succès.');
    }
}