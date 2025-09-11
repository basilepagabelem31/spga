@extends('layouts.app')

@section('title', 'Passer une nouvelle commande')

{{-- Inclure les fichiers CSS et JavaScript de Select2 --}}
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection


@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Passer une nouvelle commande</h1>
    <p class="mb-4">Veuillez remplir les informations pour votre nouvelle commande.</p>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus-circle me-1"></i> Formulaire de Commande
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('client.orders.store') }}" method="POST">
                @csrf

                {{-- Section du Panier --}}
                <div class="mb-4">
                    <h5>Produits de la commande</h5>
                    <div id="product-list">
                        {{-- Un produit par défaut pour commencer --}}
                        <div class="row product-item mb-2">
                            <div class="col-md-6">
                                <label for="product_0">Produit</label>
                               <select name="products[0][id]" id="product_0" class="form-control product-select">
                                    <option value="">Sélectionner un produit</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->unit_price, 2) }} FCFA / {{ $product->sale_unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="quantity_0">Quantité</label>
                                <input type="number" name="products[0][quantity]" id="quantity_0" class="form-control" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-product" class="btn btn-secondary btn-sm mt-2"><i class="fas fa-plus me-1"></i> Ajouter un autre produit</button>
                </div>

                {{-- Section Paiement & Livraison --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="payment_mode" class="form-label">Mode de Paiement</label>
                        <select name="payment_mode" id="payment_mode" class="form-control">
                            <option value="paiement_a_la_livraison">Paiement à la livraison</option>
                            <option value="virement_bancaire">Virement bancaire</option>
                            <option value="paiement_mobile">Paiement mobile</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="delivery_mode" class="form-label">Mode de Livraison</label>
                        <select name="delivery_mode" id="delivery_mode" class="form-control">
                            <option value="standard_72h">Standard (72h)</option>
                            <option value="express_6_12h">Express (6-12h)</option>
                        </select>
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label for="notes" class="form-label">Notes supplémentaires</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i> Confirmer la commande</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Inclure la bibliothèque jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Inclure le fichier JavaScript de Select2 --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    function initializeSelect2() {
        $('.product-select').select2({
            placeholder: 'Sélectionner un produit',
            allowClear: true
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        let productCount = 0; // Commencer le compteur à 0
        const productList = document.getElementById('product-list');
        const addButton = document.getElementById('add-product');
        
        // Initialiser Select2 pour le premier élément
        initializeSelect2();

        addButton.addEventListener('click', function () {
            productCount++;
            const newItem = document.createElement('div');
            newItem.classList.add('row', 'product-item', 'mb-2');
            newItem.innerHTML = `
                <div class="col-md-6">
                    <label for="product_${productCount}">Produit</label>
                    <select name="products[${productCount}][id]" id="product_${productCount}" class="form-control product-select" required>
                        <option value="">Sélectionner un produit</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ number_format($product->unit_price, 2) }} FCFA / {{ $product->sale_unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="quantity_${productCount}">Quantité</label>
                    <input type="number" name="products[${productCount}][quantity]" id="quantity_${productCount}" class="form-control" min="0.01" step="0.01" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-product"><i class="fas fa-trash"></i></button>
                </div>
            `;
            productList.appendChild(newItem);
            // Initialiser Select2 pour le nouvel élément
            initializeSelect2();
        });

        productList.addEventListener('click', function (e) {
            if (e.target.closest('.remove-product')) {
                const item = e.target.closest('.product-item');
                // S'assurer qu'au moins un élément reste
                if (productList.querySelectorAll('.product-item').length > 1) {
                    // Supprimer l'élément
                    item.remove();
                } else {
                    alert('Votre commande doit contenir au moins un produit.');
                }
            }
        });
    });
</script>
@endpush