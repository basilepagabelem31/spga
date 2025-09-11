<div class="modal fade" id="editDeliveryRouteModal{{ $deliveryRoute->id }}" tabindex="-1" aria-labelledby="editDeliveryRouteModalLabel{{ $deliveryRoute->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="editDeliveryRouteModalLabel{{ $deliveryRoute->id }}">Éditer la tournée #{{ $deliveryRoute->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delivery-routes.update', $deliveryRoute) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label">Date de livraison</label>
                        <input type="date" class="form-control rounded-pill" id="delivery_date" name="delivery_date" value="{{ $deliveryRoute->delivery_date->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="driver_id" class="form-label">Chauffeur</label>
                        <select class="form-select rounded-pill" id="driver_id" name="driver_id" required>
                            <option value="">Sélectionnez un chauffeur</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ $deliveryRoute->driver_id == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_info" class="form-label">Informations véhicule</label>
                        <input type="text" class="form-control rounded-pill" id="vehicle_info" name="vehicle_info" value="{{ $deliveryRoute->vehicle_info }}">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select rounded-pill" id="status" name="status" required>
                            @foreach (['Planifiée', 'En cours', 'Terminée', 'Annulée'] as $status)
                                <option value="{{ $status }}" {{ $deliveryRoute->status == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
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