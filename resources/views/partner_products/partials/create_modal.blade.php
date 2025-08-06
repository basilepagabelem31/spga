<div class="modal fade" id="createPartnerProductModal" tabindex="-1" aria-labelledby="createPartnerProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createPartnerProductModalLabel">Associer un Produit à un Partenaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('partner_products.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="partner_id_create" class="form-label">Partenaire <span class="text-danger">*</span></label>
                        <select class="form-select" id="partner_id_create" name="partner_id" required>
                            <option value="">Sélectionner un partenaire</option>
                            @foreach ($partners as $partner)
                                <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->establishment_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product_id_create" class="form-label">Produit <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id_create" name="product_id" required>
                            <option value="">Sélectionner un produit</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer l'association</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var createPartnerProductModal = document.getElementById('createPartnerProductModal');
        createPartnerProductModal.addEventListener('shown.bs.modal', function () {
            $('#partner_id_create').select2({
                dropdownParent: $('#createPartnerProductModal')
            });
            $('#product_id_create').select2({
                dropdownParent: $('#createPartnerProductModal')
            });
        });

        if ($(createPartnerProductModal).hasClass('show')) {
            $('#partner_id_create').select2({
                dropdownParent: $('#createPartnerProductModal')
            });
            $('#product_id_create').select2({
                dropdownParent: $('#createPartnerProductModal')
            });
        }
    });
</script>
