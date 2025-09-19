@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">📅 Dates de récolte pour le suivi #{{ $productionFollowUp->id }}</h2>
        <a href="{{ route('production_follow_ups.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour aux suivis
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card shadow rounded-4 mb-4">
        <div class="card-body">
            <h5 class="card-title">Informations sur le suivi de production</h5>
            <p><strong>Site de production :</strong> {{ $productionFollowUp->production_site }}</p>
            <p><strong>Culture :</strong> {{ $productionFollowUp->culture_name }}</p>
            <a href="{{ route('production_follow_ups.show', $productionFollowUp) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye me-1"></i> Voir le détail du suivi
            </a>
        </div>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des dates</h5>
            <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createHarvestDateModal">
                <i class="fas fa-plus-circle me-1"></i> Ajouter une date
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom de la spéculation</th>
                            <th>Date estimée</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($estimatedHarvestDates as $estimatedHarvestDate)
                            <tr>
                                <td>{{ $estimatedHarvestDate->speculation_name }}</td>
                                <td>{{ $estimatedHarvestDate->estimated_date->format('d/m/Y') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        {{-- Modifier --}}
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editHarvestDateModal{{ $estimatedHarvestDate->id }}" title="Modifier la date">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        {{-- Supprimer --}}
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteHarvestDateModal{{ $estimatedHarvestDate->id }}" title="Supprimer la date">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Modales pour chaque date --}}
                            @include('estimated_harvest_dates.partials.edit_modal', ['productionFollowUp' => $productionFollowUp, 'estimatedHarvestDate' => $estimatedHarvestDate])
                            @include('estimated_harvest_dates.partials.delete_modal', ['productionFollowUp' => $productionFollowUp, 'estimatedHarvestDate' => $estimatedHarvestDate])
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Aucune date de récolte estimée trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
        {{ $estimatedHarvestDates->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

{{-- Modale d'ajout de date --}}
@include('estimated_harvest_dates.partials.create_modal', ['productionFollowUp' => $productionFollowUp])
@endsection