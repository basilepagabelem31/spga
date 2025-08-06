<div class="modal fade" id="deleteRolePermissionModal{{ $assignment->role_id }}-{{ $assignment->permission_id }}" tabindex="-1" aria-labelledby="deleteRolePermissionModalLabel{{ $assignment->role_id }}-{{ $assignment->permission_id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRolePermissionModalLabel{{ $assignment->role_id }}-{{ $assignment->permission_id }}">Confirmer la suppression de l'attribution</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer l'attribution de la permission **"{{ $assignment->permission->name ?? 'Permission inconnue' }}"** au rôle **"{{ $assignment->role->name ?? 'Rôle inconnu' }}"** ?
                Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('role_has_permissions.destroy', ['role_id' => $assignment->role_id, 'permission_id' => $assignment->permission_id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer l'attribution</button>
                </form>
            </div>
        </div>
    </div>
</div>
