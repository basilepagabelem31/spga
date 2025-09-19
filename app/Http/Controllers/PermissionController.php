<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des permissions avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $permissions = $query->paginate(8)->withQueryString();
        
        return view('permissions.index', compact('permissions'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle permission.
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Stocke une nouvelle permission dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_permission',
            'permissions',
            $permission->id,
            null,
            $permission->toArray()
        );

        return redirect()->route('permissions.index')
                         ->with('success', 'Permission créée avec succès.');
    }

    /**
     * Affiche les détails d'une permission spécifique.
     */
    public function show(Permission $permission)
    {
        return view('permissions.show', compact('permission'));
    }

    /**
     * Affiche le formulaire d'édition d'une permission.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));
    }

    /**
     * Met à jour une permission existante dans la base de données.
     */
    public function update(Request $request, Permission $permission)
    {
        $oldValues = $permission->toArray(); // Capture des valeurs avant la mise à jour

        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        $permission->update($request->all());
        $newValues = $permission->refresh()->toArray(); // Capture des nouvelles valeurs

        // Log de la mise à jour
        $this->recordLog(
            'mise_a_jour_permission',
            'permissions',
            $permission->id,
            $oldValues,
            $newValues
        );

        return redirect()->route('permissions.index')
                         ->with('success', 'Permission mise à jour avec succès.');
    }

    /**
     * Supprime une permission de la base de données.
     */
    public function destroy(Permission $permission)
    {
        // Optionnel: Vérifier les dépendances (si des rôles sont liés à cette permission)
        // Vous pouvez ajouter une logique ici si vous avez des contraintes de suppression.
        // Par exemple: if ($permission->roles()->count() > 0) { ... }

        $oldValues = $permission->toArray(); // Capture des valeurs avant la suppression
        $permissionId = $permission->id;

        $permission->delete();

        // Log de la suppression
        $this->recordLog(
            'suppression_permission',
            'permissions',
            $permissionId,
            $oldValues,
            null
        );

        return redirect()->route('permissions.index')
                         ->with('success', 'Permission supprimée avec succès.');
    }
}