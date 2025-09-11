@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">📋 Gestion des Non-Conformités</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createNonConformityModal">
            <i class="fas fa-plus-circle me-1"></i> Déclarer une non-conformité
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Section de filtrage (optionnelle, si vous voulez l'ajouter) --}}
    {{-- <form action="{{ route('non_conformities.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <button type="submit" class="btn btn-info">Filtrer</button>
    </form> --}}


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produit</th>
                            <th>Contrôle Qualité ID</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Décision prise par</th>
                            <th>Date de décision</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($nonConformities as $nonConformity)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $nonConformity->product->name ?? 'Produit inconnu' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('quality_controls.show', $nonConformity->qualityControl->id) }}">
                                        QC#{{ $nonConformity->qualityControl->id ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($nonConformity->description, 50) ?? 'Non renseigné' }}</td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch ($nonConformity->status) {
                                            case 'en attente de décision': $statusClass = 'bg-warning text-dark'; break;
                                            case 'rejeté': $statusClass = 'bg-danger'; break;
                                            case 'reconditionné': $statusClass = 'bg-success'; break;
                                            default: $statusClass = 'bg-secondary'; break;
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($nonConformity->status) }}</span>
                                </td>
                                <td>{{ $nonConformity->decisionTakenBy->name ?? 'En attente' }}</td>
                                <td>{{ $nonConformity->decision_date ? $nonConformity->decision_date->format('d/m/Y') : 'N/A' }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('non_conformities.show', $nonConformity) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editNonConformityModal{{ $nonConformity->id }}" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteNonConformityModal{{ $nonConformity->id }}" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Inclusion des modales pour chaque élément du tableau --}}
                            @include('non_conformities.partials.edit_modal', ['nonConformity' => $nonConformity, 'products' => $products, 'qualityControls' => $qualityControls, 'decisionMakers' => $decisionMakers])
                            @include('non_conformities.partials.delete_modal', ['nonConformity' => $nonConformity])
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Aucune non-conformité trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $nonConformities->links() }}
    </div>
</div>

{{-- Inclusion de la modale de création --}}
@include('non_conformities.partials.create_modal', ['products' => $products, 'qualityControls' => $qualityControls, 'decisionMakers' => $decisionMakers])

@endsection