@extends('layouts.app') {{-- Assurez-vous d'avoir un layout principal --}}

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
        text-transform: uppercase;
        letter-spacing: 0.05em;
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
    
    .btn-outline-primary {
        color: #6a11cb;
        border-color: #6a11cb;
    }
    .btn-outline-primary:hover {
        background-color: #6a11cb;
        color: #fff;
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
        width: 100%; /* S'assure que le tableau prend toute la largeur disponible */
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
        text-transform: uppercase;
        font-size: 0.85rem;
        white-space: nowrap; 
    }
    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle; /* Centre verticalement le contenu des cellules */
        font-size: 0.95rem;
    }

    /* Badges */
    .badge {
        font-size: 0.8em;
        padding: 0.5em 0.8em;
        border-radius: 0.5rem;
        font-weight: 600;
        text-transform: capitalize;
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
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    /* Tooltip customisation */
    .tooltip-inner {
        background-color: #343a40;
        color: #fff;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
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

    /* Styles spécifiques pour les colonnes du tableau pour éviter le wrapping */
    .table thead th, .table tbody td {
        min-width: 120px;
    }
    .table thead th:nth-child(1), .table tbody td:nth-child(1) { min-width: 150px; }
    .table thead th:nth-child(2), .table tbody td:nth-child(2) { min-width: 150px; }
    .table thead th:nth-child(3), .table tbody td:nth-child(3) { min-width: 180px; }
    .table thead th:nth-child(4), .table tbody td:nth-child(4) { min-width: 120px; }
    .table thead th:nth-child(5), .table tbody td:nth-child(5) { min-width: 150px; }
    .table thead th:nth-child(6), .table tbody td:nth-child(6) { min-width: 150px; }
    .table thead th:nth-child(7), .table tbody td:nth-child(7) { min-width: 150px; }
    .table thead th:nth-child(8), .table tbody td:nth-child(8) { min-width: 150px; }
    .table thead th:nth-child(9), .table tbody td:nth-child(9) { min-width: 150px; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">🛒 Gestion des Commandes</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createOrderModal">
            <i class="fas fa-plus-circle me-1"></i> Créer une commande
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="alert alert-info shadow-sm">{{ session('info') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <h4 class="alert-heading">Erreurs de validation</h4>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Section de filtrage --}}
    <form action="{{ route('orders.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="order_code_search" class="form-label">Code Commande</label>
                <input type="text" class="form-control" id="order_code_search" name="order_code_search" placeholder="Rechercher par code..." value="{{ request('order_code_search') }}">
            </div>
            <div class="col-md-4">
                <label for="filter_client" class="form-label">Client</label>
                <select class="form-select" id="filter_client" name="client_id">
                    <option value="">Tous les clients</option>
                    @foreach ($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->full_name ?? $client->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_status" class="form-label">Statut</label>
                <select class="form-select" id="filter_status" name="status">
                    <option value="">Tous les statuts</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="order_date_from" class="form-label">Date de commande (Du)</label>
                <input type="date" class="form-control" id="order_date_from" name="order_date_from" value="{{ request('order_date_from') }}">
            </div>
            <div class="col-md-4">
                <label for="order_date_to" class="form-label">Date de commande (Au)</label>
                <input type="date" class="form-control" id="order_date_to" name="order_date_to" value="{{ request('order_date_to') }}">
            </div>
            <div class="col-md-4 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code Commande</th>
                            <th>Client</th>
                            <th>Date Commande</th>
                            <th>Montant Total</th>
                            <th>Mode Livraison</th>
                            <th>Mode Paiement</th>
                            <th>Statut</th>
                            <th>Validé par</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr class="transition-transform-hover">
                                <td><span class="badge bg-dark">{{ $order->order_code }}</span></td>
                                <td>{{ $order->client->full_name ?? $order->client->email ?? 'Client inconnu' }}</td>
                                <td>{{ $order->order_date->format('d/m/Y H:i') }}</td>
                                <td>{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</td>
                                <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $order->delivery_mode)) }}</span></td>
                                <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $order->payment_mode)) }}</span></td>
                                <td>
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
                                </td>
                                <td>
                                    @if ($order->validatedBy)
                                        {{ $order->validatedBy->full_name ?? $order->validatedBy->email }}
                                    @else
                                        <span class="text-not-set">Non validé</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewOrderModal{{ $order->id }}" title="Détails de la commande">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editOrderModal{{ $order->id }}" title="Modifier la commande">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteOrderModal{{ $order->id }}" title="Supprimer la commande">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                          

                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i><br>
                                    Aucune commande trouvée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>


@foreach ($orders as $order)
                         {{-- Modales pour chaque commande --}}
                            @include('orders.partials.view_modal', ['order' => $order])
                            @include('orders.partials.edit_modal', ['order' => $order, 'clients' => $clients, 'products' => $products, 'validators' => $validators, 'statuses' => $statuses])
                            @include('orders.partials.delete_modal', ['order' => $order])
                            @endforeach

            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $orders->links() }}
    </div>
</div>

{{-- Modale d'ajout de commande --}}
@include('orders.partials.create_modal', ['clients' => $clients, 'products' => $products, 'validators' => $validators, 'statuses' => $statuses])

{{-- Script pour initialiser les tooltips Bootstrap et Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Initialisation de Select2 pour les filtres et modales
        $('.form-select').select2({
            placeholder: "Sélectionner une option",
            allowClear: true,
            width: '100%',
            dropdownParent: $('body')
        });

        // Cacher les tooltips lors de l'affichage des modales
        $('.modal').on('show.bs.modal', function() {
            $('.tooltip').tooltip('hide');
        });
    });
</script>
@endsection