@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">Détails du suivi : {{ $productionFollowUp->id }}</h2>
        <a href="{{ route('production_follow_ups.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow rounded-4 p-4 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4 class="card-title mb-3">Informations générales</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Site de production :</strong>
                            <span>{{ $productionFollowUp->production_site }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Commune :</strong>
                            <span class="{{ $productionFollowUp->commune ? '' : 'text-not-set' }}">{{ $productionFollowUp->commune ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Village :</strong>
                            <span class="{{ $productionFollowUp->village ? '' : 'text-not-set' }}">{{ $productionFollowUp->village ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Nom du producteur :</strong>
                            <span class="{{ $productionFollowUp->producer_name ? '' : 'text-not-set' }}">{{ $productionFollowUp->producer_name ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Agent technique :</strong>
                            <span class="{{ $productionFollowUp->technical_agent_name ? '' : 'text-not-set' }}">{{ $productionFollowUp->technical_agent_name ?? 'Non renseigné' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h4 class="card-title mb-3">Détails de la culture</h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Date de suivi :</strong>
                            <span>{{ $productionFollowUp->follow_up_date->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Culture :</strong>
                            <span>{{ $productionFollowUp->culture_name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Variété :</strong>
                            <span class="{{ $productionFollowUp->cultivated_variety ? '' : 'text-not-set' }}">{{ $productionFollowUp->cultivated_variety ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Date de semis :</strong>
                            <span class="{{ $productionFollowUp->sowing_planting_date ? '' : 'text-not-set' }}">{{ $productionFollowUp->sowing_planting_date ? $productionFollowUp->sowing_planting_date->format('d/m/Y') : 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Surface cultivée :</strong>
                            <span class="{{ $productionFollowUp->cultivated_surface ? '' : 'text-not-set' }}">{{ $productionFollowUp->cultivated_surface ? $productionFollowUp->cultivated_surface . ' ha' : 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Type de production :</strong>
                            <span><span class="badge bg-secondary">{{ $productionFollowUp->production_type }}</span></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Stade de développement :</strong>
                            <span class="{{ $productionFollowUp->development_stage ? '' : 'text-not-set' }}">{{ $productionFollowUp->development_stage ?? 'Non renseigné' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <h4 class="card-title mb-3">Observations et recommandations</h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <strong>Travaux réalisés :</strong>
                        <p class="{{ $productionFollowUp->works_performed ? '' : 'text-not-set' }}">{{ $productionFollowUp->works_performed ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Observations techniques :</strong>
                        <p class="{{ $productionFollowUp->technical_observations ? '' : 'text-not-set' }}">{{ $productionFollowUp->technical_observations ?? 'Non renseigné' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Interventions recommandées :</strong>
                        <p class="{{ $productionFollowUp->recommended_interventions ? '' : 'text-not-set' }}">{{ $productionFollowUp->recommended_interventions ?? 'Non renseigné' }}</p>
                    </div>
                </div>
            </div>
           


<div>
                @if ($productionFollowUp->responsible_signature)
                    <img src="{{ $productionFollowUp->responsible_signature }}" alt="Signature du responsable" style="max-height: 50px; width: auto; border: 1px solid #ddd; border-radius: 4px;">
                @else
                    <span class="text-muted">Non signé</span>
                @endif
            </div>


        </div>
    </div>

    <div class="card shadow rounded-4 mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">📅 Dates de récolte estimées</h5>
            {{-- Le bouton pour accéder à la gestion des dates --}}
            <a href="{{ route('production_follow_ups.estimated_harvest_dates.index', $productionFollowUp) }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-calendar-alt me-1"></i> Gérer les dates
            </a>
        </div>
        <div class="card-body p-0">
            @if ($productionFollowUp->estimatedHarvestDates->isEmpty())
                <div class="p-4 text-center text-muted">
                    Aucune date de récolte estimée n'a été ajoutée pour ce suivi.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nom de la spéculation</th>
                                <th>Date estimée</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($productionFollowUp->estimatedHarvestDates as $date)
                                <tr>
                                    <td>{{ $date->speculation_name }}</td>
                                    <td>{{ $date->estimated_date->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection