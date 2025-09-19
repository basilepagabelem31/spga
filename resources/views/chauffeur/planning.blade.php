@extends('layouts.app')

@section('title', 'Mon Planning')

@section('content')
<div class="container-fluid py-4">
    <h1 class="h2 fw-bold mb-4">
        <i class="fas fa-calendar-alt me-2 text-primary"></i> Mon Planning
    </h1>

    {{-- Formulaire de filtres de recherche --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
<div class="card-body">
    <form action="{{ route('chauffeur.planning') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label for="filter_status" class="form-label">Statut</label>
            <select name="status" id="filter_status" class="form-select">
                <option value="">Tous les statuts</option>
                @foreach (['planifiée', 'en_cours', 'terminée', 'annulée'] as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label for="filter_date" class="form-label">Date de la tournée</label>
            <input type="date" name="delivery_date" id="filter_date" class="form-control" value="{{ request('delivery_date') }}">
        </div>
        <div class="col-md-4 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">
                <i class="fas fa-filter me-1"></i> Filtrer
            </button>
            <a href="{{ route('chauffeur.planning') }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i> Réinitialiser
            </a>
        </div>
    </form>
</div>

    {{-- Votre tableau des tournées --}}
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Date de la tournée</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Statut</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted">Nombre de livraisons</th>
                            <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-end">Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveryRoutes as $route)
                            <tr>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($route->delivery_date)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $badgeClass = '';
                                        switch($route->status) {
                                            case 'Planifiée': $badgeClass = 'bg-warning text-dark'; break;
                                            case 'En cours': $badgeClass = 'bg-info'; break;
                                            case 'Terminée': $badgeClass = 'bg-success'; break;
                                            case 'Annulée': $badgeClass = 'bg-danger'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($route->status) }}</span>
                                </td>
                                <td class="px-4 py-3">{{ $route->deliveries->count() }}</td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('chauffeur.deliveries', ['route_id' => $route->id]) }}" class="btn btn-sm btn-primary">
                                        Voir les livraisons
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">Aucune tournée de livraison n'est planifiée pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

 <div class="d-flex justify-content-center mt-4">
        {{ $deliveryRoutes->links('vendor.pagination.bootstrap-5') }}
    </div>  
    </div>
</div>
@endsection