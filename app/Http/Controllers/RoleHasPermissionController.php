<?php

namespace App\Http\Controllers;

use App\Models\RoleHasPermission;
use App\Models\Role;
use App\Models\Permission;
use App\Traits\LogsActivity; // Ajout de l'importation du trait
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Ajouté pour le débogage si nécessaire

class RoleHasPermissionController extends Controller
{
    use LogsActivity; // Utilisation du trait pour le logging

    /**
     * Affiche la liste des attributions rôle-permission avec des options de filtrage.
     */
    public function index(Request $request)
    {
        $query = RoleHasPermission::with(['role', 'permission']);

        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('permission_id')) {
            $query->where('permission_id', $request->permission_id);
        }

        $roleHasPermissions = $query->paginate(8)->withQueryString();
        
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

        $exists = RoleHasPermission::where('role_id', $request->role_id)
                                 ->where('permission_id', $request->permission_id)
                                 ->exists();

        if ($exists) {
            // Log de l'échec de la création
            $this->recordLog(
                'echec_creation_attribution_role_permission',
                'role_has_permissions',
                null,
                ['error' => 'Attribution déjà existante', 'role_id' => $request->role_id, 'permission_id' => $request->permission_id],
                null
            );
            return redirect()->back()->with('error', 'Cette attribution de permission à ce rôle existe déjà.');
        }

        $assignment = RoleHasPermission::create($request->all());

        // Log de la création
        $this->recordLog(
            'creation_attribution_role_permission',
            'role_has_permissions',
            $assignment->id,
            null,
            $assignment->toArray()
        );

        return redirect()->route('role_has_permissions.index')
                         ->with('success', 'Permission attribuée au rôle avec succès.');
    }

    /**
     * Affiche les détails d'une attribution spécifique (redirection vers l'index).
     */
    public function show($role_id, $permission_id)
    {
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
        $assignment = RoleHasPermission::where('role_id', $role_id)
                                       ->where('permission_id', $permission_id)
                                       ->first();

        if (!$assignment) {
            // Log de l'échec de la suppression
            $this->recordLog(
                'echec_suppression_attribution_role_permission',
                'role_has_permissions',
                null,
                ['error' => 'Attribution non trouvée', 'role_id' => $role_id, 'permission_id' => $permission_id],
                null
            );
            return redirect()->route('role_has_permissions.index')
                             ->with('error', 'Attribution non trouvée ou impossible à supprimer.');
        }

        $oldValues = $assignment->toArray(); // Capture des valeurs avant la suppression
        $assignmentId = $assignment->id;

        $deleted = $assignment->delete();

        if ($deleted) {
            // Log de la suppression
            $this->recordLog(
                'suppression_attribution_role_permission',
                'role_has_permissions',
                $assignmentId,
                $oldValues,
                null
            );
            return redirect()->route('role_has_permissions.index')
                             ->with('success', 'Attribution de permission supprimée avec succès.');
        }

        // Cas de figure improbable, mais pour la complétude
        $this->recordLog(
            'echec_suppression_attribution_role_permission',
            'role_has_permissions',
            $assignmentId,
            ['error' => 'Erreur inconnue lors de la suppression'],
            null
        );

        return redirect()->route('role_has_permissions.index')
                         ->with('error', 'Attribution non trouvée ou impossible à supprimer.');
    }
}