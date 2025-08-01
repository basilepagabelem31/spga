@extends('layouts.app')

@section('title', 'Gestion des Rôles')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Rôles</h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addRoleModal">
            <i class="fas fa-plus me-2"></i> Ajouter un Rôle
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr class="bg-light text-secondary text-uppercase">
                            <th scope="col">Nom du Rôle</th>
                            <th scope="col">Description</th>
                            <th scope="col" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->description }}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-info btn-sm me-2 edit-role-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editRoleModal"
                                        data-id="{{ $role->id }}"
                                        data-name="{{ $role->name }}"
                                        data-description="{{ $role->description }}"
                                        title="Modifier">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm delete-role-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteRoleModal"
                                        data-id="{{ $role->id }}"
                                        title="Supprimer">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Aucun rôle trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $roles->links('pagination::bootstrap-5') }} {{-- Utilise les styles de pagination Bootstrap 5 --}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Ajouter un nouveau Rôle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Nom du rôle:</label>
                            <input type="text" name="name" id="add_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Description:</label>
                            <textarea name="description" id="add_description" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRoleModalLabel">Modifier le Rôle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editRoleForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nom du rôle:</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description:</label>
                            <textarea name="description" id="edit_description" rows="3" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer ce rôle ? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteRoleForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Script pour gérer les modales de modification et suppression avec JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editRoleModal = document.getElementById('editRoleModal');
        editRoleModal.addEventListener('show.bs.modal', function (event) {
            // Bouton qui a déclenché la modale
            var button = event.relatedTarget;

            // Extraire les informations des attributs data-*
            var id = button.getAttribute('data-id');
            var name = button.getAttribute('data-name');
            var description = button.getAttribute('data-description');

            // Mettre à jour les champs de la modale
            var form = editRoleModal.querySelector('#editRoleForm');
            var inputName = editRoleModal.querySelector('#edit_name');
            var textareaDescription = editRoleModal.querySelector('#edit_description');

            form.action = "{{ url('roles') }}/" + id; // Définir l'action du formulaire
            inputName.value = name;
            textareaDescription.value = description;
        });

        var deleteRoleModal = document.getElementById('deleteRoleModal');
        deleteRoleModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');

            var form = deleteRoleModal.querySelector('#deleteRoleForm');
            form.action = "{{ url('roles') }}/" + id; // Définir l'action du formulaire
        });
    });
</script>
@endsection