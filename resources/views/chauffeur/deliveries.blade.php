@extends('layouts.app')

@section('title', 'Mes Livraisons')

@section('content')
<div class="container-fluid py-4">
    {{-- Titre dynamique qui change en fonction de la source de la navigation --}}
    @if(request()->has('route_id'))
        <h1 class="h2 fw-bold mb-4">
            <i class="fas fa-truck-moving me-2 text-primary"></i> Livraisons de la tournée #{{ request()->input('route_id') }}
        </h1>
    @else
        <h1 class="h2 fw-bold mb-4">
            <i class="fas fa-truck-moving me-2 text-primary"></i> Toutes mes Livraisons
        </h1>
    @endif

    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">ID Livraison</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Client</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Adresse</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Date & Heure</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Statut</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveries as $delivery)
                            <tr class="transition-transform-hover">
                                <td class="px-4 py-3 fw-medium text-gray-900">#{{ $delivery->id }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $delivery->order->client->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $delivery->address ?? $delivery->order->shipping_address ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ \Carbon\Carbon::parse($delivery->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = '';
                                        switch($delivery->status) {
                                            case 'En cours': $badgeClass = 'bg-info text-white'; break;
                                            case 'Terminée': $badgeClass = 'bg-success text-white'; break;
                                            case 'Annulée': $badgeClass = 'bg-danger text-white'; break;
                                            default: $badgeClass = 'bg-secondary text-white'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($delivery->status) }}</span>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($delivery->status !== 'Terminée')
                                        <form action="{{ route('chauffeur.deliveries.complete', $delivery->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-success rounded-pill">
                                                <i class="fas fa-check-circle me-1"></i> Terminer
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-success fw-bold">Effectuée</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Aucune livraison assignée pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center py-3">
            {{ $deliveries->links() }}
        </div>
    </div>
</div>
@endsection