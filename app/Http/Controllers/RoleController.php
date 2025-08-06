<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User; // Pour vérifier les utilisateurs associés lors de la suppression
use App\Models\Permission; // Pour vérifier les permissions associées lors de la suppression
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Affiche la liste des rôles avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Role::query(); // Démarre une nouvelle requête Eloquent

        // Recherche par nom ou description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $roles = $query->paginate(10)->withQueryString();
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau rôle.
     */
    public function create()
    {
        return view('roles.create');
    }

    /**
     * Stocke un nouveau rôle dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string',
        ]);

        Role::create($request->all());

        return redirect()->route('roles.index')
                         ->with('success', 'Rôle créé avec succès.');
    }

    /**
     * Affiche les détails d'un rôle spécifique.
     */
    public function show(Role $role)
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Affiche le formulaire d'édition d'un rôle.
     */
    public function edit(Role $role)
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Met à jour un rôle existant dans la base de données.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->all());

        return redirect()->route('roles.index')
                         ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un rôle de la base de données.
     */
    public function destroy(Role $role)
    {
        try {
            // Vérifier si des utilisateurs sont associés à ce rôle
            if ($role->users()->count() > 0) {
                return redirect()->route('roles.index')->with('error', 'Impossible de supprimer ce rôle car il est associé à des utilisateurs.');
            }

            // Vérifier si des permissions sont associées à ce rôle
            if ($role->permissions()->count() > 0) {
                return redirect()->route('roles.index')->with('error', 'Impossible de supprimer ce rôle car il est associé à des permissions.');
            }

            $role->delete();
            return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès !');
        } catch (\Illuminate\Database\QueryException $e) {
            // Gérer les erreurs de base de données génériques
            return redirect()->route('roles.index')->with('error', 'Une erreur est survenue lors de la suppression du rôle.');
        }
    }
}
