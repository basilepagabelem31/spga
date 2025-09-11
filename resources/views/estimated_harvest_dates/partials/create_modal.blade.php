<div class="modal fade" id="createHarvestDateModal" tabindex="-1" aria-labelledby="createHarvestDateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createHarvestDateModalLabel">Ajouter une date de récolte estimée</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('production_follow_ups.estimated_harvest_dates.store', $productionFollowUp) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="speculation_name" class="form-label">Nom de la spéculation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="speculation_name" name="speculation_name" value="{{ old('speculation_name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="estimated_date" class="form-label">Date estimée <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="estimated_date" name="estimated_date" value="{{ old('estimated_date') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la date</button>
                </div>
            </form>
        </div>
    </div>
</div>