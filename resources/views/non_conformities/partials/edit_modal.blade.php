<div class="modal fade" id="editNonConformityModal{{ $nonConformity->id }}" tabindex="-1" aria-labelledby="editNonConformityModalLabel{{ $nonConformity->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-warning text-dark border-0 rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="editNonConformityModalLabel{{ $nonConformity->id }}">
                    <i class="fas fa-edit me-2 fa-lg"></i> Modifier la non-conformité
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('non_conformities.update', $nonConformity) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_product_id_{{ $nonConformity->id }}" class="form-label"><i class="fas fa-box-open me-2"></i>Produit</label>
                            <select class="form-select" id="edit_product_id_{{ $nonConformity->id }}" name="product_id" required>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}" {{ $nonConformity->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_quality_control_id_{{ $nonConformity->id }}" class="form-label"><i class="fas fa-clipboard-check me-2"></i>Contrôle Qualité source</label>
                            <select class="form-select" id="edit_quality_control_id_{{ $nonConformity->id }}" name="quality_control_id" required>
                                @foreach ($qualityControls as $qc)
                                    <option value="{{ $qc->id }}" {{ $nonConformity->quality_control_id == $qc->id ? 'selected' : '' }}>Contrôle #{{ $qc->id }} - {{ $qc->created_at->format('d/m/Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="edit_description_{{ $nonConformity->id }}" class="form-label"><i class="fas fa-file-alt me-2"></i>Description de la non-conformité</label>
                        <textarea class="form-control" id="edit_description_{{ $nonConformity->id }}" name="description" rows="3" placeholder="Décrivez la nature de la non-conformité...">{{ $nonConformity->description }}</textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_status_{{ $nonConformity->id }}" class="form-label"><i class="fas fa-info-circle me-2"></i>Statut</label>
                            <select class="form-select" id="edit_status_{{ $nonConformity->id }}" name="status" required>
                                <option value="en attente de décision" {{ $nonConformity->status == 'en attente de décision' ? 'selected' : '' }}>En attente de décision</option>
                                <option value="rejeté" {{ $nonConformity->status == 'rejeté' ? 'selected' : '' }}>Rejeté</option>
                                <option value="reconditionné" {{ $nonConformity->status == 'reconditionné' ? 'selected' : '' }}>Reconditionné</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_decision_date_{{ $nonConformity->id }}" class="form-label"><i class="fas fa-calendar-alt me-2"></i>Date de décision</label>
                            <input type="date" class="form-control" id="edit_decision_date_{{ $nonConformity->id }}" name="decision_date" value="{{ $nonConformity->decision_date ? $nonConformity->decision_date->format('Y-m-d') : '' }}">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="edit_decision_taken_by_{{ $nonConformity->id }}" class="form-label"><i class="fas fa-user-shield me-2"></i>Décision prise par</label>
                        <select class="form-select" id="edit_decision_taken_by_{{ $nonConformity->id }}" name="decision_taken_by">
                            <option value="">Non renseigné</option>
                            @foreach ($decisionMakers as $user)
                                <option value="{{ $user->id }}" {{ $nonConformity->decision_taken_by == $user->id ? 'selected' : '' }}>{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between p-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning shadow-sm"><i class="fas fa-sync-alt me-2"></i>Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>