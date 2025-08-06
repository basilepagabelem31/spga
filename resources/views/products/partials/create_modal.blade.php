<div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createProductModalLabel">Ajouter un nouveau produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category_id_create" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id_create" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="provenance_type_create" class="form-label">Type de provenance <span class="text-danger">*</span></label>
                            <select class="form-select" id="provenance_type_create" name="provenance_type" required>
                                <option value="">Sélectionner le type</option>
                                <option value="ferme_propre" {{ old('provenance_type') == 'ferme_propre' ? 'selected' :'' }}>Ferme Propre</option>
                                <option value="producteur_partenaire" {{ old('provenance_type') == 'producteur_partenaire' ? 'selected' : '' }}>Producteur Partenaire</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3" id="provenance_id_group_create" style="display: none;"> {{-- Initialement caché --}}
                        <label for="provenance_id_create" class="form-label">Partenaire (si producteur partenaire) <span class="text-danger provenance-required-star">*</span></label>
                        <select class="form-select" id="provenance_id_create" name="provenance_id">
                            <option value="">Sélectionner un partenaire</option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}" {{ old('provenance_id') == $partner->id ? 'selected' : '' }}>
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
                                <option value="bio" {{ old('production_mode') == 'bio' ? 'selected' : '' }}>Bio</option>
                                <option value="agroécologie" {{ old('production_mode') == 'agroécologie' ? 'selected' : '' }}>Agroécologie</option>
                                <option value="conventionnel" {{ old('production_mode') == 'conventionnel' ? 'selected' : '' }}>Conventionnel</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="packaging_format" class="form-label">Format d'emballage</label>
                            <input type="text" class="form-control" id="packaging_format" name="packaging_format" value="{{ old('packaging_format') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="min_order_quantity" class="form-label">Quantité min. commande</label>
                            <input type="number" step="0.01" class="form-control" id="min_order_quantity" name="min_order_quantity" value="{{ old('min_order_quantity', 0) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unit_price" class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="unit_price" name="unit_price" value="{{ old('unit_price') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sale_unit" class="form-label">Unité de vente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sale_unit" name="sale_unit" value="{{ old('sale_unit') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="product_image_create" class="form-label">Image du produit</label>
                        <input type="file" class="form-control" id="product_image_create" name="product_image">
                        <small class="form-text text-muted">Formats acceptés : JPG, JPEG, PNG, GIF. Taille max : 2MB.</small>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="disponible" {{ old('status') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="indisponible" {{ old('status') == 'indisponible' ? 'selected' : '' }}>Indisponible</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modalités de paiement</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="payment_cash_create" name="payment_modalities[]" value="cash" {{ in_array('cash', old('payment_modalities', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_cash_create">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="payment_transfer_create" name="payment_modalities[]" value="virement" {{ in_array('virement', old('payment_modalities', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_transfer_create">Virement bancaire</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="payment_check_create" name="payment_modalities[]" value="cheque" {{ in_array('cheque', old('payment_modalities', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="payment_check_create">Chèque</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="estimated_harvest_quantity" class="form-label">Qté. récolte estimée</label>
                            <input type="number" step="0.01" class="form-control" id="estimated_harvest_quantity" name="estimated_harvest_quantity" value="{{ old('estimated_harvest_quantity') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estimated_harvest_period" class="form-label">Période récolte estimée</label>
                            <input type="text" class="form-control" id="estimated_harvest_period" name="estimated_harvest_period" value="{{ old('estimated_harvest_period') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le produit</button>
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
        var createModal = document.getElementById('createProductModal');
        var provenanceTypeSelectCreate = document.getElementById('provenance_type_create');
        var provenanceIdGroupCreate = document.getElementById('provenance_id_group_create');
        var provenanceIdSelectCreate = $('#provenance_id_create'); // Utiliser jQuery pour Select2
        var provenanceRequiredStarCreate = provenanceIdGroupCreate.querySelector('.provenance-required-star');

        // Fonction pour gérer l'affichage conditionnel et la validation
        function toggleProvenanceIdFieldCreate() {
            if (provenanceTypeSelectCreate.value === 'producteur_partenaire') {
                provenanceIdGroupCreate.style.display = 'block';
                provenanceIdSelectCreate.attr('required', true); // Rendre le champ requis
                if (provenanceRequiredStarCreate) provenanceRequiredStarCreate.style.display = 'inline';
            } else {
                provenanceIdGroupCreate.style.display = 'none';
                provenanceIdSelectCreate.val('').trigger('change'); // Vider et réinitialiser Select2
                provenanceIdSelectCreate.attr('required', false); // Ne pas rendre le champ requis
                if (provenanceRequiredStarCreate) provenanceRequiredStarCreate.style.display = 'none';
            }
        }

        // Initialiser Select2 lorsque la modale est montrée
        createModal.addEventListener('shown.bs.modal', function () {
            $('#category_id_create').select2({
                dropdownParent: $('#createProductModal')
            });
            provenanceIdSelectCreate.select2({
                dropdownParent: $('#createProductModal')
            });
            toggleProvenanceIdFieldCreate(); // Appeler la fonction au moment de l'affichage
        });

        // Gérer l'affichage conditionnel lors du changement de sélection
        provenanceTypeSelectCreate.addEventListener('change', toggleProvenanceIdFieldCreate);

        // Appeler la fonction une fois au chargement initial si la modale est déjà visible (par ex. après une erreur de validation)
        if ($(createModal).hasClass('show')) {
            $('#category_id_create').select2({
                dropdownParent: $('#createProductModal')
            });
            provenanceIdSelectCreate.select2({
                dropdownParent: $('#createProductModal')
            });
            toggleProvenanceIdFieldCreate();
        } else {
            // S'assurer que le champ est caché si la modale n'est pas affichée au chargement
            provenanceIdGroupCreate.style.display = 'none';
            provenanceIdSelectCreate.attr('required', false);
            if (provenanceRequiredStarCreate) provenanceRequiredStarCreate.style.display = 'none';
        }
    });
</script>
