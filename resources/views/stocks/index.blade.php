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
        
        /* Empêche le texte de l'en-tête de revenir à la ligne */
        white-space: nowrap; 
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

    /* Styles spécifiques pour les colonnes du tableau pour éviter le wrapping */
    .table thead th:nth-child(1), /* Produit */
    .table tbody td:nth-child(1) {
        min-width: 180px;
    }
    .table thead th:nth-child(2), /* Quantité Mouvement */
    .table tbody td:nth-child(2) {
        min-width: 100px;
    }
    .table thead th:nth-child(3), /* Type Mouvement */
    .table tbody td:nth-child(3) {
        min-width: 150px;
    }
    .table thead th:nth-child(4), /* Référence */
    .table tbody td:nth-child(4) {
        min-width: 150px;
    }
    .table thead th:nth-child(5), /* Seuil Alerte */
    .table tbody td:nth-child(5) {
        min-width: 120px;
    }
    .table thead th:nth-child(6), /* Date Mouvement */
    .table tbody td:nth-child(6) {
        min-width: 150px;
    }
    .table thead th:nth-child(7), /* Stock Actuel Produit */
    .table tbody td:nth-child(7) {
        min-width: 150px;
    }
    .table thead th:nth-child(8), /* Actions */
    .table tbody td:nth-child(8) {
        min-width: 120px; /* Pour les boutons d'action */
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">📦 Gestion du Stock</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createStockModal">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un mouvement
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
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Section de filtrage --}}
    <form action="{{ route('stocks.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="filter_product" class="form-label">Filtrer par produit</label>
                <select class="form-select" id="filter_product" name="product_id">
                    <option value="">Tous les produits</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_movement_type" class="form-label">Filtrer par type de mouvement</label>
                <select class="form-select" id="filter_movement_type" name="movement_type">
                    <option value="">Tous les types</option>
                    @foreach ($movementTypes as $type)
                        <option value="{{ $type }}" {{ request('movement_type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="reference_id_search" class="form-label">Recherche par référence</label>
                <input type="text" class="form-control" id="reference_id_search" name="reference_id_search" placeholder="Référence..." value="{{ request('reference_id_search') }}">
            </div>
            <div class="col-md-4">
                <label for="movement_date_from" class="form-label">Date de mouvement (du)</label>
                <input type="date" class="form-control" id="movement_date_from" name="movement_date_from" value="{{ request('movement_date_from') }}">
            </div>
            <div class="col-md-4">
                <label for="movement_date_to" class="form-label">Date de mouvement (au)</label>
                <input type="date" class="form-control" id="movement_date_to" name="movement_date_to" value="{{ request('movement_date_to') }}">
            </div>
            <div class="col-md-4 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th>Quantité Mouvement</th>
                            <th>Type Mouvement</th>
                            <th>Référence</th>
                            <th>Seuil Alerte</th>
                            <th>Date Mouvement</th>
                            <th>Stock Actuel Produit</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stocks as $stock)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $stock->product->name ?? 'Produit inconnu' }}</span></td>
                                <td>
                                    {{ number_format($stock->quantity, 2, ',', ' ') }} 
                                    @if ($stock->product)
                                        {{ $stock->product->sale_unit }}
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $typeClass = '';
                                        switch ($stock->movement_type) {
                                            case 'entrée': $typeClass = 'bg-success'; break;
                                            case 'sortie': $typeClass = 'bg-danger'; break;
                                            case 'future_recolte': $typeClass = 'bg-info'; break;
                                            default: $typeClass = 'bg-secondary'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $typeClass }}">{{ ucfirst($stock->movement_type) }}</span>
                                </td>
                                <td class="{{ $stock->reference_id ? '' : 'text-not-set' }}">{{ $stock->reference_id ?? 'Non renseigné' }}</td>
                                <td>
                                    @if ($stock->product && $stock->product->alert_threshold !== null) {{-- NOUVEAU --}}
                                        {{ number_format($stock->product->alert_threshold, 2, ',', ' ') }}
                                        @if ($stock->product)
                                            {{ $stock->product->sale_unit }}
                                        @endif
                                    @else
                                        <span class="text-not-set">Non renseigné</span>
                                    @endif
                                    @if ($stock->product && $stock->product->isLowStock()) {{-- NOUVEAU --}}
                                        <span class="badge bg-warning text-dark ms-1" data-bs-toggle="tooltip" title="Stock faible !">⚠️</span>
                                    @endif
                                </td>
                                <td class="{{ $stock->movement_date ? '' : 'text-not-set' }}">{{ $stock->movement_date ? $stock->movement_date->format('d/m/Y H:i') : 'Non renseigné' }}</td>
                                <td>
                                    @if ($stock->product && $stock->product->current_stock_quantity !== null)
                                        <span class="badge {{ $stock->product->isLowStock() ? 'bg-warning text-dark' : 'bg-success' }}">
                                            {{ number_format($stock->product->current_stock_quantity, 2, ',', ' ') }} {{ $stock->product->sale_unit }}
                                        </span>
                                    @else
                                        <span class="text-not-set">N/A</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Modifier -->
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editStockModal{{ $stock->id }}" title="Modifier le mouvement">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Supprimer -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteStockModal{{ $stock->id }}" title="Supprimer le mouvement">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales pour chaque mouvement de stock --}}
                            @include('stocks.partials.edit_modal', ['stock' => $stock, 'products' => $products, 'movementTypes' => $movementTypes])
                            @include('stocks.partials.delete_modal', ['stock' => $stock])

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Aucun mouvement de stock trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $stocks->links() }}
    </div>
</div>

{{-- Modale d'ajout de mouvement de stock --}}
@include('stocks.partials.create_modal', ['products' => $products, 'movementTypes' => $movementTypes])

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

        // Initialisation de Select2 pour les filtres
        $('#filter_product').select2({
            placeholder: "Sélectionner un produit",
            allowClear: true,
            width: '100%'
        });
        $('#filter_movement_type').select2({
            placeholder: "Sélectionner un type",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endsection
