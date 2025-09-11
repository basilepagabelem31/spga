@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">Détails de la date de récolte estimée</h2>
        <a href="{{ route('estimated_harvest_dates.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow rounded-4 p-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Informations sur la date de récolte</h4>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>ID du suivi de production :</strong>
                    <a href="{{ route('production_follow_ups.show', $estimatedHarvestDate->productionFollowUp) }}">
                        #{{ $estimatedHarvestDate->production_follow_up_id }}
                    </a>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Nom de la spéculation :</strong>
                    <span>{{ $estimatedHarvestDate->speculation_name }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <strong>Date estimée :</strong>
                    <span>{{ $estimatedHarvestDate->estimated_date->format('d/m/Y') }}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection