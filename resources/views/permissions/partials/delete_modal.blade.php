<div class="modal fade" id="deletePermissionModal{{ $permission->id }}" tabindex="-1" aria-labelledby="deletePermissionModalLabel{{ $permission->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePermissionModalLabel{{ $permission->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer la permission **{{ $permission->name }}** ? Cette action est irréversible.
                @if ($permission->roles()->count() > 0)
                    <p class="text-danger mt-2">Attention : Cette permission est actuellement associée à {{ $permission->roles()->count() }} rôle(s). La suppression peut affecter les fonctionnalités de ces rôles.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('permissions.destroy', $permission) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
