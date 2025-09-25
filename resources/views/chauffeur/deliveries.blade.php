@extends('layouts.app')

@section('title', 'Mes Livraisons')

@section('content')
<div class="container-fluid py-4">
    @if(request()->has('route_id'))
        <h1 class="h2 fw-bold mb-4">
            <i class="fas fa-truck-moving me-2 text-primary"></i> Livraisons de la tournée #{{ request()->input('route_id') }}
        </h1>
    @else
        <h1 class="h2 fw-bold mb-4">
            <i class="fas fa-truck-moving me-2 text-primary"></i> Toutes mes Livraisons
        </h1>
    @endif

    {{-- Formulaire de filtres de recherche --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('chauffeur.deliveries') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="route_id" value="{{ request('route_id') }}">
                <div class="col-md-4">
                    <label for="filter_status" class="form-label">Statut</label>
                    <select name="status" id="filter_status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="En cours" {{ request('status') === 'En cours' ? 'selected' : '' }}>En cours</option>
                        <option value="Terminée" {{ request('status') === 'Terminée' ? 'selected' : '' }}>Terminée</option>
                        <option value="Annulée" {{ request('status') === 'Annulée' ? 'selected' : '' }}>Annulée</option>
                    </select>
                </div>
                <div class="col-md-4">
                    {{-- CORRECTION: Nom et libellé de l'input pour le filtre par date effective --}}
                    <label for="filter_date" class="form-label">Date de livraison effective</label>
                    <input type="date" name="delivered_at" id="filter_date" class="form-control" value="{{ request('delivered_at') }}">
                </div>
                <div class="col-md-4 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('chauffeur.deliveries', ['route_id' => request('route_id')]) }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Votre tableau de livraisons --}}
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">ID</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Client</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Adr. Livraison</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Adr. Client</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Email</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Téléphone</th>

                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Géolocalisation</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Date souhaitée</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Date planifiée</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Notes</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Statut</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-end text-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveries as $delivery)
                        <tr class="transition-transform-hover">
                            <td class="px-4 py-3 fw-medium text-gray-900 text-nowrap">#{{ $delivery->id }}</td>
                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->order->client->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->order->delivery_location ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->order->client->address ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->order->client->email ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->order->client->phone_number ?? 'N/A' }}</td>

                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->order->geolocation ?? 'N/A' }}</td>
                            {{-- Affichage de la date souhaitée du client --}}
                            <td class="px-4 py-3 text-gray-700 text-nowrap">
                                {{ \Carbon\Carbon::parse($delivery->order->desired_delivery_date)->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            {{-- Affichage de la date planifiée de la tournée --}}
                            <td class="px-4 py-3 text-gray-700 text-nowrap">
                                {{ \Carbon\Carbon::parse($delivery->deliveryRoute->delivery_date)->format('d/m/Y') ?? 'N/A' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $delivery->notes ?? 'N/A' }}</td>
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
                            <td class="px-4 py-3 text-end text-nowrap">
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
                            <td colspan="10" class="text-center py-5 text-muted">Aucune livraison assignée pour le moment.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-center py-3">
            {{ $deliveries->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>
</div>
@endsection