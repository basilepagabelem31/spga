@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Modifier la Livraison #{{ $delivery->id }}</h2>
    <form action="{{ route('deliveries.update', $delivery) }}" method="POST">
        @csrf
        @method('PUT')
        @include('deliveries.form', ['delivery' => $delivery])
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
@endsection