<div class="modal fade" id="editOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="editOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOrderModalLabel{{ $order->id }}">Modifier la commande : {{ $order->order_code }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.update', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="client_id_edit_{{ $order->id }}" class="form-label">Client <span class="text-danger">*</span></label>
                            <select class="form-select" id="client_id_edit_{{ $order->id }}" name="client_id" required>
                                <option value="">Sélectionner un client</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}" @selected(old('client_id', $order->client_id) == $client->id)>
                                        {{ $client->full_name ?? $client->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_edit_{{ $order->id }}" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select class="form-select" id="status_edit_{{ $order->id }}" name="status" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $order->status) == $status)>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="desired_delivery_date_edit_{{ $order->id }}" class="form-label">Date de livraison souhaitée</label>
                            <input type="text" class="form-control" id="desired_delivery_date_edit_{{ $order->id }}" name="desired_delivery_date" value="{{ old('desired_delivery_date', $order->desired_delivery_date) }}" placeholder="Ex: 25/12/2024 ou Semaine 48">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="delivery_mode_edit_{{ $order->id }}" class="form-label">Mode de livraison <span class="text-danger">*</span></label>
                            <select class="form-select" id="delivery_mode_edit_{{ $order->id }}" name="delivery_mode" required>
                                <option value="standard_72h" @selected(old('delivery_mode', $order->delivery_mode) == 'standard_72h')>Standard (72h)</option>
                                <option value="express_6_12h" @selected(old('delivery_mode', $order->delivery_mode) == 'express_6_12h')>Express (6-12h)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="delivery_location_edit_{{ $order->id }}" class="form-label">Lieu de livraison</label>
                        <textarea class="form-control" id="delivery_location_edit_{{ $order->id }}" name="delivery_location" rows="2">{{ old('delivery_location', $order->delivery_location) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="geolocation_edit_{{ $order->id }}" class="form-label">Géolocalisation (URL ou Coordonnées)</label>
                        <input type="text" class="form-control" id="geolocation_edit_{{ $order->id }}" name="geolocation" value="{{ old('geolocation', $order->geolocation) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_mode_edit_{{ $order->id }}" class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_mode_edit_{{ $order->id }}" name="payment_mode" required>
                                <option value="paiement_mobile" @selected(old('payment_mode', $order->payment_mode) == 'paiement_mobile')>Paiement Mobile</option>
                                <option value="paiement_a_la_livraison" @selected(old('payment_mode', $order->payment_mode) == 'paiement_a_la_livraison')>Paiement à la livraison</option>
                                <option value="virement_bancaire" @selected(old('payment_mode', $order->payment_mode) == 'virement_bancaire')>Virement bancaire</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="validated_by_edit_{{ $order->id }}" class="form-label">Validé par</label>
                            <select class="form-select" id="validated_by_edit_{{ $order->id }}" name="validated_by">
                                <option value="">Non validé</option>
                                @foreach ($validators as $validator)
                                    <option value="{{ $validator->id }}" @selected(old('validated_by', $order->validated_by) == $validator->id)>
                                        {{ $validator->full_name ?? $validator->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes_edit_{{ $order->id }}" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes_edit_{{ $order->id }}" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                    </div>

                    <hr class="my-4">
                    <h4>Articles de la commande</h4>
                    <div id="order-items-container-edit-{{ $order->id }}">
                        {{-- Les articles de commande existants seront affichés ici --}}
                        @foreach ($order->orderItems as $index => $item)
                            <div class="row g-2 mb-2 order-item-row">
                                <div class="col-md-6">
                                    <select class="form-select product-select" name="products[{{ $index }}][id]" required>
                                        <option value="">Sélectionner un produit</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" data-unit-price="{{ $product->unit_price }}" data-sale-unit="{{ $product->sale_unit }}" @selected($item->product_id == $product->id)>
                                                {{ $product->name }} ({{ number_format($product->unit_price, 2) }} FCFA/{{ $product->sale_unit }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" step="0.01" class="form-control quantity-input" name="products[{{ $index }}][quantity]" value="{{ old('products.' . $index . '.quantity', $item->quantity) }}" min="0.01" required placeholder="Quantité">
                                </div>
                                <div class="col-md-2 d-flex align-items-center">
                                    <span class="item-total">{{ number_format($item->getLineTotal(), 2) }} FCFA</span>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="add-item-btn-edit-{{ $order->id }}"><i class="fas fa-plus me-1"></i> Ajouter un article</button>
                    <div class="text-end mt-3">
                        <strong>Montant Total Estimé : <span id="total-amount-display-edit-{{ $order->id }}">{{ number_format($order->total_amount, 2) }} FCFA</span></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Mettre à jour la commande</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Script pour initialiser Select2 et gérer les articles de commande --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editOrderModal = document.getElementById('editOrderModal{{ $order->id }}');
        var orderItemsContainerEdit = document.getElementById('order-items-container-edit-{{ $order->id }}');
        var addItemBtnEdit = document.getElementById('add-item-btn-edit-{{ $order->id }}');
        var totalAmountDisplayEdit = document.getElementById('total-amount-display-edit-{{ $order->id }}');
        var productIndexEdit = {{ $order->orderItems->count() }}; // Index de départ pour les nouveaux produits

        // Fonction pour mettre à jour le montant total
        function updateOrderTotalEdit() {
            let total = 0;
            orderItemsContainerEdit.querySelectorAll('.order-item-row').forEach(function(row) {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const productSelect = row.querySelector('.product-select');
                const unitPrice = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.unitPrice) || 0;
                const itemTotal = quantity * unitPrice;
                row.querySelector('.item-total').textContent = itemTotal.toFixed(2) + ' FCFA';
                total += itemTotal;
            });
            totalAmountDisplayEdit.textContent = total.toFixed(2) + ' FCFA';
        }

        // Fonction pour ajouter un nouvel article de commande
        function addOrderItemRowEdit(productId = '', quantity = '') {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-2', 'mb-2', 'order-item-row');
            newRow.innerHTML = `
                <div class="col-md-6">
                    <select class="form-select product-select" name="products[${productIndexEdit}][id]" required>
                        <option value="">Sélectionner un produit</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-unit-price="{{ $product->unit_price }}" data-sale-unit="{{ $product->sale_unit }}">{{ $product->name }} ({{ number_format($product->unit_price, 2) }} FCFA/{{ $product->sale_unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" class="form-control quantity-input" name="products[${productIndexEdit}][quantity]" value="${quantity}" min="0.01" required placeholder="Quantité">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                    <span class="item-total">0.00 FCFA</span>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn"><i class="fas fa-times"></i></button>
                </div>
            `;
            orderItemsContainerEdit.appendChild(newRow);

            // Initialiser Select2 pour le nouveau select de produit
            $(newRow.querySelector('.product-select')).select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });

            // Pré-sélectionner le produit si fourni
            if (productId) {
                $(newRow.querySelector('.product-select')).val(productId).trigger('change');
            }

            // Attacher les écouteurs d'événements
            newRow.querySelector('.product-select').addEventListener('change', updateOrderTotalEdit);
            newRow.querySelector('.quantity-input').addEventListener('input', updateOrderTotalEdit);
            newRow.querySelector('.remove-item-btn').addEventListener('click', function() {
                newRow.remove();
                updateOrderTotalEdit();
            });

            productIndexEdit++;
            updateOrderTotalEdit(); // Mettre à jour le total après ajout
        }

        // Événement pour le bouton "Ajouter un article"
        addItemBtnEdit.addEventListener('click', function() {
            addOrderItemRowEdit();
        });

        // Initialisation des Select2 existants et des écouteurs d'événements lors de l'ouverture de la modale
        editOrderModal.addEventListener('shown.bs.modal', function () {
            $('#client_id_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#status_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#delivery_mode_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#payment_mode_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#validated_by_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });

            // Initialiser Select2 et attacher les écouteurs pour les articles déjà présents
            orderItemsContainerEdit.querySelectorAll('.order-item-row').forEach(function(row) {
                $(row.querySelector('.product-select')).select2({
                    dropdownParent: $('#editOrderModal{{ $order->id }}')
                });
                row.querySelector('.product-select').addEventListener('change', updateOrderTotalEdit);
                row.querySelector('.quantity-input').addEventListener('input', updateOrderTotalEdit);
                row.querySelector('.remove-item-btn').addEventListener('click', function() {
                    row.remove();
                    updateOrderTotalEdit();
                });
            });
            updateOrderTotalEdit(); // Calculer le total initial
        });

        // Si la modale est déjà ouverte au chargement (ex: après erreur de validation)
        if ($(editOrderModal).hasClass('show')) {
            $('#client_id_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#status_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#delivery_mode_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#payment_mode_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            $('#validated_by_edit_{{ $order->id }}').select2({
                dropdownParent: $('#editOrderModal{{ $order->id }}')
            });
            orderItemsContainerEdit.querySelectorAll('.order-item-row').forEach(function(row) {
                $(row.querySelector('.product-select')).select2({
                    dropdownParent: $('#editOrderModal{{ $order->id }}')
                });
                row.querySelector('.product-select').addEventListener('change', updateOrderTotalEdit);
                row.querySelector('.quantity-input').addEventListener('input', updateOrderTotalEdit);
                row.querySelector('.remove-item-btn').addEventListener('click', function() {
                    row.remove();
                    updateOrderTotalEdit();
                });
            });
            updateOrderTotalEdit();
        }
    });
</script>
