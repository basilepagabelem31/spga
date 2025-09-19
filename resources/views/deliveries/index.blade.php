@extends('layouts.app')

@section('content')

<style>
    /* Styles généraux pour le corps de la page */
    body {
        background-color: #f0f2f5; /* Couleur de fond douce */
    }

    /* Styles pour les boutons */
    .btn {
        border-radius: 0.75rem; /* Coins arrondis pour les boutons */
        transition: all 0.3s ease; /* Transition douce pour les effets au survol */
        font-weight: 500;
    }

    .btn-primary {
        background-image: linear-gradient(to right, #6a11cb 0%, #2575fc 100%); /* Dégradé de couleur */
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Ombre douce */
    }

    .btn-primary:hover {
        transform: translateY(-3px); /* Effet de léger soulèvement */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }
    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: #fff;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: #fff;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #138496;
    }


    /* Styles pour les cartes (conteneur du tableau) */
    .card {
        border: none;
        border-radius: 1.25rem; /* Coins arrondis plus prononcés */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Ombre plus douce et plus présente */
        overflow: hidden; /* S'assure que les coins arrondis sont respectés par le contenu */
    }

    /* Styles pour le tableau */
    .table {
        margin-bottom: 0; /* Supprime la marge en bas du tableau */
    }

    .table-hover tbody tr:hover {
        background-color: #e9ecef; /* Couleur de survol plus distincte */
        transform: translateY(-2px) scale(1.005); /* Léger soulèvement et agrandissement au survol */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Ombre légère au survol */
        transition: all 0.3s ease-in-out;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6; /* Bordure plus prononcée pour l'en-tête */
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa; /* Fond légèrement grisé pour l'en-tête */
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle; /* Centre verticalement le contenu des cellules */
    }

    /* Badges */
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
        border-radius: 0.5rem;
        font-weight: 600;
    }

    /* Conteneur des actions pour un meilleur alignement */
    .btn-group {
        display: flex;
        gap: 0.5rem; /* Espacement entre les boutons */
    }

    /* Pagination */
    .pagination .page-item .page-link {
        border-radius: 0.5rem;
        margin: 0 0.2rem;
        color: #007bff;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    /* Titre de la page */
    h2 {
        font-weight: 700;
        color: #343a40;
    }

    /* Alertes */
    .alert {
        border-radius: 0.75rem;
        font-weight: 500;
    }

    /* Tooltip customisation */
    .tooltip-inner {
        background-color: #343a40;
        color: #fff;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
    }
    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #343a40;
    }
    .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
        border-bottom-color: #343a40;
    }

    /* Style pour les champs "Non renseigné" */
    .text-not-set {
        font-style: italic;
        color: #888; /* Couleur grise pour indiquer que ce n'est pas renseigné */
    }
    
    /* Select2 customisation */
    .select2-container--bootstrap-5 .select2-selection--single {
        border-radius: 0.75rem !important; /* Coins arrondis pour select2 */
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">📦 Gestion des Livraisons</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createDeliveryModal">
            <i class="fas fa-plus-circle me-1"></i> Nouvelle Livraison
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Section de filtrage --}}
    <form action="{{ route('deliveries.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="order_id" class="form-label">Commande</label>
                <select class="form-select" id="order_id" name="order_id">
                    <option value="">Toutes les commandes</option>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>
                            Commande #{{ $order->id }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="delivery_route_id" class="form-label">Tournée</label>
                <select class="form-select" id="delivery_route_id" name="delivery_route_id">
                    <option value="">Toutes les tournées</option>
                    @foreach ($deliveryRoutes as $route)
                        <option value="{{ $route->id }}" {{ request('delivery_route_id') == $route->id ? 'selected' : '' }}>
                            Tournée du {{ $route->delivery_date->format('d/m/Y') }}  par {{ $route->driver->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Statut</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="En cours" {{ request('status') == 'En cours' ? 'selected' : '' }}>En cours</option>
                    <option value="Terminée" {{ request('status') == 'Terminée' ? 'selected' : '' }}>Terminée</option>
                    <option value="Annulée" {{ request('status') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                </select>
            </div>
            <div class="col-md-3 d-grid gap-2">
                <label for="delivered_at" class="form-label">Date de livraison</label>
                <input type="date" class="form-control" id="delivered_at" name="delivered_at" value="{{ request('delivered_at') }}">
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('deliveries.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Commande</th>
                            <th>Tournée</th>
                            <th>Statut</th>
                            <th>Date de livraison</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($deliveries as $delivery)
                        <tr>
                            <td>{{ $delivery->id }}</td>
                            <td>Commande #{{ $delivery->order->id }}</td>
                            <td>Tournée du {{ $delivery->deliveryRoute->delivery_date->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $delivery->status === 'Terminée' ? 'bg-success' : ($delivery->status === 'En cours' ? 'bg-info' : 'bg-secondary') }}">{{ $delivery->status }}</span></td>
                            <td class="{{ $delivery->delivered_at ? '' : 'text-not-set' }}">{{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('deliveries.show', $delivery) }}" class="btn btn-sm btn-info" title="Voir les détails"><i class="fas fa-eye"></i></a>
                                    <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editDeliveryModal{{ $delivery->id }}" title="Modifier la livraison"><i class="fas fa-edit"></i></button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteDeliveryModal{{ $delivery->id }}" title="Supprimer la livraison"><i class="fas fa-trash-alt"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune livraison trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $deliveries->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

{{-- Modales --}}
@include('deliveries.partials.create_modal', ['orders' => $orders, 'deliveryRoutes' => $deliveryRoutes])
@foreach ($deliveries as $delivery)
    @include('deliveries.partials.edit_modal', ['delivery' => $delivery, 'orders' => $orders, 'deliveryRoutes' => $deliveryRoutes])
    @include('deliveries.partials.delete_modal', ['delivery' => $delivery])
@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#order_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Sélectionnez une commande",
            allowClear: true
        });
        $('#delivery_route_id').select2({
            theme: 'bootstrap-5',
            placeholder: "Sélectionnez une tournée",
            allowClear: true
        });
        // Initialiser les tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection