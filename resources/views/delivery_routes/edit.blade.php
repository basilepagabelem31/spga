@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier la Tournée #{{ $deliveryRoute->id }}</h2>
    <form action="{{ route('delivery_routes.update', $deliveryRoute) }}" method="POST">
        @csrf
        @method('PUT')
        @include('delivery_routes.form', ['deliveryRoute' => $deliveryRoute])
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
@endsection