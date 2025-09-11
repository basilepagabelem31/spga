<div class="modal fade" id="deleteHarvestDateModal{{ $estimatedHarvestDate->id }}" tabindex="-1" aria-labelledby="deleteHarvestDateModalLabel{{ $estimatedHarvestDate->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteHarvestDateModalLabel{{ $estimatedHarvestDate->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer la date de récolte estimée du **{{ $estimatedHarvestDate->estimated_date->format('d/m/Y') }}** pour ce suivi ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('production_follow_ups.estimated_harvest_dates.destroy', [$productionFollowUp, $estimatedHarvestDate]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>