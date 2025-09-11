<div class="modal fade" id="createQualityControlModal" tabindex="-1" aria-labelledby="createQualityControlModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createQualityControlModalLabel">Ajouter un Contrôle Qualité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('quality_controls.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="control_date" class="form-label">Date du contrôle <span class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="control_date" name="control_date" value="{{ old('control_date') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="controller_id" class="form-label">Contrôleur <span class="text-danger">*</span></label>
                            <select class="form-select" id="controller_id" name="controller_id" required>
                                <option value="">Sélectionner un contrôleur</option>
                                @foreach($controllers as $controller)
                                    <option value="{{ $controller->id }}" {{ old('controller_id') == $controller->id ? 'selected' : '' }}>
                                        {{ $controller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_id" class="form-label">Produit <span class="text-danger">*</span></label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Sélectionner un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lot_reference" class="form-label">Référence du lot</label>
                            <input type="text" class="form-control" id="lot_reference" name="lot_reference" value="{{ old('lot_reference') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="control_type" class="form-label">Type de contrôle</label>
                            <select class="form-select" id="control_type" name="control_type">
                                <option value="">Sélectionner un type</option>
                                @foreach(['Visuel', 'Physico-chimique', 'Microbiologique', 'Poids', 'Température'] as $type)
                                    <option value="{{ $type }}" {{ old('control_type') == $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="method_used" class="form-label">Méthode utilisée</label>
                            <input type="text" class="form-control" id="method_used" name="method_used" value="{{ old('method_used') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="control_result" class="form-label">Résultat du contrôle <span class="text-danger">*</span></label>
                            <select class="form-select" id="control_result" name="control_result" required>
                                <option value="">Sélectionner un résultat</option>
                                @foreach(['Conforme', 'Non conforme', 'À réévaluer'] as $result)
                                    <option value="{{ $result }}" {{ old('control_result') == $result ? 'selected' : '' }}>
                                        {{ $result }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Signature du responsable</label>
                            <div class="border p-1 mb-2 rounded">
                                <canvas id="signature-pad-create" width="400" height="200" class="w-100"></canvas>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="clear-signature-create">Effacer la signature</button>
                            <input type="hidden" name="responsible_signature_qc" id="signature-output-create">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="observed_non_conformities" class="form-label">Non-conformités observées</label>
                        <textarea class="form-control" id="observed_non_conformities" name="observed_non_conformities" rows="3">{{ old('observed_non_conformities') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="proposed_corrective_actions" class="form-label">Actions correctives proposées</label>
                        <textarea class="form-control" id="proposed_corrective_actions" name="proposed_corrective_actions" rows="3">{{ old('proposed_corrective_actions') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le contrôle</button>
                </div>
            </form>
        </div>
    </div>
</div>