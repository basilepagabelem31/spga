<div class="modal fade" id="createDeliveryRouteModal" tabindex="-1" aria-labelledby="createDeliveryRouteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="createDeliveryRouteModalLabel">Créer une nouvelle tournée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delivery-routes.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-0">
                    <div class="mb-3">
                        <label for="delivery_date" class="form-label">Date de la Tournée</label>
                        <input type="date" class="form-control rounded-pill" id="delivery_date" name="delivery_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="driver_id" class="form-label">Chauffeur</label>
                        <select class="form-select rounded-pill" id="driver_id" name="driver_id" required>
                            <option value="">Sélectionnez un chauffeur</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        {{-- NOTE: Pour un design comme Select2, il faudrait l'intégrer avec du JS --}}
                    </div>
                    <div class="mb-3">
                        <label for="vehicle_info" class="form-label">Informations véhicule (facultatif)</label>
                        <input type="text" class="form-control rounded-pill" id="vehicle_info" name="vehicle_info">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select rounded-pill" id="status" name="status" required>
                            <option value="Planifiée">Planifiée</option>
                            <option value="En cours">En cours</option>
                            <option value="Terminée">Terminée</option>
                            <option value="Annulée">Annulée</option>
                        </select>
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