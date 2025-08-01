<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Affiche la liste des permissions.
     */
    public function index()
    {
        $permissions = Permission::paginate(10);
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
        $permission->delete();

        return redirect()->route('permissions.index')
                         ->with('success', 'Permission supprimée avec succès.');
    }
}