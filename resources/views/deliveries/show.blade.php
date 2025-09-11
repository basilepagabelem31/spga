@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Détails de la Livraison #{{ $delivery->id }}</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Informations sur la Livraison</h5>
            <p><strong>Commande :</strong> #{{ $delivery->order->id }}</p>
            <p><strong>Tournée :</strong> {{ $delivery->deliveryRoute->delivery_date->format('d/m/Y') }} (Chauffeur: {{ $delivery->deliveryRoute->driver->name }})</p>
            <p><strong>Statut :</strong> {{ $delivery->status }}</p>
            <p><strong>Date de livraison :</strong> {{ $delivery->delivered_at ? $delivery->delivered_at->format('d/m/Y H:i') : 'N/A' }}</p>
            <p><strong>Notes :</strong> {{ $delivery->notes ?? 'N/A' }}</p>
        </div>
    </div>
    <a href="{{ route('deliveries.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>
</div>
@endsection