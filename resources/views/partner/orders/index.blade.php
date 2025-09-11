@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h1 class="h2 fw-bold text-gray-800">
            <i class="fas fa-shopping-basket me-2 text-primary"></i> Mes Commandes
        </h1>
        {{-- Bouton "Nouvelle commande" si c'est pertinent pour le partenaire --}}
        {{-- <a href="#" class="btn btn-primary shadow-sm rounded-pill px-4 py-2">
            <i class="fas fa-plus me-1"></i> Nouvelle Commande
        </a> --}}
    </div>

    {{-- Formulaire de recherche et de filtrage --}}
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('partenaire.orders') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6">
                    <label for="search" class="visually-hidden">Rechercher une commande</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Rechercher par code ou client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="status" class="visually-hidden">Filtrer par statut</label>
                    <select name="status" id="status" class="form-select">
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="alert alert-info border-start border-4 border-info py-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h4 class="alert-heading fw-bold">Information</h4>
                    <p class="mb-0">
                        @if(request()->filled('search') || request()->filled('status'))
                            Aucune commande ne correspond à votre recherche.
                        @else
                            Vous n'avez pas encore de commandes. Nous vous encourageons à explorer nos produits !
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Commande #</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Client</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Date</th>
                                <th scope="col" class="px-3 py-3 text-uppercase fw-bold text-muted text-nowrap">Montant Total</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Livraison</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Paiement</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Statut</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Validé par</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-end text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr class="transition-transform-hover">
                                    <td class="px-4 py-3 fw-medium text-gray-900 text-nowrap">{{ $order->order_code }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $order->client->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-nowrap">{{ $order->order_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-nowrap">{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</td>
                                    <td class="px-4 py-3"><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->delivery_mode)) }}</span></td>
                                    <td class="px-4 py-3"><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $order->payment_mode)) }}</span></td>
                                    <td class="px-4 py-3 text-nowrap">
                                        <span class="badge {{ $order->getBadgeClass() }} d-inline-flex align-items-center px-3 py-2 rounded-pill">
                                            @php
                                                $status = $order->status;
                                                $icon = '';
                                                switch($status) {
                                                    case 'En attente de validation': $icon = '⏳'; break;
                                                    case 'Validée': $icon = '👍'; break;
                                                    case 'En préparation': $icon = '📦'; break;
                                                    case 'En Livraison': $icon = '🚚'; break;
                                                    case 'Livrée': $icon = '✅'; break;
                                                    case 'Terminée': $icon = '✅'; break;
                                                    case 'Annulée': $icon = '❌'; break;
                                                    default: $icon = '❓'; break;
                                                }
                                            @endphp
                                            {{ $icon }} {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-nowrap">
                                        @if ($order->validatedBy)
                                            <span class="d-inline-block text-truncate" style="max-width: 150px;">
                                                {{ $order->validatedBy->full_name ?? $order->validatedBy->email }}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">En attente</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill show-order-details" data-bs-toggle="modal" data-bs-target="#orderDetailsModal" data-order-id="{{ $order->id }}">
                                            <i class="fas fa-eye me-1"></i> Détails
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
</div>

{{-- Structure de la modale vide --}}
<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Détails de la commande</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const orderDetailsModal = document.getElementById('orderDetailsModal');
        const modalBody = orderDetailsModal.querySelector('.modal-body');

        document.querySelectorAll('.show-order-details').forEach(button => {
            button.addEventListener('click', function () {
                const orderId = this.getAttribute('data-order-id');
                const url = `/partenaire/orders/details/${orderId}`;
                
                modalBody.innerHTML = `
                    <div class="d-flex justify-content-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                `;

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur de réseau ou accès non autorisé.');
                        }
                        return response.text();
                    })
                    .then(html => {
                        modalBody.innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Erreur lors du chargement des détails de la commande:', error);
                        modalBody.innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                Une erreur est survenue lors du chargement des détails. Veuillez réessayer.
                            </div>
                        `;
                    });
            });
        });
    });
</script>

<style>
    /* Styles personnalisés pour un meilleur design */
    body { background-color: #f8f9fa; }
    .text-gray-800 { color: #212529; }
    .card { border-radius: 1.25rem; }
    .table thead th { font-weight: 700; color: #6c757d; border-bottom-width: 2px; }
    .table-hover tbody tr:hover { background-color: #f1f3f5; }
    .transition-transform-hover:hover { transform: scale(1.005); transition: transform 0.2s ease-in-out; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); }
    .badge { font-weight: 500; letter-spacing: 0.5px; padding: 0.5em 0.8em; }
    .text-not-set { color: #adb5bd; font-style: italic; }
</style>
@endsection