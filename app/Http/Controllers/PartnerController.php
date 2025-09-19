<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\User; // Pour l'association avec un utilisateur existant
use App\Models\Role; // Assurez-vous d'importer le modèle Role
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class PartnerController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des partenaires avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Partner::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('locality_region')) {
            $query->where('locality_region', 'like', '%' . $request->locality_region . '%');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('establishment_name', 'like', '%' . $search . '%')
                  ->orWhere('contact_name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        $partners = $query->paginate(8)->withQueryString();
        
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'partenaire');
        })->get();

        $partnerTypes = Partner::select('type')->distinct()->pluck('type');

        return view('partners.index', compact('partners', 'users', 'partnerTypes'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau partenaire.
     */
    public function create()
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'partenaire');
        })->get();
        return view('partners.create', compact('users'));
    }

    /**
     * Stocke un nouveau partenaire dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'establishment_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'function' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'locality_region' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(['Producteur individuel', 'Coopérative agricole/maraîchère', 'Ferme partenaire'])],
            'years_of_experience' => 'nullable|integer|min:0',
        ]);

        $partner = Partner::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_partenaire',
            'partners',
            $partner->id,
            null,
            $partner->toArray()
        );

        return redirect()->route('partners.index')
                         ->with('success', 'Partenaire créé avec succès.');
    }

    /**
     * Affiche les détails d'un partenaire spécifique.
     */
    public function show(Partner $partner)
    {
        $partner->load('user', 'products', 'contracts');
        return view('partners.show', compact('partner'));
    }

    /**
     * Affiche le formulaire d'édition d'un partenaire.
     */
    public function edit(Partner $partner)
    {
        $users = User::whereHas('role', function ($q) {
            $q->where('name', 'partenaire');
        })->get();
        return view('partners.edit', compact('partner', 'users'));
    }

    /**
     * Met à jour un partenaire existant dans la base de données.
     */
    public function update(Request $request, Partner $partner)
    {
        $oldValues = $partner->toArray(); // Capture des valeurs avant la mise à jour

        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'establishment_name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'function' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'locality_region' => 'nullable|string|max:255',
            'type' => ['required', Rule::in(['Producteur individuel', 'Coopérative agricole/maraîchère', 'Ferme partenaire'])],
            'years_of_experience' => 'nullable|integer|min:0',
        ]);

        $partner->update($request->all());
        $newValues = $partner->refresh()->toArray(); // Capture des nouvelles valeurs
        
        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_partenaire',
            'partners',
            $partner->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('partners.index')
                         ->with('success', 'Partenaire mis à jour avec succès.');
    }

    /**
     * Supprime un partenaire de la base de données.
     */
    public function destroy(Partner $partner)
    {
        // Vérifier les dépendances
        if ($partner->contracts()->count() > 0 || $partner->products()->count() > 0) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_partenaire',
                'partners',
                $partner->id,
                ['error' => 'Partenaire lié à des contrats ou des produits'],
                null
            );

            return redirect()->route('partners.index')
                             ->with('error', 'Impossible de supprimer ce partenaire car il est lié à des contrats ou des produits.');
        }

        $oldValues = $partner->toArray(); // Capture des valeurs avant la suppression
        $partnerId = $partner->id;

        $partner->delete();
        
        // Log de la suppression
        $this->recordLog(
            'suppression_partenaire',
            'partners',
            $partnerId,
            $oldValues,
            null
        );

        return redirect()->route('partners.index')
                         ->with('success', 'Partenaire supprimé avec succès.');
    }
}