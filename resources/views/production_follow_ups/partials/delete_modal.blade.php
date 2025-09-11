<div class="modal fade" id="deleteFollowUpModal{{ $followUp->id }}" tabindex="-1" aria-labelledby="deleteFollowUpModalLabel{{ $followUp->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteFollowUpModalLabel{{ $followUp->id }}">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce suivi de production pour **{{ $followUp->production_site }}** - **{{ $followUp->culture_name }}** ? Cette action est irréversible.
                @if ($followUp->estimatedHarvestDates()->count() > 0)
                    <p class="text-danger mt-2">
                        Attention : Ce suivi est lié à **{{ $followUp->estimatedHarvestDates()->count() }}** date(s) de récolte estimée. La suppression entraînera la perte de ces informations.
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('production_follow_ups.destroy', $followUp) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>