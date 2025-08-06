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

    /* Style pour les images de produit */
    .product-thumbnail {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
    }

    /* Styles spécifiques pour les colonnes du tableau pour éviter le wrapping */
    .table thead th:nth-child(1), /* Image */
    .table tbody td:nth-child(1) {
        min-width: 80px;
    }
    .table thead th:nth-child(2), /* Nom */
    .table tbody td:nth-child(2) {
        min-width: 150px; /* Assure un minimum de largeur pour le nom */
    }

    .table thead th:nth-child(3), /* Catégorie */
    .table tbody td:nth-child(3) {
        min-width: 120px;
    }

    .table thead th:nth-child(4), /* Provenance */
    .table tbody td:nth-child(4) {
        min-width: 180px;
    }

    .table thead th:nth-child(5), /* Mode Prod. */
    .table tbody td:nth-child(5) {
        min-width: 120px;
    }

    .table thead th:nth-child(6), /* Prix Unitaire */
    .table tbody td:nth-child(6) {
        min-width: 120px;
    }

    .table thead th:nth-child(7), /* Unité Vente */
    .table tbody td:nth-child(7) {
        min-width: 100px;
    }
    
    .table thead th:nth-child(8), /* Stock Actuel */
    .table tbody td:nth-child(8) {
        min-width: 120px;
    }

    .table thead th:nth-child(9), /* Statut */
    .table tbody td:nth-child(9) {
        min-width: 100px;
    }

    .table thead th:nth-child(10), /* Actions */
    .table tbody td:nth-child(10) {
        min-width: 120px; /* Pour les boutons d'action */
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">📦 Gestion des Produits</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createProductModal">
            <i class="fas fa-plus-circle me-1"></i> Ajouter un produit
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

    {{-- Section de filtrage --}}
    <form action="{{ route('products.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche rapide</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Nom ou description..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <label for="filter_category" class="form-label">Filtrer par catégorie</label>
                <select class="form-select" id="filter_category" name="category_id">
                    <option value="">Toutes les catégories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_provenance_type" class="form-label">Filtrer par provenance</label>
                <select class="form-select" id="filter_provenance_type" name="provenance_type">
                    <option value="">Tous les types</option>
                    <option value="ferme_propre" {{ request('provenance_type') == 'ferme_propre' ? 'selected' : '' }}>Ferme Propre</option>
                    <option value="producteur_partenaire" {{ request('provenance_type') == 'producteur_partenaire' ? 'selected' : '' }}>Producteur Partenaire</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_production_mode" class="form-label">Filtrer par mode de production</label>
                <select class="form-select" id="filter_production_mode" name="production_mode">
                    <option value="">Tous les modes</option>
                    <option value="bio" {{ request('production_mode') == 'bio' ? 'selected' : '' }}>Bio</option>
                    <option value="agroécologie" {{ request('production_mode') == 'agroécologie' ? 'selected' : '' }}>Agroécologie</option>
                    <option value="conventionnel" {{ request('production_mode') == 'conventionnel' ? 'selected' : '' }}>Conventionnel</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="filter_status" class="form-label">Filtrer par statut</label>
                <select class="form-select" id="filter_status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="disponible" {{ request('status') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                    <option value="indisponible" {{ request('status') == 'indisponible' ? 'selected' : '' }}>Indisponible</option>
                </select>
            </div>
            <div class="col-md-4 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Catégorie</th>
                            <th>Provenance</th>
                            <th>Mode Prod.</th>
                            <th>Prix Unitaire</th>
                            <th>Unité Vente</th>
                            <th>Stock Actuel</th> {{-- NOUVEAU --}}
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="Image produit" class="product-thumbnail">
                                    @else
                                        <img src="https://placehold.co/50x50/e0e0e0/808080?text=No+Img" alt="Pas d'image" class="product-thumbnail">
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td><span class="badge bg-secondary">{{ $product->category->name ?? 'Catégorie inconnue' }}</span></td>
                                <td>
                                    @if ($product->provenance_type === 'ferme_propre')
                                        <span class="badge bg-info">Ferme Propre</span>
                                    @elseif ($product->provenance_type === 'producteur_partenaire')
                                        <span class="badge bg-success">Partenaire: {{ $product->provenanceName ?? 'Inconnu' }}</span>
                                    @else
                                        <span class="badge bg-warning">Non défini</span>
                                    @endif
                                </td>
                                <td><span class="badge bg-dark">{{ ucfirst($product->production_mode) }}</span></td>
                                <td>{{ number_format($product->unit_price, 2, ',', ' ') }} FCFA</td>
                                <td>{{ $product->sale_unit }}</td>
                                <td>
                                    @if ($product->current_stock_quantity !== null)
                                        <span class="badge {{ $product->isLowStock() ? 'bg-warning text-dark' : 'bg-success' }}">
                                            {{ number_format($product->current_stock_quantity, 2, ',', ' ') }} {{ $product->sale_unit }}
                                        </span>
                                    @else
                                        <span class="text-not-set">N/A</span>
                                    @endif
                                </td> {{-- NOUVEAU --}}
                                <td>
                                    @if ($product->status === 'disponible')
                                        <span class="badge bg-success">Disponible</span>
                                    @else
                                        <span class="badge bg-danger">Indisponible</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Modifier -->
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editProductModal{{ $product->id }}" title="Modifier le produit">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Supprimer -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProductModal{{ $product->id }}" title="Supprimer le produit">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales pour chaque produit --}}
                            @include('products.partials.edit_modal', ['product' => $product, 'categories' => $categories, 'partners' => $partners])
                            @include('products.partials.delete_modal', ['product' => $product])

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Aucun produit trouvé.</td> {{-- Colonnes ajustées --}}
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->links() }}
    </div>
</div>

{{-- Modale d'ajout de produit --}}
@include('products.partials.create_modal', ['categories' => $categories, 'partners' => $partners])

{{-- Script pour initialiser les tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
