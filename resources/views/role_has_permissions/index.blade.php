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
        <h2 class="mb-2">🔗 Attributions Rôles-Permissions</h2>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createRolePermissionModal">
            <i class="fas fa-link me-1"></i> Attribuer une permission
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
    <form action="{{ route('role_has_permissions.index') }}" method="GET" class="mb-4 p-4 bg-white rounded-4 shadow-sm">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label for="filter_role" class="form-label">Filtrer par rôle</label>
                <select class="form-select" id="filter_role" name="role_id">
                    <option value="">Tous les rôles</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ request('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label for="filter_permission" class="form-label">Filtrer par permission</label>
                <select class="form-select" id="filter_permission" name="permission_id">
                    <option value="">Toutes les permissions</option>
                    @foreach ($permissions as $permission)
                        <option value="{{ $permission->id }}" {{ request('permission_id') == $permission->id ? 'selected' : '' }}>{{ $permission->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-grid gap-2">
                <button type="submit" class="btn btn-info"><i class="fas fa-filter me-1"></i> Filtrer</button>
                <a href="{{ route('role_has_permissions.index') }}" class="btn btn-outline-secondary"><i class="fas fa-sync-alt me-1"></i> Réinitialiser</a>
            </div>
        </div>
    </form>


    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Rôle</th>
                            <th>Permission</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roleHasPermissions as $assignment)
                            <tr>
                                <td><span class="badge bg-primary">{{ $assignment->role->name ?? 'Rôle inconnu' }}</span></td>
                                <td><span class="badge bg-success">{{ $assignment->permission->name ?? 'Permission inconnue' }}</span></td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Supprimer l'attribution -->
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteRolePermissionModal{{ $assignment->role_id }}-{{ $assignment->permission_id }}" title="Supprimer l'attribution">
                                            <i class="fas fa-unlink"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Modale de suppression pour chaque attribution --}}
                            @include('role_has_permissions.partials.delete_modal', ['assignment' => $assignment])

                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Aucune attribution de permission trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $roleHasPermissions->links() }}
    </div>
</div>

{{-- Modale d'ajout d'attribution --}}
@include('role_has_permissions.partials.create_modal', ['roles' => $roles, 'permissions' => $permissions])

{{-- Script pour initialiser les tooltips Bootstrap --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
