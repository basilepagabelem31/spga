<div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1" aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProductModalLabel{{ $product->id }}">Modifier le produit : {{ $product->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('partenaire.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name_{{ $product->id }}" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name_{{ $product->id }}" name="name" value="{{ old('name', $product->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description_{{ $product->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description_{{ $product->id }}" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_category_id_{{ $product->id }}" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_category_id_{{ $product->id }}" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_production_mode_{{ $product->id }}" class="form-label">Mode de production <span class="text-danger">*</span></label>
                            <select class="form-select" id="edit_production_mode_{{ $product->id }}" name="production_mode" required>
                                <option value="">Sélectionner le mode</option>
                                <option value="bio" {{ old('production_mode', $product->production_mode) == 'bio' ? 'selected' : '' }}>Bio</option>
                                <option value="agroécologie" {{ old('production_mode', $product->production_mode) == 'agroécologie' ? 'selected' : '' }}>Agroécologie</option>
                                <option value="conventionnel" {{ old('production_mode', $product->production_mode) == 'conventionnel' ? 'selected' : '' }}>Conventionnel</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_packaging_format_{{ $product->id }}" class="form-label">Format d'emballage</label>
                            <input type="text" class="form-control" id="edit_packaging_format_{{ $product->id }}" name="packaging_format" value="{{ old('packaging_format', $product->packaging_format) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_min_order_quantity_{{ $product->id }}" class="form-label">Quantité min. commande</label>
                            <input type="number" step="0.01" class="form-control" id="edit_min_order_quantity_{{ $product->id }}" name="min_order_quantity" value="{{ old('min_order_quantity', $product->min_order_quantity) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_unit_price_{{ $product->id }}" class="form-label">Prix unitaire <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="edit_unit_price_{{ $product->id }}" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_sale_unit_{{ $product->id }}" class="form-label">Unité de vente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="edit_sale_unit_{{ $product->id }}" name="sale_unit" value="{{ old('sale_unit', $product->sale_unit) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_alert_threshold_{{ $product->id }}" class="form-label">Seuil d'alerte (Stock) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control" id="edit_alert_threshold_{{ $product->id }}" name="alert_threshold" value="{{ old('alert_threshold', $product->alert_threshold) }}" min="0" required>
                            <small class="form-text text-muted">Quantité minimale avant alerte de stock bas.</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_product_image_{{ $product->id }}" class="form-label">Image du produit</label>
                        <input type="file" class="form-control" id="edit_product_image_{{ $product->id }}" name="image">
                        @if ($product->image)
                            <div class="mt-2">
                                <img src="{{ Storage::url($product->image) }}" alt="Image actuelle" class="product-thumbnail">
                                <small class="text-muted ms-2">Image actuelle</small>
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="edit_status_{{ $product->id }}" class="form-label">Statut <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_status_{{ $product->id }}" name="status" required>
                            <option value="disponible" {{ old('status', $product->status) == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="indisponible" {{ old('status', $product->status) == 'indisponible' ? 'selected' : '' }}>Indisponible</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modalités de paiement</label>
                        @php
                            $paymentModalities = is_array($product->payment_modalities) ? $product->payment_modalities : json_decode($product->payment_modalities, true) ?? [];
                        @endphp
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="edit_payment_cash_{{ $product->id }}" name="payment_modalities[]" value="cash" {{ in_array('cash', old('payment_modalities', $paymentModalities)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_payment_cash_{{ $product->id }}">Cash</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="edit_payment_transfer_{{ $product->id }}" name="payment_modalities[]" value="virement" {{ in_array('virement', old('payment_modalities', $paymentModalities)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_payment_transfer_{{ $product->id }}">Virement bancaire</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="edit_payment_check_{{ $product->id }}" name="payment_modalities[]" value="cheque" {{ in_array('cheque', old('payment_modalities', $paymentModalities)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="edit_payment_check_{{ $product->id }}">Chèque</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_estimated_harvest_quantity_{{ $product->id }}" class="form-label">Qté. récolte estimée</label>
                            <input type="number" step="0.01" class="form-control" id="edit_estimated_harvest_quantity_{{ $product->id }}" name="estimated_harvest_quantity" value="{{ old('estimated_harvest_quantity', $product->estimated_harvest_quantity) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_estimated_harvest_period_{{ $product->id }}" class="form-label">Période récolte estimée</label>
                            <input type="text" class="form-control" id="edit_estimated_harvest_period_{{ $product->id }}" name="estimated_harvest_period" value="{{ old('estimated_harvest_period', $product->estimated_harvest_period) }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Modifier le produit</button>
                </div>
            </form>
        </div>
    </div>
</div>