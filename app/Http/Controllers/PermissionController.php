<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Affiche la liste des permissions avec des options de filtrage et de recherche.
     */
    public function index(Request $request)
    {
        $query = Permission::query(); // Démarre une nouvelle requête Eloquent

        // Recherche par nom ou description
        // Si le paramètre 'search' est présent dans la requête HTTP,
        // ajoute des conditions OR WHERE pour rechercher la chaîne dans ces colonnes.
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Exécute la requête, pagine les résultats (10 par page)
        // et ajoute les paramètres de la requête actuelle à l'URL de pagination.
        $permissions = $query->paginate(10)->withQueryString();
        
        // Retourne la vue 'permissions.index' en passant les permissions paginées.
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

        Permission::create($request->all());

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
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string',
        ]);

        $permission->update($request->all());

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

        $permission->delete();

        return redirect()->route('permissions.index')
                         ->with('success', 'Permission supprimée avec succès.');
    }
}
