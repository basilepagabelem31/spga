<div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createOrderModalLabel">Créer une nouvelle commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_id_create" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_id_create" name="client_id" required>
                                <option value="" disabled selected>Sélectionner un client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->full_name ?? $client->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_create" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_create" name="status" required>
                                <option value="" disabled selected>Sélectionner un statut</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="desired_delivery_date" class="form-label">Date de livraison souhaitée <span class="text-danger">*</span></label>
<input type="date" class="form-control" id="desired_delivery_date" name="desired_delivery_date"
       value="{{ old('desired_delivery_date') }}"
       placeholder="Ex: 25/12/2024 ou Semaine 48">                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_mode" class="form-label">Mode de livraison <span class="text-danger">*</span></label>
                            <select class="form-select" id="delivery_mode" name="delivery_mode" required>
                                {{-- Option par défaut désactivée et sélectionnée --}}
                                <option value="" disabled selected>Sélectionner un mode de livraison</option>
                                <option value="standard_72h" {{ old('delivery_mode') == 'standard_72h' ? 'selected' : '' }}>Standard (72h)</option>
                                <option value="express_6_12h" {{ old('delivery_mode') == 'express_6_12h' ? 'selected' : '' }}>Express (6-12h)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="delivery_location" class="form-label">Lieu de livraison  <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="delivery_location" name="delivery_location" rows="2">{{ old('delivery_location') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="geolocation" class="form-label">Géolocalisation (URL ou Coordonnées) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="geolocation" name="geolocation" value="{{ old('geolocation') }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_mode" class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_mode" name="payment_mode" required>
                                {{-- Option par défaut désactivée et sélectionnée --}}
                                <option value="" disabled selected>Sélectionner un mode de paiement</option>
                                <option value="paiement_mobile" {{ old('payment_mode') == 'paiement_mobile' ? 'selected' : '' }}>Paiement Mobile</option>
                                <option value="paiement_a_la_livraison" {{ old('payment_mode') == 'paiement_a_la_livraison' ? 'selected' : '' }}>Paiement à la livraison</option>
                                <option value="virement_bancaire" {{ old('payment_mode') == 'virement_bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validated_by_create" class="form-label">Validé par</label>
                            <select class="form-select" id="validated_by_create" name="validated_by">
                                <option value="" {{ old('validated_by') == '' ? 'selected' : '' }}>Non validé</option>
                                @foreach ($validators as $validator)
                                    <option value="{{ $validator->id }}" {{ old('validated_by') == $validator->id ? 'selected' : '' }}>
                                        {{ $validator->full_name ?? $validator->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <hr class="my-4">
                    <h4>Articles de la commande</h4>
                    <div id="order-items-container-create">
                        @if (old('products'))
                            @foreach (old('products') as $index => $oldProduct)
                                <div class="row g-2 mb-2 order-item-row">
                                    <div class="col-md-6">
                                        <select class="form-select product-select" name="products[{{ $index }}][id]" required>
                                            <option value="" disabled selected>Sélectionner un produit</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" data-unit-price="{{ $product->unit_price }}" data-sale-unit="{{ $product->sale_unit }}" {{ $oldProduct['id'] == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} ({{ number_format($product->unit_price, 2) }} FCFA/{{ $product->sale_unit }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" step="0.01" class="form-control quantity-input" name="products[{{ $index }}][quantity]" value="{{ $oldProduct['quantity'] }}" min="0.01" required placeholder="Quantité">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-center">
                                        <span class="item-total">0.00 FCFA</span>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-item-btn-create"><i class="fas fa-plus me-1"></i> Ajouter un article</button>
                    <div class="text-end mt-3">
                        <strong>Montant Total Estimé : <span id="total-amount-display-create">0.00 FCFA</span></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer la commande</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script pour initialiser Select2 et gérer les articles de commande --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var createOrderModal = document.getElementById('createOrderModal');
        var orderItemsContainerCreate = document.getElementById('order-items-container-create');
        var addItemBtnCreate = document.getElementById('add-item-btn-create');
        var totalAmountDisplayCreate = document.getElementById('total-amount-display-create');
        var productIndexCreate = {{ old('products') ? count(old('products')) : 0 }};

        // Fonction pour mettre à jour le montant total
        function updateOrderTotalCreate() {
            let total = 0;
            orderItemsContainerCreate.querySelectorAll('.order-item-row').forEach(function(row) {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const productSelect = row.querySelector('.product-select');
                const unitPrice = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.unitPrice) || 0;
                const itemTotal = quantity * unitPrice;
                row.querySelector('.item-total').textContent = itemTotal.toFixed(2) + ' FCFA';
                total += itemTotal;
            });
            totalAmountDisplayCreate.textContent = total.toFixed(2) + ' FCFA';
        }

        // Fonction pour ajouter un nouvel article de commande
        function addOrderItemRowCreate(productId = '', quantity = '') {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-2', 'mb-2', 'order-item-row');
            newRow.innerHTML = `
                <div class="col-md-6">
                    <select class="form-select product-select" name="products[${productIndexCreate}][id]" required>
                        {{-- Ajout de disabled selected pour garantir le bon affichage --}}
                        <option value="" disabled selected>Sélectionner un produit</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-unit-price="{{ $product->unit_price }}" data-sale-unit="{{ $product->sale_unit }}">{{ $product->name }} ({{ number_format($product->unit_price, 2) }} FCFA/{{ $product->sale_unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" class="form-control quantity-input" name="products[${productIndexCreate}][quantity]" value="${quantity}" min="0.01" required placeholder="Quantité">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <span class="item-total">0.00 FCFA</span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i></button>
                </div>
            `;
            orderItemsContainerCreate.appendChild(newRow);

            // Initialiser Select2 pour le nouveau select de produit
            $(newRow.querySelector('.product-select')).select2({
                dropdownParent: $('#createOrderModal')
            });

            // Pré-sélectionner le produit si fourni
            if (productId) {
                $(newRow.querySelector('.product-select')).val(productId).trigger('change');
            }

            // Attacher les écouteurs d'événements
            newRow.querySelector('.product-select').addEventListener('change', updateOrderTotalCreate);
            newRow.querySelector('.quantity-input').addEventListener('input', updateOrderTotalCreate);
            newRow.querySelector('.remove-item-btn').addEventListener('click', function() {
                newRow.remove();
                updateOrderTotalCreate();
            });

            productIndexCreate++;
            updateOrderTotalCreate(); // Mettre à jour le total après ajout
        }

        // Événement pour le bouton "Ajouter un article"
        addItemBtnCreate.addEventListener('click', function() {
            addOrderItemRowCreate();
        });

        // Initialisation des Select2 existants et des écouteurs d'événements lors de l'ouverture de la modale
        createOrderModal.addEventListener('shown.bs.modal', function () {
            $('#client_id_create').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#status_create').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#validated_by_create').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#delivery_mode').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#payment_mode').select2({
                dropdownParent: $('#createOrderModal')
            });

            // Initialiser Select2 et attacher les écouteurs pour les articles déjà présents (e.g., après validation échouée)
            orderItemsContainerCreate.querySelectorAll('.order-item-row').forEach(function(row) {
                $(row.querySelector('.product-select')).select2({
                    dropdownParent: $('#createOrderModal')
                });
                row.querySelector('.product-select').addEventListener('change', updateOrderTotalCreate);
                row.querySelector('.quantity-input').addEventListener('input', updateOrderTotalCreate);
                row.querySelector('.remove-item-btn').addEventListener('click', function() {
                    row.remove();
                    updateOrderTotalCreate();
                });
            });
            updateOrderTotalCreate(); // Calculer le total initial
        });

        // Si la modale est déjà ouverte au chargement (ex: après erreur de validation)
        if ($(createOrderModal).hasClass('show')) {
            $('#client_id_create').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#status_create').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#validated_by_create').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#delivery_mode').select2({
                dropdownParent: $('#createOrderModal')
            });
            $('#payment_mode').select2({
                dropdownParent: $('#createOrderModal')
            });
            orderItemsContainerCreate.querySelectorAll('.order-item-row').forEach(function(row) {
                $(row.querySelector('.product-select')).select2({
                    dropdownParent: $('#createOrderModal')
                });
                row.querySelector('.product-select').addEventListener('change', updateOrderTotalCreate);
                row.querySelector('.quantity-input').addEventListener('input', updateOrderTotalCreate);
                row.querySelector('.remove-item-btn').addEventListener('click', function() {
                    row.remove();
                    updateOrderTotalCreate();
                });
            });
            updateOrderTotalCreate();
        }
    });
</script>