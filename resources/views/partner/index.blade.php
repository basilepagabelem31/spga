<!-- @extends('layouts.app') {{-- Assurez-vous d'avoir un layout principal --}}

@section('content')

<style>
    /* Styles généraux pour le corps de la page */
    body {
        background-color: #f0f2f5; /* Couleur de fond douce */
    }

    /* Styles pour les boutons */
    .btn {
        border-radius: 0.75rem; /* Coins arrondis pour les boutons */
        transition: all 0.3s ease; /* Transition douce pour les effets au survol */
        font-weight: 500;
    }

    .btn-primary {
        background-image: linear-gradient(to right, #6a11cb 0%, #2575fc 100%); /* Dégradé de couleur */
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Ombre douce */
    }

    .btn-primary:hover {
        transform: translateY(-3px); /* Effet de léger soulèvement */
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    }

    .btn-outline-warning {
        color: #ffc107;
        border-color: #ffc107;
    }
    .btn-outline-warning:hover {
        background-color: #ffc107;
        color: #fff;
    }

    .btn-outline-danger {
        color: #dc3545;
        border-color: #dc3545;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .btn-outline-secondary {
        color: #6c757d;
        border-color: #6c757d;
    }
    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: #fff;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: #138496;
    }


    /* Styles pour les cartes (conteneur du tableau) */
    .card {
        border: none;
        border-radius: 1.25rem; /* Coins arrondis plus prononcés */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Ombre plus douce et plus présente */
        overflow: hidden; /* S'assure que les coins arrondis sont respectés par le contenu */
    }

    /* Styles pour le tableau */
    .table {
        margin-bottom: 0; /* Supprime la marge en bas du tableau */
    }

    .table-hover tbody tr:hover {
        background-color: #e9ecef; /* Couleur de survol plus distincte */
        transform: translateY(-2px) scale(1.005); /* Léger soulèvement et agrandissement au survol */
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Ombre légère au survol */
        transition: all 0.3s ease-in-out;
    }

    .table thead th {
        border-bottom: 2px solid #dee2e6; /* Bordure plus prononcée pour l'en-tête */
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa; /* Fond légèrement grisé pour l'en-tête */
    }

    .table tbody td {
        padding: 1rem 1.5rem;
        vertical-align: middle; /* Centre verticalement le contenu des cellules */
    }

    /* Badges */
    .badge {
        font-size: 0.85em;
        padding: 0.5em 0.8em;
        border-radius: 0.5rem;
        font-weight: 600;
    }

    /* Conteneur des actions pour un meilleur alignement */
    .btn-group {
        display: flex;
        gap: 0.5rem; /* Espacement entre les boutons */
    }

    /* Pagination */
    .pagination .page-item .page-link {
        border-radius: 0.5rem;
        margin: 0 0.2rem;
        color: #007bff;
        border: 1px solid #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: #fff;
    }

    /* Titre de la page */
    h2 {
        font-weight: 700;
        color: #343a40;
    }

    /* Alertes */
    .alert {
        border-radius: 0.75rem;
        font-weight: 500;
    }

    /* Tooltip customisation */
    .tooltip-inner {
        background-color: #343a40;
        color: #fff;
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
    }
    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #343a40;
    }
    .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
        border-bottom-color: #343a40;
    }

    /* Style pour les champs "Non renseigné" */
    .text-not-set {
        font-style: italic;
        color: #888; /* Couleur grise pour indiquer que ce n'est pas renseigné */
    }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">🤝 Gestion des Partenaires</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createPartnerModal">
            <i class="fas fa-handshake me-1"></i> Ajouter un partenaire
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info shadow-sm">{{ session('info') }}</div>
    @endif
    
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    {{-- Section de filtrage --}}
    <form action="{{ route('partners.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche rapide</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Nom établissement, contact, email, téléphone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="filter_type" class="form-label">Filtrer par type</label>
                <select class="form-select" id="filter_type" name="type">
                    <option value="">Tous les types</option>
                    @foreach ($partnerTypes as $type)
                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_locality" class="form-label">Localité/Région</label>
                <input type="text" class="form-control" id="filter_locality" name="locality_region" placeholder="Entrez une localité..." value="{{ request('locality_region') }}">
            </div>
            <div class="col-md-2 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('partners.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Établissement</th>
                            <th>Contact</th>
                            <th>Fonction</th>
                            <th>Téléphone</th>
                            <th>Email</th>
                            <th>Localité</th>
                            <th>Type</th>
                            <th>Expérience (années)</th>
                            <th>Utilisateur associé</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($partners as $partner)
                            <tr>
                                <td>{{ $partner->establishment_name }}</td>
                                <td class="{{ $partner->contact_name ? '' : 'text-not-set' }}">{{ $partner->contact_name ?? 'Non renseigné' }}</td>
                                <td class="{{ $partner->function ? '' : 'text-not-set' }}">{{ $partner->function ?? 'Non renseigné' }}</td>
                                <td class="{{ $partner->phone ? '' : 'text-not-set' }}">{{ $partner->phone ?? 'Non renseigné' }}</td>
                                <td class="{{ $partner->email ? '' : 'text-not-set' }}">{{ $partner->email ?? 'Non renseigné' }}</td>
                                <td class="{{ $partner->locality_region ? '' : 'text-not-set' }}">{{ $partner->locality_region ?? 'Non renseigné' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $partner->type }}</span></td>
                                <td class="{{ $partner->years_of_experience !== null ? '' : 'text-not-set' }}">
                                    {{ $partner->years_of_experience ?? 'Non renseigné' }}
                                </td>
                                <td>
                                    @if ($partner->user)
                                        <span class="badge bg-primary">{{ $partner->user->full_name ?? $partner->user->email }}</span>
                                    @else
                                        <span class="text-not-set">Aucun</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Modifier -->
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editPartnerModal{{ $partner->id }}" title="Modifier le partenaire">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Supprimer -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deletePartnerModal{{ $partner->id }}" title="Supprimer le partenaire">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales pour chaque partenaire --}}
                            @include('partners.partials.edit_modal', ['partner' => $partner, 'users' => $users])
                            @include('partners.partials.delete_modal', ['partner' => $partner])

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">Aucun partenaire trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $partners->links() }}
    </div>
</div>

{{-- Modale d'ajout de partenaire --}}
@include('partners.partials.create_modal', ['users' => $users])

{{-- Script pour initialiser les tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection -->
