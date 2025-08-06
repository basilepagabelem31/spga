<div class="modal fade" id="createStockModal" tabindex="-1" aria-labelledby="createStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createStockModalLabel">Ajouter un mouvement de stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stocks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id_create" class="form-label">Produit <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id_create" name="product_id" required>
                            <option value="">Sélectionner un produit</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sale_unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantité <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="quantity" name="quantity" value="{{ old('quantity') }}" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="movement_type" class="form-label">Type de mouvement <span class="text-danger">*</span></label>
                        <select class="form-select" id="movement_type" name="movement_type" required>
                            <option value="">Sélectionner le type</option>
                            @foreach ($movementTypes as $type)
                                <option value="{{ $type }}" {{ old('movement_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reference_id" class="form-label">Référence (optionnel)</label>
                        <input type="text" class="form-control" id="reference_id" name="reference_id" value="{{ old('reference_id') }}">
                        <small class="form-text text-muted">Ex: Numéro de commande, de livraison, de récolte.</small>
                    </div>
                    <div class="mb-3">
                        <label for="alert_threshold" class="form-label">Seuil d'alerte (optionnel)</label>
                        <input type="number" step="0.01" class="form-control" id="alert_threshold" name="alert_threshold" value="{{ old('alert_threshold') }}" min="0">
                        <small class="form-text text-muted">Quantité minimale avant alerte de stock bas.</small>
                    </div>
                    <div class="mb-3">
                        <label for="movement_date" class="form-label">Date du mouvement (optionnel)</label>
                        <input type="datetime-local" class="form-control" id="movement_date" name="movement_date" value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer le mouvement</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script pour initialiser Select2 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var createStockModal = document.getElementById('createStockModal');
        createStockModal.addEventListener('shown.bs.modal', function () {
            $('#product_id_create').select2({
                dropdownParent: $('#createStockModal')
            });
            $('#movement_type').select2({
                dropdownParent: $('#createStockModal')
            });
        });

        if ($(createStockModal).hasClass('show')) {
            $('#product_id_create').select2({
                dropdownParent: $('#createStockModal')
            });
            $('#movement_type').select2({
                dropdownParent: $('#createStockModal')
            });
        }
    });
</script>
