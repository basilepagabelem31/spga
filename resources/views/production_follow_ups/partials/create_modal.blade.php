
<div class="modal fade" id="createProductionFollowUpModal" tabindex="-1" aria-labelledby="createProductionFollowUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> {{-- Changez la taille de la modale en xl pour plus d'espace --}}
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProductionFollowUpModalLabel">🌱 Ajouter un nouveau suivi de production</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('production_follow_ups.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3"> {{-- Utilisez g-3 pour un espacement des colonnes --}}
                        {{-- Partie 1: Informations sur le site et le personnel --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">Informations générales 📍</h6>
                            <div class="card p-3 mb-3">
                                <div class="mb-3">
                                    <label for="production_site" class="form-label">Site de production <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="production_site" name="production_site" value="{{ old('production_site') }}" required>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="commune" class="form-label">Commune</label>
                                            <input type="text" class="form-control" id="commune" name="commune" value="{{ old('commune') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="village" class="form-label">Village</label>
                                            <input type="text" class="form-control" id="village" name="village" value="{{ old('village') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="producer_name" class="form-label">Nom du producteur</label>
                                    <input type="text" class="form-control" id="producer_name" name="producer_name" value="{{ old('producer_name') }}">
                                </div>
                                <div class="mb-3">
                                    <label for="technical_agent_name" class="form-label">Agent technique</label>
                                    <input type="text" class="form-control" id="technical_agent_name" name="technical_agent_name" value="{{ old('technical_agent_name') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Partie 2: Détails de la culture --}}
                        <div class="col-md-6">
                            <h6 class="mb-3">Détails de la culture 🌱</h6>
                            <div class="card p-3 mb-3">
                                <div class="mb-3">
                                    <label for="follow_up_date" class="form-label">Date du suivi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" value="{{ old('follow_up_date', date('Y-m-d')) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="culture_name" class="form-label">Culture <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="culture_name" name="culture_name" value="{{ old('culture_name') }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="cultivated_variety" class="form-label">Variété cultivée</label>
                                    <input type="text" class="form-control" id="cultivated_variety" name="cultivated_variety" value="{{ old('cultivated_variety') }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sowing_planting_date" class="form-label">Date de semis / plantation</label>
                                            <input type="date" class="form-control" id="sowing_planting_date" name="sowing_planting_date" value="{{ old('sowing_planting_date') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cultivated_surface" class="form-label">Surface cultivée (ha)</label>
                                            <input type="number" step="0.01" class="form-control" id="cultivated_surface" name="cultivated_surface" value="{{ old('cultivated_surface') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="production_type" class="form-label">Type de production <span class="text-danger">*</span></label>
                                    <select class="form-select" id="production_type" name="production_type" required>
                                        <option value="">Sélectionner un type</option>
                                        <option value="Conventionnel" {{ old('production_type') == 'Conventionnel' ? 'selected' : '' }}>Conventionnel</option>
                                        <option value="Biologique" {{ old('production_type') == 'Biologique' ? 'selected' : '' }}>Biologique</option>
                                        <option value="Agroécologie" {{ old('production_type') == 'Agroécologie' ? 'selected' : '' }}>Agroécologie</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Partie 3: Observations et recommandations --}}
                        <div class="col-md-12">
                            <h6 class="mb-3">Observations et recommandations 📝</h6>
                            <div class="card p-3 mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="development_stage" class="form-label">Stade de développement</label>
                                            <input type="text" class="form-control" id="development_stage" name="development_stage" value="{{ old('development_stage') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="works_performed" class="form-label">Travaux réalisés</label>
                                            <textarea class="form-control" id="works_performed" name="works_performed" rows="4">{{ old('works_performed') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="technical_observations" class="form-label">Observations techniques</label>
                                            <textarea class="form-control" id="technical_observations" name="technical_observations" rows="4">{{ old('technical_observations') }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label for="recommended_interventions" class="form-label">Interventions recommandées</label>
                                            <textarea class="form-control" id="recommended_interventions" name="recommended_interventions" rows="4">{{ old('recommended_interventions') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Partie 4: Signature --}}
                        <div class="col-md-12">
                            <h6 class="mb-3">Signature du responsable ✍️</h6>
                            <div class="card p-3 mb-3">
                                <div class="mb-3">
                                    <div class="card p-2 border">
    <canvas id="signature-pad-create" class="signature-pad w-100"></canvas>
</div>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="clear-signature-create">
                                            <i class="fas fa-eraser me-1"></i> Effacer
                                        </button>
                                    </div>
                                    <input type="hidden" id="signature-output-create" name="responsible_signature">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le suivi</button>
                </div>
            </form>
        </div>
    </div>
</div>