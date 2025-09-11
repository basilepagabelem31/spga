<div class="modal fade" id="viewOrderModal{{ $order->id }}" tabindex="-1" aria-labelledby="viewOrderModalLabel{{ $order->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewOrderModalLabel{{ $order->id }}">Détails de la commande <span class="badge bg-dark fw-bold">#{{ $order->order_code }}</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">
                            {{-- Ce lien ne sera pas utilisé dans la modale, mais il est dans votre code initial --}}
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary rounded-circle me-3">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            Détails de la commande <span class="badge bg-dark fw-bold">#{{ $order->order_code }}</span>
                        </h2>
                        <div class="d-flex align-items-center">
                            @if ($order->status == 'En attente de validation')
                                <form action="{{ route('orders.validate', $order) }}" method="POST" class="d-inline me-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-1"></i> Valider la commande</button>
                                </form>
                            @endif
                            
                        </div>
                    </div>
                    @if (session('success'))
                        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
                    @endif
                    <div class="card shadow rounded-4 mb-4">
                        <div class="card-body">
                            <h5 class="card-title fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i> Informations générales</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-muted">Client :</strong> {{ $order->client->full_name ?? 'N/A' }}</p>
                                    <p class="mb-1"><strong class="text-muted">Email :</strong> {{ $order->client->email ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-muted">Date de la commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                                    <p class="mb-1"><strong class="text-muted">Statut :</strong> 
                                        @php
                                            $statusClass = '';
                                            switch ($order->status) {
                                                case 'En attente de validation': $statusClass = 'bg-warning text-dark'; break;
                                                case 'Validée': $statusClass = 'bg-primary'; break;
                                                case 'En préparation': $statusClass = 'bg-info'; break;
                                                case 'En livraison': $statusClass = 'bg-dark'; break;
                                                case 'Livrée': $statusClass = 'bg-success'; break;
                                                case 'Annulée': $statusClass = 'bg-danger'; break;
                                                default: $statusClass = 'bg-secondary'; break;
                                            }
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $order->status }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-muted">Mode de livraison :</strong> <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->delivery_mode)) }}</span></p>
                                    <p class="mb-1"><strong class="text-muted">Mode de paiement :</strong> <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $order->payment_mode)) }}</span></p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1"><strong class="text-muted">Validé par :</strong> {{ $order->validatedBy->full_name ?? 'Non validé' }}</p>
                                    <p class="mb-1"><strong class="text-muted">Montant total :</strong> <span class="fw-bold fs-5 text-primary">{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow rounded-4">
                        <div class="card-body p-0">
                            <h5 class="card-title fw-bold text-primary p-4 pb-0"><i class="fas fa-box-open me-2"></i> Produits de la commande</h5>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="px-4 py-3">Produit</th>
                                            <th class="px-4 py-3 text-end">Quantité</th>
                                            <th class="px-4 py-3 text-end">Prix unitaire</th>
                                            <th class="px-4 py-3 text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->orderItems as $orderItem)
                                            <tr class="transition-transform-hover">
                                                <td class="px-4 py-3">
                                                    <div class="d-flex align-items-center">
@if ($orderItem->product && $orderItem->product->image)
    <img src="{{ Storage::url($orderItem->product->image) }}" alt="Image produit" class="img-thumbnail me-3" style="width: 50px;">
@else
    {{-- Image de substitution si le produit n'a pas d'image ou si la relation est manquante --}}
    <img src="https://via.placeholder.com/50" alt="Pas d'image" class="img-thumbnail me-3" style="width: 50px;">
@endif                                                        <div>
                                                            <div class="fw-bold">{{ $orderItem->product->name ?? 'N/A' }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-end">{{ $orderItem->quantity }}</td>
                                                <td class="px-4 py-3 text-end">{{ number_format($orderItem->unit_price_at_order, 2, ',', ' ') }} FCFA</td>
                                                <td class="px-4 py-3 text-end">{{ number_format($orderItem->quantity * $orderItem->unit_price_at_order, 2, ',', ' ') }} FCFA</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold py-3 pe-4">Total général :</td>
                                            <td class="text-end fw-bold py-3">{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>