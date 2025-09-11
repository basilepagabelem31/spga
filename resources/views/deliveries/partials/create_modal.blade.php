<div class="modal fade" id="createDeliveryModal" tabindex="-1" aria-labelledby="createDeliveryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="createDeliveryModalLabel">Créer une nouvelle livraison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('deliveries.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label for="create_order_id" class="form-label">Commande</label>
                        <select class="form-select rounded-pill" id="create_order_id" name="order_id" required>
                            <option value="">Sélectionnez une commande</option>
                            @foreach ($orders as $order)
                                <option value="{{ $order->id }}">Commande #{{ $order->id }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create_delivery_route_id" class="form-label">Tournée de livraison</label>
                        <select class="form-select rounded-pill" id="create_delivery_route_id" name="delivery_route_id" required>
                            <option value="">Sélectionnez une tournée</option>
                            @foreach ($deliveryRoutes as $route)
                                <option value="{{ $route->id }}">Tournée du {{ $route->delivery_date->format('d/m/Y') }}  par {{ $route->driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create_status" class="form-label">Statut</label>
                        <select class="form-select rounded-pill" id="create_status" name="status" required>
                            <option value="En cours">En cours</option>
                            <option value="Terminée">Terminée</option>
                            <option value="Annulée">Annulée</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create_delivered_at" class="form-label">Date de livraison (facultatif)</label>
                        <input type="datetime-local" class="form-control rounded-pill" id="create_delivered_at" name="delivered_at">
                    </div>
                    <div class="mb-3">
                        <label for="create_notes" class="form-label">Notes (facultatif)</label>
                        <textarea class="form-control rounded-4" id="create_notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#create_order_id').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#createDeliveryModal'),
            placeholder: "Sélectionnez une commande",
            allowClear: true
        });
        $('#create_delivery_route_id').select2({
            theme: 'bootstrap-5',
            dropdownParent: $('#createDeliveryModal'),
            placeholder: "Sélectionnez une tournée",
            allowClear: true
        });
    });
</script>