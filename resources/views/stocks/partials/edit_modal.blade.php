<div class="modal fade" id="editStockModal{{ $stock->id }}" tabindex="-1" aria-labelledby="editStockModalLabel{{ $stock->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStockModalLabel{{ $stock->id }}">Modifier le mouvement de stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('stocks.update', $stock) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="product_id_edit_{{ $stock->id }}" class="form-label">Produit <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id_edit_{{ $stock->id }}" name="product_id" required>
                            <option value="">Sélectionner un produit</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id', $stock->product_id) == $product->id)>
                                    {{ $product->name }} ({{ $product->sale_unit }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="quantity_edit_{{ $stock->id }}" class="form-label">Quantité <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control" id="quantity_edit_{{ $stock->id }}" name="quantity" value="{{ old('quantity', $stock->quantity) }}" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="movement_type_edit_{{ $stock->id }}" class="form-label">Type de mouvement <span class="text-danger">*</span></label>
                        <select class="form-select" id="movement_type_edit_{{ $stock->id }}" name="movement_type" required>
                            <option value="">Sélectionner le type</option>
                            @foreach ($movementTypes as $type)
                                <option value="{{ $type }}" @selected(old('movement_type', $stock->movement_type) == $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="reference_id_edit_{{ $stock->id }}" class="form-label">Référence (optionnel)</label>
                        <input type="text" class="form-control" id="reference_id_edit_{{ $stock->id }}" name="reference_id" value="{{ old('reference_id', $stock->reference_id) }}">
                        <small class="form-text text-muted">Ex: Numéro de commande, de livraison, de récolte.</small>
                    </div>
                    {{-- Le champ alert_threshold a été déplacé vers la modale de création/édition du produit --}}
                    <div class="mb-3">
                        <label for="movement_date_edit_{{ $stock->id }}" class="form-label">Date du mouvement (optionnel)</label>
                        <input type="datetime-local" class="form-control" id="movement_date_edit_{{ $stock->id }}" name="movement_date" value="{{ old('movement_date', $stock->movement_date ? $stock->movement_date->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Mettre à jour le mouvement</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script pour initialiser Select2 --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editStockModal = document.getElementById('editStockModal{{ $stock->id }}');
        editStockModal.addEventListener('shown.bs.modal', function () {
            $('#product_id_edit_{{ $stock->id }}').select2({
                dropdownParent: $('#editStockModal{{ $stock->id }}')
            });
            $('#movement_type_edit_{{ $stock->id }}').select2({
                dropdownParent: $('#editStockModal{{ $stock->id }}')
            });
        });

        if ($(editStockModal).hasClass('show')) {
            $('#product_id_edit_{{ $stock->id }}').select2({
                dropdownParent: $('#editStockModal{{ $stock->id }}')
            });
            $('#movement_type_edit_{{ $stock->id }}').select2({
                dropdownParent: $('#editStockModal{{ $stock->id }}')
            });
        }
    });
</script>
