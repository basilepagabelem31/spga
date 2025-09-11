<div class="modal fade" id="deleteDeliveryRouteModal{{ $deliveryRoute->id }}" tabindex="-1" aria-labelledby="deleteDeliveryRouteModalLabel{{ $deliveryRoute->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="deleteDeliveryRouteModalLabel{{ $deliveryRoute->id }}">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer la tournée de livraison du <strong>{{ $deliveryRoute->delivery_date->format('d/m/Y') }}</strong> ? Cette action est irréversible.
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('delivery-routes.destroy', $deliveryRoute) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>