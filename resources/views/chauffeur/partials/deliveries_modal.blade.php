<div class="modal-header bg-primary text-white">
    <h5 class="modal-title d-flex align-items-center">
        <i class="fas fa-truck-moving me-2"></i>
        Livraisons de la tournée #{{ $route->id }}
        <span class="badge bg-light text-dark ms-2">
            {{ \Carbon\Carbon::parse($route->delivery_date)->translatedFormat('d F Y') }}
        </span>
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body bg-light p-4">
    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-secondary text-white">
            <tr>
                <th class="text-nowrap">ID</th>
                <th class="text-nowrap">Client</th>
                <th class="text-nowrap">Adresse Livraison</th>
                <th class="text-nowrap">Adresse Client</th>
                <th class="text-nowrap">Géolocalisation</th>
                <th class="text-nowrap">Date souhaitée</th>
                <th class="text-nowrap">Date planifiée</th>
                <th class="text-nowrap">Notes</th>
                <th class="text-nowrap">Statut</th>
                <th class="text-end text-nowrap">Actions</th>
            </tr>
        </thead>
                <tbody>
                    @forelse ($deliveries as $delivery)
                    <tr class="transition">
                        <td class="fw-bold text-primary">#{{ $delivery->id }}</td>
                        <td>{{ $delivery->order->client->name ?? 'N/A' }}</td>
                        <td>{{ $delivery->order->delivery_location ?? 'N/A' }}</td>
                        <td>{{ $delivery->order->client->address ?? 'N/A' }}</td>
                        <td><span class="text-muted">{{ $delivery->order->geolocation ?? 'N/A' }}</span></td>
                        <td>
                            <span class="badge bg-info text-dark">
                                {{ optional($delivery->order->desired_delivery_date)->format('d/m/Y') ?? 'N/A' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ optional($delivery->deliveryRoute->delivery_date)->format('d/m/Y') ?? 'N/A' }}
                            </span>
                        </td>
                        <td>{{ $delivery->notes ?? '—' }}</td>
                        <td>
                            @php
                                $badgeClass = match($delivery->status) {
                                    'En cours' => 'bg-info',
                                    'Terminée' => 'bg-success',
                                    'Annulée' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} px-3 py-2">
                                {{ ucfirst($delivery->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($delivery->status !== 'Terminée')
                            <form action="{{ route('chauffeur.deliveries.complete', $delivery->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-success shadow-sm rounded-pill">
                                    <i class="fas fa-check-circle me-1"></i> Terminer
                                </button>
                            </form>
                            @else
                            <span class="text-success fw-bold">
                                <i class="fas fa-check-circle me-1"></i> Effectuée
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i><br>
                            <span class="text-muted">Aucune livraison pour cette tournée.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-footer bg-light">
    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
        <i class="fas fa-times me-1"></i> Fermer
    </button>
</div>
