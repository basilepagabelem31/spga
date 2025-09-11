<div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel{{ $product->id }}">Modifier le produit : {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id_edit_{{ $product->id }}" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id_edit_{{ $product->id }}" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="provenance_type_edit_{{ $product->id }}" class="form-label">Type de provenance <span class="text-danger">*</span></label>
                            <select class="form-select" id="provenance_type_edit_{{ $product->id }}" name="provenance_type" required>
                                <option value="">Sélectionner le type</option>
                                <option value="ferme_propre" @selected(old('provenance_type', $product->provenance_type) == 'ferme_propre')>Ferme Propre</option>
                                <option value="producteur_partenaire" @selected(old('provenance_type', $product->provenance_type) == 'producteur_partenaire')>Producteur Partenaire</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="provenance_id_group_edit_{{ $product->id }}" style="display: none;"> {{-- Initialement caché --}}
                        <label for="provenance_id_edit_{{ $product->id }}" class="form-label">Partenaire (si producteur partenaire) <span class="text-danger provenance-required-star">*</span></label>
                        <select class="form-select" id="provenance_id_edit_{{ $product->id }}" name="provenance_id">
                            <option value="">Sélectionner un partenaire</option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}" @selected(old('provenance_id', $product->provenance_id) == $partner->id)>
                                    {{ $partner->establishment_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="production_mode" class="form-label">Mode de production <span class="text-danger">*</span></label>
                            <select class="form-select" id="production_mode" name="production_mode" required>
                                <option value="">Sélectionner le mode</option>
                                <option value="bio" @selected(old('production_mode', $product->production_mode) == 'bio')>Bio</option>
                                <option value="agroécologie" @selected(old('production_mode', $product->production_mode) == 'agroécologie')>Agroécologie</option>
                                <option value="conventionnel" @selected(old('production_mode', $product->production_mode) == 'conventionnel')>Conventionnel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="packaging_format" class="form-label">Format d'emballage</label>
                            <input type="text" class="form-control" id="packaging_format" name="packaging_format" value="{{ old('packaging_format', $product->packaging_format) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="min_order_quantity" class="form-label">Quantité min. commande</label>
                            <input type="number" step="0.01" class="form-control" id="min_order_quantity" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_price" class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sale_unit" class="form-label">Unité de vente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sale_unit" name="sale_unit" value="{{ old('sale_unit', $product->sale_unit) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="product_image_edit_{{ $product->id }}" class="form-label">Image du produit</label>
                        <input type="file" class="form-control" id="product_image_edit_{{ $product->id }}" name="product_image">
                        <small class="form-text text-muted">Formats acceptés : JPG, JPEG, PNG, GIF. Taille max : 2MB.</small>
                        
                        @if ($product->image)
                            <div class="mt-2">
                                <small class="form-text text-muted">Image actuelle : 
                                    <img src="{{ Storage::url($product->image) }}" alt="Image produit actuelle" class="product-thumbnail me-2">
                                    <a href="{{ Storage::url($product->image) }}" target="_blank" class="btn btn-sm btn-outline-info p-1 py-0" title="Voir l'image actuelle">
                                        <i class="fas fa-eye me-1"></i> Voir
                                    </a>
                                </small>
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="clear_image" id="clear_image_{{ $product->id }}" value="1">
                                    <label class="form-check-label" for="clear_image_{{ $product->id }}">
                                        Supprimer l'image actuelle
                                    </label>
                                </div>
                            </div>
                        @else
                            <small class="form-text text-muted">Aucune image n'est actuellement associée à ce produit.</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="alert_threshold_edit_{{ $product->id }}" class="form-label">Seuil d'alerte (Stock) <span class="text-danger">*</span></label> {{-- NOUVEAU --}}
                        <input type="number" step="0.01" class="form-control" id="alert_threshold_edit_{{ $product->id }}" name="alert_threshold" value="{{ old('alert_threshold', $product->alert_threshold ?? 0) }}" min="0" required>
                        <small class="form-text text-muted">Quantité minimale avant alerte de stock bas.</small>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="disponible" @selected(old('status', $product->status) == 'disponible')>Disponible</option>
                            <option value="indisponible" @selected(old('status', $product->status) == 'indisponible')>Indisponible</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modalités de paiement</label>
                        <div>
                            @php
                                // Assurez-vous que $product->payment_modalities est un tableau
                                $productPaymentModalities = json_decode($product->payment_modalities, true) ?? [];
                                $oldPaymentModalities = old('payment_modalities', $productPaymentModalities);
                            @endphp
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="payment_cash_edit_{{ $product->id }}" name="payment_modalities[]" value="cash" {{ in_array('cash', $oldPaymentModalities) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_cash_edit_{{ $product->id }}">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="payment_transfer_edit_{{ $product->id }}" name="payment_modalities[]" value="virement" {{ in_array('virement', $oldPaymentModalities) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_transfer_edit_{{ $product->id }}">Virement bancaire</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="payment_check_edit_{{ $product->id }}" name="payment_modalities[]" value="cheque" {{ in_array('cheque', $oldPaymentModalities) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_check_edit_{{ $product->id }}">Chèque</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estimated_harvest_quantity" class="form-label">Qté. récolte estimée</label>
                            <input type="number" step="0.01" class="form-control" id="estimated_harvest_quantity" name="estimated_harvest_quantity" value="{{ old('estimated_harvest_quantity', $product->estimated_harvest_quantity) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estimated_harvest_period" class="form-label">Période récolte estimée</label>
                            <input type="text" class="form-control" id="estimated_harvest_period" name="estimated_harvest_period" value="{{ old('estimated_harvest_period', $product->estimated_harvest_period) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Mettre à jour</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Inclure Select2 CSS et JS (si non déjà dans le layout principal) --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal{{ $product->id }} = document.getElementById('editProductModal{{ $product->id }}');
        var provenanceTypeSelectEdit = document.getElementById('provenance_type_edit_{{ $product->id }}');
        var provenanceIdGroupEdit = document.getElementById('provenance_id_group_edit_{{ $product->id }}');
        var provenanceIdSelectEdit = $('#provenance_id_edit_{{ $product->id }}'); // Utiliser jQuery pour Select2
        var provenanceRequiredStarEdit = provenanceIdGroupEdit.querySelector('.provenance-required-star');


        // Fonction pour gérer l'affichage conditionnel et la validation
        function toggleProvenanceIdFieldEdit() {
            if (provenanceTypeSelectEdit.value === 'producteur_partenaire') {
                provenanceIdGroupEdit.style.display = 'block';
                provenanceIdSelectEdit.attr('required', true); // Rendre le champ requis
                if (provenanceRequiredStarEdit) provenanceRequiredStarEdit.style.display = 'inline';
            } else {
                provenanceIdGroupEdit.style.display = 'none';
                provenanceIdSelectEdit.val('').trigger('change'); // Vider et réinitialiser Select2
                provenanceIdSelectEdit.attr('required', false); // Ne pas rendre le champ requis
                if (provenanceRequiredStarEdit) provenanceRequiredStarEdit.style.display = 'none';
            }
        }

        // Initialiser Select2 lorsque la modale est montrée
        editModal{{ $product->id }}.addEventListener('shown.bs.modal', function () {
            $('#category_id_edit_{{ $product->id }}').select2({
                dropdownParent: $('#editProductModal{{ $product->id }}')
            });
            provenanceIdSelectEdit.select2({
                dropdownParent: $('#editProductModal{{ $product->id }}')
            });
            toggleProvenanceIdFieldEdit(); // Appeler la fonction au moment de l'affichage
        });

        // Gérer l'affichage conditionnel lors du changement de sélection
        provenanceTypeSelectEdit.addEventListener('change', toggleProvenanceIdFieldEdit);

        // Appeler la fonction une fois au chargement initial si la modale est déjà visible (par ex. après une erreur de validation)
        if ($(editModal{{ $product->id }}).hasClass('show')) {
            $('#category_id_edit_{{ $product->id }}').select2({
                dropdownParent: $('#editProductModal{{ $product->id }}')
            });
            provenanceIdSelectEdit.select2({
                dropdownParent: $('#editProductModal{{ $product->id }}')
            });
            toggleProvenanceIdFieldEdit();
        } else {
            // S'assurer que le champ est caché si la modale n'est pas affichée au chargement
            provenanceIdGroupEdit.style.display = 'none';
            provenanceIdSelectEdit.attr('required', false);
            if (provenanceRequiredStarEdit) provenanceRequiredStarEdit.style.display = 'none';
        }
    });
</script>
