@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Détails de la Tournée #{{ $deliveryRoute->id }}</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Tournée du {{ $deliveryRoute->delivery_date->format('d/m/Y') }}</h5>
            <p><strong>Chauffeur :</strong> {{ $deliveryRoute->driver->name }}</p>
            <p><strong>Véhicule :</strong> {{ $deliveryRoute->vehicle_info ?? 'N/A' }}</p>
            <p><strong>Statut :</strong> {{ $deliveryRoute->status }}</p>
            <hr>
            <h4>Livraisons de cette tournée</h4>
            @forelse ($deliveryRoute->deliveries as $delivery)
                <p><strong>Commande #{{ $delivery->order->id }}</strong> - Statut : {{ $delivery->status }}</p>
            @empty
                <p>Aucune livraison n'est encore associée à cette tournée.</p>
            @endforelse
        </div>
    </div>
    <a href="{{ route('delivery_routes.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>
</div>
@endsection