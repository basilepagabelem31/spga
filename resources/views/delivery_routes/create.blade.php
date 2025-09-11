@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Créer une Nouvelle Tournée</h2>
    <form action="{{ route('delivery_routes.store') }}" method="POST">
        @csrf
        @include('delivery_routes.form')
        <button type="submit" class="btn btn-primary">Créer la Tournée</button>
    </form>
</div>
@endsection