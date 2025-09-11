@extends('layouts.app') {{-- Assurez-vous d'avoir un layout principal --}}

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

    .btn-outline-success {
        color: #28a745;
        border-color: #28a745;
    }
    .btn-outline-success:hover {
        background-color: #28a745;
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

    /* Badges de statut */
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
        <h2 class="mb-2">👥 Gestion des Utilisateurs</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fas fa-user-plus me-1"></i> Ajouter un utilisateur
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info shadow-sm">{{ session('info') }}</div>
    @endif

    {{-- Section de filtrage --}}
    <form action="{{ route('users.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche rapide</label>
                <input type="text" class="form-control" id="search" name="search" placeholder="Nom, email, téléphone..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="filter_role" class="form-label">Filtrer par rôle</label>
                <select class="form-select" id="filter_role" name="role_id">
                    <option value="">Tous les rôles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_status" class="form-label">Filtrer par statut</label>
                <select class="form-select" id="filter_status" name="is_active">
                    <option value="">Tous les statuts</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Actif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="col-md-2 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th> {{-- Nouvelle colonne --}}
                            <th>Adresse</th> {{-- Nouvelle colonne --}}
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->first_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td class="{{ $user->phone_number ? '' : 'text-not-set' }}">{{ $user->phone_number ?? 'Non renseigné' }}</td> {{-- Affichage du numéro de téléphone --}}
                                <td class="{{ $user->address ? '' : 'text-not-set' }}">{{ $user->address ?? 'Non renseigné' }}</td> {{-- Affichage de l'adresse --}}
                                <td>
                                    <span class="badge bg-secondary">{{ $user->role->name ?? 'Non assigné' }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Modifier -->
                                        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" title="Modifier l'utilisateur">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Supprimer -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}" title="Supprimer l'utilisateur">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                        <!-- Activer/Désactiver -->
                                        @if ($user->is_active)
                                            <form action="{{ route('users.deactivate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Désactiver le compte">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('users.activate', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activer le compte">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            {{-- Modales pour chaque utilisateur --}}
                            @include('users.partials.edit_modal', ['user' => $user, 'roles' => $roles])
                            @include('users.partials.delete_modal', ['user' => $user])

                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $users->links() }}
    </div>
</div>

{{-- Modale d'ajout d'utilisateur --}}
@include('users.partials.create_modal', ['roles' => $roles])

{{-- Script pour initialiser les tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });




    document.addEventListener('DOMContentLoaded', function () {
        // Initialisation des tooltips Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Gestion de l'affichage du modal d'édition en cas d'erreur de validation
        @if ($errors->any() && old('_token') && session('open_edit_modal_id'))
            var userId = "{{ session('open_edit_modal_id') }}";
            var editModal = new bootstrap.Modal(document.getElementById('editUserModal' + userId));
            editModal.show();
        @endif
    });


</script>
@endsection
