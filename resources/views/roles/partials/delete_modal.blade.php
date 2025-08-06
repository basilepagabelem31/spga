<div class="modal fade" id="deleteRoleModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteRoleModalLabel{{ $role->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRoleModalLabel{{ $role->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le rôle **{{ $role->name }}** ? Cette action est irréversible.
                @if ($role->users()->count() > 0)
                    <p class="text-danger mt-2">Attention : Ce rôle est actuellement associé à {{ $role->users()->count() }} utilisateur(s). La suppression peut entraîner des problèmes d'accès pour ces utilisateurs.</p>
                @endif
                @if ($role->permissions()->count() > 0)
                    <p class="text-danger mt-2">Attention : Ce rôle est actuellement associé à {{ $role->permissions()->count() }} permission(s). La suppression peut affecter les fonctionnalités de ces permissions.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('roles.destroy', $role) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
