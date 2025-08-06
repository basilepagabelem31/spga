<div class="modal fade" id="deletePartnerProductModal{{ $partnerProduct->id }}" tabindex="-1" aria-labelledby="deletePartnerProductModalLabel{{ $partnerProduct->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deletePartnerProductModalLabel{{ $partnerProduct->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer l'association entre le partenaire **"{{ $partnerProduct->partner->establishment_name ?? 'Inconnu' }}"** et le produit **"{{ $partnerProduct->product->name ?? 'Inconnu' }}"** ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('partner_products.destroy', $partnerProduct) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
