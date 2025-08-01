<?php

namespace App\Http\Controllers;

use App\Models\RoleHasPermission;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleHasPermissionController extends Controller
{
    /**
     * Affiche la liste des associations rôle-permission.
     */
    public function index()
    {
        $roleHasPermissions = RoleHasPermission::with(['role', 'permission'])->paginate(10);
        return view('role_has_permissions.index', compact('roleHasPermissions'));
    }

    /**
     * Affiche le formulaire pour associer un rôle à une permission.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('role_has_permissions.create', compact('roles', 'permissions'));
    }

    /**
     * Associe un rôle à une permission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        // Vérifier si l'association existe déjà
        $exists = RoleHasPermission::where('role_id', $request->role_id)
                                  ->where('permission_id', $request->permission_id)
                                  ->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['message' => 'Cette association existe déjà.']);
        }

        RoleHasPermission::create($request->all());

        return redirect()->route('role_has_permissions.index')
                         ->with('success', 'Association créée avec succès.');
    }

    /**
     * Affiche les détails d'une association spécifique (peut être combiné pour role_id et permission_id).
     * Note: Laravel ne gère pas directement les clés primaires composites dans la résolution de modèle par défaut comme celle-ci.
     * Vous devrez peut-être ajuster la route et la logique pour passer les deux IDs.
     */
    public function show($role_id, $permission_id)
    {
        $roleHasPermission = RoleHasPermission::where('role_id', $role_id)
                                             ->where('permission_id', $permission_id)
                                             ->with(['role', 'permission'])
                                             ->firstOrFail();
        return view('role_has_permissions.show', compact('roleHasPermission'));
    }

    // Pas de méthodes 'edit' ou 'update' typiques pour les tables pivots simples car l'association est binaire.
    // Il s'agit généralement de créer ou supprimer l'association.

    /**
     * Supprime une association rôle-permission.
     */
    public function destroy($role_id, $permission_id)
    {
        RoleHasPermission::where('role_id', $role_id)
                         ->where('permission_id', $permission_id)
                         ->delete();

        return redirect()->route('role_has_permissions.index')
                         ->with('success', 'Association supprimée avec succès.');
    }
}