@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-md-4">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
            @else
                <img src="{{ asset('images/default_product.jpg') }}" class="img-fluid rounded" alt="Image par défaut">
            @endif
        </div>
        <div class="col-md-8">
            <h1 class="display-4">{{ $product->name }}</h1>
            <p class="lead">{{ $product->description }}</p>
            
            <hr>

            <ul class="list-group list-group-flush mb-4">
                <li class="list-group-item"><strong>Prix :</strong> {{ number_format($product->unit_price, 2, ',', ' ') }} FCFA / {{ $product->sale_unit }}</li>
                <li class="list-group-item"><strong>Catégorie :</strong> {{ $product->category->name }}</li>
                <li class="list-group-item"><strong>Mode de production :</strong> {{ $product->production_mode }}</li>
                <li class="list-group-item"><strong>Unité de vente :</strong> <span class="fw-bold text-success"> {{ $product->sale_unit }}</span></li>
                <li class="list-group-item"><strong>Quantité minimum de commande :</strong> {{ $product->min_order_quantity }} {{ $product->sale_unit }}</li>
                <li class="list-group-item"><strong>Statut :</strong> {{ $product->status === 'disponible' ? 'Disponible' : 'Indisponible' }}</li>
            </ul>
            
            <a href="{{ route('client.orders.create') }}" class="btn btn-success btn-lg me-2">Ajouter à la commande</a>
            <a href="{{ route('client.products') }}" class="btn btn-secondary btn-lg">Retour au catalogue</a>
        </div>
    </div>
</div>
@endsection