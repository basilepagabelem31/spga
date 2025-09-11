@extends('layouts.app')

@section('title', 'Catalogue de Produits')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Catalogue de Produits</h1>
    <p class="mb-4">Parcourez les produits disponibles pour passer une nouvelle commande.</p>

    {{-- Formulaire de recherche amélioré --}}
    <div class="row justify-content-center mb-4">
        <div class="col-md-6">
            <form action="{{ route('client.products') }}" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un produit par nom..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Grille de produits améliorée --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @forelse ($products as $product)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 rounded-3">
                    {{-- Affichage de l'image du produit --}}
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top rounded-top-3" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        {{-- Image par défaut si aucune image n'est disponible --}}
                        <img src="{{ asset('images/default_product.jpg') }}" class="card-img-top rounded-top-3" alt="Image par défaut" style="height: 200px; object-fit: cover;">
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold text-primary">{{ $product->name }}</h5>
                        <p class="card-text text-muted flex-grow-1">{{ $product->description }}</p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="fs-5 fw-bold text-success">{{ number_format($product->unit_price, 2, ',', ' ') }} FCFA</span>
                            <small class="text-muted">/ {{ $product->sale_unit }}</small>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0 text-end">
                        {{-- Bouton pour ouvrir la modale --}}
                        <button type="button" class="btn btn-primary btn-sm rounded-pill view-product-btn" data-bs-toggle="modal" data-bs-target="#productDetailsModal" data-product-id="{{ $product->id }}">
                            <i class="fas fa-eye me-1"></i> Voir plus
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center" role="alert">
                    Aucun produit ne correspond à votre recherche.
                </div>
            </div>
        @endforelse
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(request()->input())->links() }}
    </div>
</div>

<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailsModalLabel">Détails du Produit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="modal-content-container">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const productDetailsModal = document.getElementById('productDetailsModal');
        const modalContentContainer = document.getElementById('modal-content-container');

        productDetailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const productId = button.getAttribute('data-product-id');
            
            const productJsonUrl = `/client/products/${productId}/show_json`;

            // Afficher le spinner de chargement
            modalContentContainer.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `;
            
            // Requête AJAX pour obtenir les détails du produit
            fetch(productJsonUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur de réseau ou de serveur.');
                }
                return response.json();
            })
            .then(data => {
                let product = data.product;

                // CORRECTION : Convertissez la quantité en nombre pour la comparaison
                let stockHtml = '';
                const stockQuantity = parseFloat(product.current_stock_quantity);

                if (stockQuantity > 0) {
                    stockHtml = `
                        <p class="card-text">
                            <span class="fw-bold">Quantité en stock:</span> 
                            <span class="text-primary">${product.current_stock_quantity} ${product.sale_unit}</span>
                        </p>
                    `;
                } else {
                    stockHtml = `<p class="card-text text-danger">Stock indisponible</p>`;
                }

                const formattedPrice = parseFloat(product.unit_price).toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                const modalHtml = `
                    <div class="row">
                        <div class="col-md-5">
                            <img src="${product.image ? '/storage/' + product.image : '/images/default_product.jpg'}" 
                                 class="img-fluid rounded-3 shadow-sm" alt="${product.name}" style="max-height: 300px; object-fit: cover;">
                        </div>
                        <div class="col-md-7">
                            <h3 class="fw-bold text-primary">${product.name}</h3>
                            <p class="lead text-muted">${product.description || ''}</p>
                            <hr>
                            <p class="fs-4 fw-bold text-success">
                                ${formattedPrice} FCFA <small class="text-muted">/ ${product.sale_unit}</small>
                            </p>
                            <p class="card-text"><span class="fw-bold">Catégorie:</span> ${product.category ? product.category.name : 'N/A'}</p>
                            ${stockHtml}
                            <p class="card-text"><span class="fw-bold">Statut:</span>
                                <span class="badge ${product.status === 'disponible' ? 'bg-success' : 'bg-danger'}">
                                    ${product.status === 'disponible' ? 'Disponible' : 'Indisponible'}
                                </span>
                            </p>
                            <p class="card-text"><span class="fw-bold">Mode de production:</span> ${product.production_mode || 'Non spécifié'}</p>
                            <p class="card-text"><span class="fw-bold">Quantité minimum de commande :</span> ${product.min_order_quantity} ${product.sale_unit}</p>

                            <p class="card-text"><span class="fw-bold">Conditionnement:</span> ${product.packaging_format || 'Non spécifié'}</p>
                        </div>
                    </div>
                `;
                modalContentContainer.innerHTML = modalHtml;
            })
            .catch(error => {
                console.error('Erreur:', error);
                modalContentContainer.innerHTML = `
                    <div class="alert alert-danger" role="alert">
                        Impossible de charger les détails du produit: ${error.message}
                    </div>
                `;
            });
        });
    });
</script>
@endsectionion