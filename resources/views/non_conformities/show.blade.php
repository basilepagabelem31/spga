@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">Détails de la Non-Conformité #{{ $nonConformity->id }}</h2>
        <a href="{{ route('non_conformities.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour à la liste</a>
    </div>

    <div class="card shadow rounded-4 p-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Produit:</strong>
                    <p>{{ $nonConformity->product->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Contrôle Qualité source:</strong>
                    <p>
                        @if($nonConformity->qualityControl)
                            <a href="{{ route('quality_controls.show', $nonConformity->qualityControl->id) }}">
                                Contrôle #{{ $nonConformity->qualityControl->id }}
                            </a>
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Statut:</strong>
                    @php
                        $statusClass = '';
                        switch ($nonConformity->status) {
                            case 'en attente de décision': $statusClass = 'bg-warning text-dark'; break;
                            case 'rejeté': $statusClass = 'bg-danger'; break;
                            case 'reconditionné': $statusClass = 'bg-success'; break;
                            default: $statusClass = 'bg-secondary'; break;
                        }
                    @endphp
                    <p><span class="badge {{ $statusClass }}">{{ ucfirst($nonConformity->status) }}</span></p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Décision prise par:</strong>
                    <p>{{ $nonConformity->decisionTakenBy->name ?? 'En attente' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Date de décision:</strong>
                    <p>{{ $nonConformity->decision_date ? $nonConformity->decision_date->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Description:</strong>
                    <p>{{ $nonConformity->description ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editNonConformityModal{{ $nonConformity->id }}">
                    <i class="fas fa-edit me-1"></i> Modifier
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteNonConformityModal{{ $nonConformity->id }}">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Inclusion des modales pour l'édition et la suppression --}}
@include('non_conformities.partials.edit_modal', ['nonConformity' => $nonConformity, 'products' => $products, 'qualityControls' => $qualityControls, 'decisionMakers' => $decisionMakers])
@include('non_conformities.partials.delete_modal', ['nonConformity' => $nonConformity])

@endsection