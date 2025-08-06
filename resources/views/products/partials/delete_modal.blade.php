<div class="modal fade" id="deleteProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteProductModalLabel{{ $product->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteProductModalLabel{{ $product->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer le produit **{{ $product->name }}** ? Cette action est irréversible.
                @if ($product->orderItems()->count() > 0)
                    <p class="text-danger mt-2">Attention : Ce produit est actuellement lié à {{ $product->orderItems()->count() }} article(s) de commande.</p>
                @endif
                @if ($product->stocks()->count() > 0)
                    <p class="text-danger mt-2">Attention : Ce produit est actuellement lié à {{ $product->stocks()->count() }} enregistrement(s) de stock.</p>
                @endif
                {{-- Vous pouvez ajouter d'autres vérifications ici si nécessaire (ex: qualityControls, nonConformities) --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('products.destroy', $product) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
