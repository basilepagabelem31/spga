<div class="card shadow rounded-4 mb-4">
    <div class="card-body">
        <h5 class="card-title fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i> Informations générales</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <p class="mb-1"><strong class="text-muted">Code Commande :</strong> #{{ $order->id }}</p>
                <p class="mb-1"><strong class="text-muted">Client :</strong> {{ $order->client->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong class="text-muted">Date de la commande :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                <p class="mb-1"><strong class="text-muted">Montant total :</strong> <span class="fw-bold fs-5 text-primary">{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</span></p>
            </div>
            <div class="col-md-6">
             <p class="mb-1"><strong class="text-muted">Mode de livraison :</strong> <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->delivery_mode)) }}</span></p>
                                    <p class="mb-1"><strong class="text-muted">Mode de paiement :</strong> <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $order->payment_mode)) }}</span></p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong class="text-muted">Statut :</strong> 
                    @php
                        $statusClass = '';
                        switch($order->status) {
                            case 'en_attente': $statusClass = 'bg-warning text-dark'; break;
                            case 'confirmée': $statusClass = 'bg-primary'; break;
                            case 'en_cours': $statusClass = 'bg-info'; break;
                            case 'livrée': $statusClass = 'bg-success'; break;
                            case 'annulée': $statusClass = 'bg-danger'; break;
                            default: $statusClass = 'bg-secondary'; break;
                        }
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                </p>
<p class="mb-1"><strong class="text-muted">Validé par :</strong> {{ $order->validatedBy->full_name ?? 'Non validé' }}</p>            </div>
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
@endif                                    <div>
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