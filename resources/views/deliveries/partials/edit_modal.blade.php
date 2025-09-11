<div class="modal fade" id="editDeliveryModal{{ $delivery->id }}" tabindex="-1" aria-labelledby="editDeliveryModalLabel{{ $delivery->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editDeliveryModalLabel{{ $delivery->id }}">Éditer la livraison #{{ $delivery->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('deliveries.update', $delivery) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label for="edit_order_id{{ $delivery->id }}" class="form-label">Commande</label>
                        <select class="form-select rounded-pill" id="edit_order_id{{ $delivery->id }}" name="order_id" required>
                            <option value="">Sélectionnez une commande</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}" {{ $delivery->order_id == $order->id ? 'selected' : '' }}>
                                    Commande #{{ $order->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_delivery_route_id{{ $delivery->id }}" class="form-label">Tournée de livraison</label>
                        <select class="form-select rounded-pill" id="edit_delivery_route_id{{ $delivery->id }}" name="delivery_route_id" required>
                            <option value="">Sélectionnez une tournée</option>
                            @foreach ($deliveryRoutes as $route)
                                <option value="{{ $route->id }}" {{ $delivery->delivery_route_id == $route->id ? 'selected' : '' }}>
                                    Tournée du {{ $route->delivery_date->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status{{ $delivery->id }}" class="form-label">Statut</label>
                        <select class="form-select rounded-pill" id="edit_status{{ $delivery->id }}" name="status" required>
                            @foreach (['En cours', 'Terminée', 'Annulée'] as $status)
                                <option value="{{ $status }}" {{ $delivery->status == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_delivered_at{{ $delivery->id }}" class="form-label">Date de livraison (facultatif)</label>
                        <input type="datetime-local" class="form-control rounded-pill" id="edit_delivered_at{{ $delivery->id }}" name="delivered_at" value="{{ $delivery->delivered_at ? $delivery->delivered_at->format('Y-m-d\TH:i') : '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="edit_notes{{ $delivery->id }}" class="form-label">Notes (facultatif)</label>
                        <textarea class="form-control rounded-4" id="edit_notes{{ $delivery->id }}" name="notes" rows="3">{{ $delivery->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning text-white">Sauvegarder</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#edit_order_id{{ $delivery->id }}').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editDeliveryModal{{ $delivery->id }}'),
            placeholder: "Sélectionnez une commande",
            allowClear: true
        });
        $('#edit_delivery_route_id{{ $delivery->id }}').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#editDeliveryModal{{ $delivery->id }}'),
            placeholder: "Sélectionnez une tournée",
            allowClear: true
        });
    });
</script>