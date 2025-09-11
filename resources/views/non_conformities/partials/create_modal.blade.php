<div class="modal fade" id="createNonConformityModal" tabindex="-1" aria-labelledby="createNonConformityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white border-0 rounded-top-4">
                <h5 class="modal-title d-flex align-items-center" id="createNonConformityModalLabel">
                    <i class="fas fa-exclamation-triangle me-2 fa-lg"></i> Déclarer une nouvelle non-conformité
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('non_conformities.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_id" class="form-label"><i class="fas fa-box-open me-2"></i>Produit</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="" disabled selected>Sélectionner le produit concerné</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="quality_control_id" class="form-label"><i class="fas fa-clipboard-check me-2"></i>Contrôle Qualité source</label>
                            <select class="form-select" id="quality_control_id" name="quality_control_id" required>
                                <option value="" disabled selected>Lien vers le contrôle qualité</option>
                                @foreach ($qualityControls as $qc)
                                    <option value="{{ $qc->id }}">Contrôle #{{ $qc->id }} - {{ $qc->created_at->format('d/m/Y') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 mt-3">
                        <label for="description" class="form-label"><i class="fas fa-file-alt me-2"></i>Description de la non-conformité</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Décrivez la nature de la non-conformité..."></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label"><i class="fas fa-info-circle me-2"></i>Statut</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="en attente de décision" selected>En attente de décision</option>
                                <option value="rejeté">Rejeté</option>
                                <option value="reconditionné">Reconditionné</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="decision_date" class="form-label"><i class="fas fa-calendar-alt me-2"></i>Date de décision</label>
                            <input type="date" class="form-control" id="decision_date" name="decision_date">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="decision_taken_by" class="form-label"><i class="fas fa-user-shield me-2"></i>Décision prise par</label>
                        <select class="form-select" id="decision_taken_by" name="decision_taken_by">
                            <option value="">Non renseigné</option>
                            @foreach ($decisionMakers as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-between p-3 border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save me-2"></i>Enregistrer la non-conformité</button>
                </div>
            </form>
        </div>
    </div>
</div>