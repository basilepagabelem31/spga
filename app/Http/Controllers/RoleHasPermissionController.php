<?php

namespace App\Http\Controllers;

use App\Models\RoleHasPermission; // Assurez-vous que ce modèle existe (voir note ci-dessous)
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleHasPermissionController extends Controller
{
    /**
     * Affiche la liste des attributions rôle-permission avec des options de filtrage.
     */
    public function index(Request $request)
    {
        $query = RoleHasPermission::with(['role', 'permission']);

        // Filtrer par rôle
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Filtrer par permission
        if ($request->filled('permission_id')) {
            $query->where('permission_id', $request->permission_id);
        }

        $roleHasPermissions = $query->paginate(10)->withQueryString();
        
        // Récupérer tous les rôles et permissions pour les menus déroulants de filtre et des modales
        $roles = Role::all();
        $permissions = Permission::all();

        return view('role_has_permissions.index', compact('roleHasPermissions', 'roles', 'permissions'));
    }

    /**
     * Affiche le formulaire de création d'une nouvelle attribution.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        return view('role_has_permissions.create', compact('roles', 'permissions'));
    }

    /**
     * Stocke une nouvelle attribution rôle-permission dans la base de données.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission_id' => 'required|exists:permissions,id',
        ]);

        // Vérifier si l'attribution existe déjà
        $exists = RoleHasPermission::where('role_id', $request->role_id)
                                  ->where('permission_id', $request->permission_id)
                                  ->exists();

        if ($exists) {
            return redirect()->back()->with('error', 'Cette attribution de permission à ce rôle existe déjà.');
        }

        RoleHasPermission::create($request->all());

        return redirect()->route('role_has_permissions.index')
                         ->with('success', 'Permission attribuée au rôle avec succès.');
    }

    /**
     * Affiche les détails d'une attribution spécifique (redirection vers l'index).
     */
    public function show($role_id, $permission_id)
    {
        // Pour une table pivot, la vue "show" est rarement utile.
        // On redirige généralement vers la liste avec un message si besoin.
        $assignment = RoleHasPermission::where('role_id', $role_id)
                                       ->where('permission_id', $permission_id)
                                       ->first();
        if (!$assignment) {
            return redirect()->route('role_has_permissions.index')->with('error', 'Attribution non trouvée.');
        }
        return redirect()->route('role_has_permissions.index')->with('info', "Détails de l'attribution : Rôle '{$assignment->role->name}' a la permission '{$assignment->permission->name}'.");
    }

    /**
     * Supprime une attribution rôle-permission de la base de données.
     */
    public function destroy($role_id, $permission_id)
    {
        $deleted = RoleHasPermission::where('role_id', $role_id)
                                    ->where('permission_id', $permission_id)
                                    ->delete();

        if ($deleted) {
            return redirect()->route('role_has_permissions.index')
                             ->with('success', 'Attribution de permission supprimée avec succès.');
        }

        return redirect()->route('role_has_permissions.index')
                         ->with('error', 'Attribution non trouvée ou impossible à supprimer.');
    }
}
