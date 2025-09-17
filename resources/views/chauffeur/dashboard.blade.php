@extends('layouts.app')

@section('title', 'Tableau de bord Chauffeur')

@section('content')

<div class="container-fluid">
    <h1 class="mt-4">Tableau de bord Chauffeur</h1>
    <p class="mb-4">Bonjour, {{ auth()->user()->first_name }} ! Voici les informations clés pour votre journée de travail.</p>

    {{-- Section des indicateurs clés --}}
    <div class="row">
        {{-- Livraisons du jour --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Livraisons prévues aujourd'hui</div>
                            <div class="text-lg fw-bold">{{ $totalDeliveries }}</div>
                        </div>
                        <i class="fas fa-route fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.deliveries', ['date' => now()->toDateString()]) }}">
                        Voir les tournées
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Livraisons en cours --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Livraisons en cours</div>
                            <div class="text-lg fw-bold">{{ $pendingDeliveries }}</div>
                        </div>
                        <i class="fas fa-truck-moving fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.deliveries', ['status' => 'En cours']) }}">
                        Détails de la livraison
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Livraisons terminées --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Livraisons terminées</div>
                            <div class="text-lg fw-bold">{{ $completedDeliveries }}</div>
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.deliveries', ['status' => 'Terminée']) }}">
                        Historique des livraisons
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Heure de la prochaine livraison --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Prochaine livraison à</div>
                            @if($nextDelivery)
                                <div class="text-lg fw-bold">
                                    {{ $nextDelivery ? \Carbon\Carbon::parse($nextDelivery->planned_delivery_time)->format('H:i') : 'N/A' }}
                            </div>
                            @else
                                <div class="text-lg fw-bold">N/A</div>
                            @endif
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.planning') }}">
                        Voir le planning
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section principale : Carte et tableau des livraisons --}}
    <div class="row">
        {{-- Carte de la tournée (placeholder) --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    Carte de la tournée du jour
                </div>
                <div class="card-body">
                    {{-- Placeholder pour une carte, remplacez par une intégration d'API (ex: Google Maps) --}}
                    <div class="text-center p-5 bg-light rounded" style="min-height: 300px;">
                        <i class="fas fa-map fa-3x text-secondary mb-3"></i>
                        <p class="text-secondary">La carte de votre tournée apparaîtra ici.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des prochaines livraisons --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Détails des prochaines livraisons
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N° Commande</th>
                                    <th>Client</th>
                                    <th>Adresse</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($deliveries->whereNotIn('status', ['Terminée', 'Annulée'])->sortBy('created_at') as $delivery)
                                    <tr>
                                        <td>#{{ $delivery->id }}</td>
                                        <td>{{ $delivery->order->client->name ?? 'N/A' }}</td>
                                        <td>{{ $delivery->address ?? $delivery->order->shipping_address ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = '';
                                                switch($delivery->status) {
                                                    case 'En cours': $badgeClass = 'bg-info'; break;
                                                    case 'Planifiée': $badgeClass = 'bg-warning'; break;
                                                    case 'Terminée': $badgeClass = 'bg-success'; break;
                                                    case 'Annulée': $badgeClass = 'bg-danger'; break;
                                                    default: $badgeClass = 'bg-secondary'; break;
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($delivery->status) }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('chauffeur.deliveries.complete', $delivery->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-success">
                                                    Marquer comme terminé
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Toutes les livraisons du jour sont terminées.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection