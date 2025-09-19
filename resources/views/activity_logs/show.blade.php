@extends('layouts.app')

@section('title', 'Détails du Journal d\'Activité')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <a href="{{ route('activity-logs.index') }}" class="btn btn-outline-secondary rounded-circle me-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            Détails du Journal #{{ $activityLog->id }}
        </h2>
    </div>
    
    <div class="card shadow rounded-4 mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold text-primary mb-3"><i class="fas fa-info-circle me-2"></i> Informations de l'événement</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong class="text-muted">Utilisateur :</strong> {{ $activityLog->user->full_name ?? $activityLog->user->name ?? 'Utilisateur inconnu' }}</p>
                    <p class="mb-1"><strong class="text-muted">Rôle :</strong> {{ $activityLog->user->role->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong class="text-muted">Action :</strong> <span class="badge bg-primary">{{ str_replace('_', ' ', ucfirst($activityLog->action)) }}</span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong class="text-muted">Date & Heure :</strong> {{ $activityLog->created_at->format('d/m/Y à H:i:s') }}</p>
                    <p class="mb-1"><strong class="text-muted">IP :</strong> {{ $activityLog->ip_address ?? 'N/A' }}</p>
                    <p class="mb-1"><strong class="text-muted">Agent Utilisateur :</strong> <small>{{ $activityLog->user_agent ?? 'N/A' }}</small></p>
                </div>
            </div>
            
            @if ($activityLog->description)
                <hr class="my-3">
                <p class="mb-1"><strong class="text-muted">Description de l'action :</strong></p>
                <p class="mb-0 p-3 bg-light rounded-3 border">{{ $activityLog->description }}</p>
            @endif
        </div>
    </div>

    <div class="card shadow rounded-4 mb-4">
        <div class="card-body">
            <h5 class="card-title fw-bold text-success mb-3"><i class="fas fa-database me-2"></i> Données de l'enregistrement</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong class="text-muted">Table concernée :</strong> <span class="badge bg-secondary">{{ $activityLog->table_name ?? 'N/A' }}</span></p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong class="text-muted">ID de l'enregistrement :</strong> {{ $activityLog->record_id ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    
    @if ($activityLog->old_values)
        <div class="card shadow rounded-4 mb-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-danger mb-3"><i class="fas fa-undo me-2"></i> Valeurs Précédentes</h5>
                <pre class="bg-light p-3 rounded-3 border">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif
    
    @if ($activityLog->new_values)
        <div class="card shadow rounded-4">
            <div class="card-body">
                <h5 class="card-title fw-bold text-success mb-3"><i class="fas fa-arrow-right me-2"></i> Valeurs Nouvelles</h5>
                <pre class="bg-light p-3 rounded-3 border">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    @endif
</div>
@endsection