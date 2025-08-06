<div class="modal fade" id="deletePartnerModal{{ $partner->id }}" tabindex="-1" aria-labelledby="deletePartnerModalLabel{{ $partner->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePartnerModalLabel{{ $partner->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le partenaire **{{ $partner->establishment_name }}** ? Cette action est irréversible.
                @if ($partner->contracts()->count() > 0 || $partner->products()->count() > 0)
                    <p class="text-danger mt-2">Attention : Ce partenaire est lié à des {{ $partner->contracts()->count() }} contrats et/ou {{ $partner->products()->count() }} produits. La suppression peut entraîner des incohérences.</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('partners.destroy', $partner) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
