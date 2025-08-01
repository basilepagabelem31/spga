<?php

namespace App\Http\Controllers;

use App\Models\Partner;
use App\Models\User; // Pour l'association avec un utilisateur existant
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class PartnerController extends Controller
{
    /**
     * Affiche la liste des partenaires.
     */
    public function index()
    {
        $partners = Partner::with('user')->paginate(10);
        return view('partners.index', compact('partners'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau partenaire.
     */
    public function create()
    {
        $users = User::all(); // Vous pouvez filtrer les utilisateurs qui peuvent être des partenaires si nécessaire
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

        Partner::create($request->all());

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
        $users = User::all();
        return view('partners.edit', compact('partner', 'users'));
    }

    /**
     * Met à jour un partenaire existant dans la base de données.
     */
    public function update(Request $request, Partner $partner)
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

        $partner->update($request->all());

        return redirect()->route('partners.index')
                         ->with('success', 'Partenaire mis à jour avec succès.');
    }

    /**
     * Supprime un partenaire de la base de données.
     */
    public function destroy(Partner $partner)
    {
        // Optionnel: Vérifier les dépendances
        if ($partner->contracts()->count() > 0 || $partner->products()->count() > 0) {
            return redirect()->route('partners.index')
                             ->with('error', 'Impossible de supprimer ce partenaire car il est lié à des contrats ou des produits.');
        }

        $partner->delete();

        return redirect()->route('partners.index')
                         ->with('success', 'Partenaire supprimé avec succès.');
    }
}