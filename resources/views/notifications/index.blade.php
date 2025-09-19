@extends('layouts.app')

@section('content')

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">🔔 Mes Notifications</h2>
    </div>

    @if (session('success'))
        <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Statut</th>
                            <th scope="col">Type</th>
                            <th scope="col">Message</th>
                            <th scope="col">Reçu le</th>
                            <th scope="col" class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                            <tr class="{{ $notification->isRead() ? 'table-light text-muted' : 'fw-bold' }}">
                                <td>
                                    @if ($notification->isRead())
                                        <i class="fas fa-envelope-open text-secondary me-2" title="Lue"></i> Lue
                                    @else
                                        <i class="fas fa-envelope text-primary me-2" title="Non lue"></i> Non lue
                                    @endif
                                </td>
                                <td>{{ $notification->type }}</td>
                                <td>{{ Str::limit($notification->message, 100) }}</td>
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('notifications.show', $notification) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if (!$notification->isRead())
                                            <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer comme lu">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteNotificationModal{{ $notification->id }}" title="Supprimer">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            {{-- Inclusion de la modale de suppression --}}
                            @include('notifications.partials.delete_modal', ['notification' => $notification])
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Vous n'avez aucune notification pour le moment.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

@endsection