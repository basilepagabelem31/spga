@extends('layouts.app')

@section('title', 'Journal d\'Activité')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Journal d'Activité</h1>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmClearAllModal">
            <i class="fas fa-trash-alt me-1"></i> Vider tout le journal
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card shadow rounded-4 mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fas fa-filter me-2"></i>Filtres de recherche</h5>
            <form action="{{ route('activity-logs.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Par Utilisateur</label>
                    <select name="user_id" id="user_id" class="form-control">
                        <option value="">-- Tous les utilisateurs --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label">Par Action</label>
                    <select name="action" id="action" class="form-control">
                        <option value="">-- Toutes les actions --</option>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type }}" {{ request('action') == $type ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', Str::title($type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="table_name" class="form-label">Par Table</label>
                    <select name="table_name" id="table_name" class="form-control">
                        <option value="">-- Toutes les tables --</option>
                        @foreach($tableNames as $table)
                            @if ($table)
                                <option value="{{ $table }}" {{ request('table_name') == $table ? 'selected' : '' }}>
                                    {{ $table }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2 w-100"><i class="fas fa-search me-1"></i> Rechercher</button>
                </div>
            </form>
            <div class="mt-2">
                <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-redo me-1"></i> Réinitialiser les filtres</a>
            </div>
        </div>
    </div>

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Date & Heure</th>
                            <th class="px-4 py-3">Utilisateur</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Table / ID</th>
                            <th class="px-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activityLogs as $log)
                            <tr>
                                <td class="px-4 py-3">{{ $log->id }}</td>
                                <td class="px-4 py-3">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3">
                                    {{ $log->user->name ?? 'Utilisateur inconnu' }}
                                    @if($log->user && $log->user->role)
                                        <br><small class="text-muted">{{ $log->user->role->name }}</small>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge bg-primary">{{ str_replace('_', ' ', Str::title($log->action)) }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($log->table_name)
                                        <span class="badge bg-secondary">{{ $log->table_name }}</span>
                                        @if ($log->record_id)
                                            <br>ID: {{ $log->record_id }}
                                        @endif
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <a href="{{ route('activity-logs.show', $log->id) }}" class="btn btn-sm btn-info me-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal{{ $log->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">Aucun journal d'activité trouvé pour cette sélection.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="d-flex justify-content-center mt-4">
{{ $activityLogs->links('vendor.pagination.bootstrap-5') }}    </div>
    </div>
</div>

<div class="modal fade" id="confirmClearAllModal" tabindex="-1" aria-labelledby="confirmClearAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmClearAllModalLabel">Confirmer la suppression massive</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('activity-logs.clearAll') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                        <p class="fw-bold">Vous êtes sur le point de supprimer TOUS les journaux d'activité. Cette action est irréversible.</p>
                    </div>
                    <div class="form-group">
                        <label for="confirmation_text">Pour confirmer, tapez le mot "SUPPRIMER" ci-dessous :</label>
                        <input type="text" name="confirmation_text" id="confirmation_text" class="form-control mt-2" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-1"></i> Vider tout</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($activityLogs as $log)
<div class="modal fade" id="confirmDeleteModal{{ $log->id }}" tabindex="-1" aria-labelledby="confirmDeleteModalLabel{{ $log->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel{{ $log->id }}">Confirmer la suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce journal d'activité ?</p>
                <ul>
                    <li><strong>ID:</strong> {{ $log->id }}</li>
                    <li><strong>Action:</strong> {{ str_replace('_', ' ', Str::title($log->action)) }}</li>
                    <li><strong>Utilisateur:</strong> {{ $log->user->name ?? 'Utilisateur inconnu' }}</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('activity-logs.destroy', $log->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection