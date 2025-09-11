@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Créer une Nouvelle Livraison</h2>
    <form action="{{ route('deliveries.store') }}" method="POST">
        @csrf
        @include('deliveries.form')
        <button type="submit" class="btn btn-primary">Créer la Livraison</button>
    </form>
</div>
@endsection