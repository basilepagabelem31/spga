<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Affiche la liste des rôles.
     */
    public function index()
    {
        $roles = Role::paginate(10); // Affiche 10 rôles par page
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
            $role->delete();
            return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès !');
        } catch (\Illuminate\Database\QueryException $e) {
            // Gérer le cas où le rôle est utilisé (clé étrangère)
            if ($e->getCode() == "23000") { // Code d'erreur pour intégrité référentielle
                return redirect()->route('roles.index')->with('error', 'Impossible de supprimer ce rôle car il est associé à des utilisateurs ou des permissions.');
            }
            return redirect()->route('roles.index')->with('error', 'Une erreur est survenue lors de la suppression du rôle.');
        }
    }
}