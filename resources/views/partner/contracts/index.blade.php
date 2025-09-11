@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h1 class="h2 fw-bold text-gray-800">
            <i class="fas fa-file-contract me-2 text-primary"></i> Mes Contrats
        </h1>
    </div>

    @if($contracts->isEmpty())
        <div class="alert alert-info border-start border-4 border-info py-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <h4 class="alert-heading fw-bold">Information</h4>
                    <p class="mb-0">Vous n'avez pas encore de contrats. Contactez votre administrateur pour plus de détails.</p>
                </div>
            </div>
        </div>
    @else
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Contrat #</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Titre</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Date de Début</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Date de Fin</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Description</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Statut</th>
                                <th scope="col" class="px-4 py-3 text-uppercase fw-bold text-muted text-nowrap">Fichier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contracts as $contract)
                                @php
                                    $now = now();
                                    $status = 'Inconnu';
                                    $badgeClass = 'bg-secondary';
                                    $icon = '❓';

                                    if ($contract->start_date <= $now && (!$contract->end_date || $contract->end_date >= $now)) {
                                        $status = 'Actif';
                                        $badgeClass = 'bg-success';
                                        $icon = '✅';
                                    } elseif ($contract->end_date && $contract->end_date < $now) {
                                        $status = 'Expiré';
                                        $badgeClass = 'bg-danger';
                                        $icon = '⛔';
                                    } elseif ($contract->start_date > $now) {
                                        $status = 'À venir';
                                        $badgeClass = 'bg-info';
                                        $icon = '📅';
                                    }
                                @endphp
                                <tr class="transition-transform-hover">
                                    <td class="px-4 py-3 fw-medium text-gray-900 text-nowrap">{{ $contract->id }}</td>
                                    <td class="text-nowrap">{{ $contract->title }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-nowrap">{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-gray-700 text-nowrap">
                                        @if ($contract->end_date)
                                            {{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted fst-italic">Permanent</span>
                                        @endif
                                    </td>
                                    <td class="{{ $contract->description ? '' : 'text-not-set' }}">
                                        {{ Str::limit($contract->description ?? 'Non renseigné', 50) }}
                                    </td>
                                    <td class="px-4 py-3 text-nowrap">
                                        <span class="badge {{ $badgeClass }} d-inline-flex align-items-center px-3 py-2 rounded-pill">
                                            <span class="me-1">{{ $icon }}</span> {{ $status }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        @if ($contract->file_path)
                                            <a href="{{ Storage::url($contract->file_path) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill" title="Voir le fichier">
                                                <i class="fas fa-file-alt me-1"></i> Voir
                                            </a>
                                        @else
                                            <span class="text-muted fst-italic">Non renseigné</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    /* Styles personnalisés pour un meilleur design */
    body {
        background-color: #f8f9fa; /* Arrière-plan plus doux */
    }
    .text-gray-800 {
        color: #212529; /* Couleur de texte sombre pour le contraste */
    }
    .card {
        border-radius: 1.25rem; /* Coins plus arrondis */
    }
    .table thead th {
        font-weight: 700; /* Plus de gras pour les en-têtes */
        color: #6c757d;
        border-bottom-width: 2px;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
    }
    .transition-transform-hover:hover {
        transform: scale(1.005);
        transition: transform 0.2s ease-in-out;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
        padding: 0.5em 0.8em;
    }
    .text-not-set {
        color: #adb5bd; /* Couleur pour les champs non renseignés */
        font-style: italic;
    }
</style>
@endsection