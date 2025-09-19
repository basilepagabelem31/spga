<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class RoleController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des rôles avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $roles = $query->paginate(8)->withQueryString();
        
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

        $role = Role::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_role',
            'roles',
            $role->id,
            null,
            $role->toArray()
        );

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
        $oldValues = $role->toArray(); // Capture des valeurs avant la mise à jour

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->all());
        $newValues = $role->refresh()->toArray(); // Capture des nouvelles valeurs

        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_role',
            'roles',
            $role->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('roles.index')
                         ->with('success', 'Rôle mis à jour avec succès.');
    }

    /**
     * Supprime un rôle de la base de données.
     */
    public function destroy(Role $role)
    {
        $oldValues = $role->toArray(); // Capture des valeurs avant la suppression
        $roleId = $role->id;

        try {
            if ($role->users()->count() > 0) {
                $this->recordLog(
                    'echec_suppression_role',
                    'roles',
                    $roleId,
                    ['error' => 'Le rôle est associé à des utilisateurs'],
                    null
                );
                return redirect()->route('roles.index')->with('error', 'Impossible de supprimer ce rôle car il est associé à des utilisateurs.');
            }

            if ($role->permissions()->count() > 0) {
                $this->recordLog(
                    'echec_suppression_role',
                    'roles',
                    $roleId,
                    ['error' => 'Le rôle est associé à des permissions'],
                    null
                );
                return redirect()->route('roles.index')->with('error', 'Impossible de supprimer ce rôle car il est associé à des permissions.');
            }

            $role->delete();

            $this->recordLog(
                'suppression_role',
                'roles',
                $roleId,
                $oldValues,
                null
            );

            return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès !');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->recordLog(
                'echec_suppression_role',
                'roles',
                $roleId,
                ['error' => 'Erreur de base de données', 'exception' => $e->getMessage()],
                null
            );
            return redirect()->route('roles.index')->with('error', 'Une erreur est survenue lors de la suppression du rôle.');
        }
    }
}