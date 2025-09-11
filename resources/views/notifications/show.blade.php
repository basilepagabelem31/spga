@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">Détails de la Notification #{{ $notification->id }}</h2>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour à la liste</a>
    </div>

    <div class="card shadow rounded-4 p-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <h5 class="card-title">
                        <span class="badge {{ $notification->isRead() ? 'bg-secondary' : 'bg-primary' }}">
                            @if ($notification->isRead())
                                <i class="fas fa-envelope-open me-1"></i> Lue
                            @else
                                <i class="fas fa-envelope me-1"></i> Non lue
                            @endif
                        </span>
                    </h5>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Type de notification:</strong>
                    <p>{{ $notification->type }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Message:</strong>
                    <p>{{ $notification->message }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Reçu le:</strong>
                    <p>{{ $notification->created_at->format('d/m/Y à H:i') }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Utilisateur destinataire:</strong>
                    <p>{{ $notification->user->name ?? 'Tous les utilisateurs' }}</p>
                </div>
                @if ($notification->isRead())
                    <div class="col-md-12 mb-3">
                        <strong>Date de lecture:</strong>
                        <p>{{ $notification->read_at->format('d/m/Y à H:i') }}</p>
                    </div>
                @endif
            </div>
            <div class="mt-4 d-flex gap-2">
                @if (!$notification->isRead())
                    <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success"><i class="fas fa-check-circle me-1"></i>Marquer comme lu</button>
                    </form>
                @endif
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteNotificationModal{{ $notification->id }}">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Inclusion de la modale de suppression --}}
@include('notifications.partials.delete_modal', ['notification' => $notification])

@endsection