@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">Détails du Contrôle Qualité #{{ $qualityControl->id }}</h2>
        <a href="{{ route('quality_controls.index') }}" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow rounded-4 p-4">
        <div class="card-body">
            <h4 class="card-title mb-3">Informations du contrôle</h4>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Date du contrôle :</strong>
                            <span>{{ $qualityControl->control_date->format('d/m/Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Contrôleur :</strong>
                            <span>{{ $qualityControl->controller->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Unité de production :</strong>
                            <span>{{ $qualityControl->production_unit ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Produit :</strong>
                            <span>{{ $qualityControl->product->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Référence du lot :</strong>
                            <span>{{ $qualityControl->lot_reference ?? 'Non renseigné' }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Type de contrôle :</strong>
                            <span>{{ $qualityControl->control_type ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Méthode utilisée :</strong>
                            <span>{{ $qualityControl->method_used ?? 'Non renseigné' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Résultat :</strong>
                            @php
                                $badgeClass = '';
                                switch ($qualityControl->control_result) {
                                    case 'Conforme':
                                        $badgeClass = 'bg-success';
                                        break;
                                    case 'Non conforme':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'À réévaluer':
                                        $badgeClass = 'bg-warning text-dark';
                                        break;
                                }
                            @endphp
                            <span><span class="badge {{ $badgeClass }}">{{ $qualityControl->control_result }}</span></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Signature :</strong>
                            <span>{{ $qualityControl->responsible_signature_qc ?? 'Non renseigné' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4">
            
            <h4 class="card-title mb-3">Non-conformités & Actions</h4>
            <div class="row">
                <div class="col-md-6">
                    <strong>Non-conformités observées :</strong>
                    <p class="{{ $qualityControl->observed_non_conformities ? '' : 'text-muted' }}">
                        {{ $qualityControl->observed_non_conformities ?? 'Aucune non-conformité observée.' }}
                    </p>
                </div>
                <div class="col-md-6">
                    <strong>Actions correctives proposées :</strong>
                    <p class="{{ $qualityControl->proposed_corrective_actions ? '' : 'text-muted' }}">
                        {{ $qualityControl->proposed_corrective_actions ?? 'Aucune action proposée.' }}
                    </p>
                </div>
            </div>

            @if($qualityControl->nonConformities->isNotEmpty())
            <hr class="my-4">
            <h4 class="card-title mb-3">Non-conformités liées</h4>
            <ul class="list-group">
                @foreach($qualityControl->nonConformities as $nonConformity)
                    <li class="list-group-item">
                        <a href="{{ route('non_conformities.show', $nonConformity) }}" class="text-decoration-none">
                            <strong>ID #{{ $nonConformity->id }} :</strong> {{ Str::limit($nonConformity->description, 100) }}
                        </a>
                        <span class="badge bg-secondary float-end">{{ $nonConformity->status }}</span>
                    </li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
</div>
@endsection