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
                            @php
                                $data = $notification->data ?? [];
                            @endphp
                            <tr class="{{ $notification->read_at ? 'table-light text-muted' : 'fw-bold' }}">
                                <!-- Statut -->
                                <td>
                                    @if ($notification->read_at)
                                        <i class="fas fa-envelope-open text-secondary me-2" title="Lue"></i> Lue
                                    @else
                                        <i class="fas fa-envelope text-primary me-2" title="Non lue"></i> Non lue
                                    @endif
                                </td>

                                <!-- Type -->
                                <td>{{ class_basename($notification->type) }}</td>

                                <!-- Message principal -->
                                <td>{{ Str::limit($data['message'] ?? '—', 100) }}</td>

                                <!-- Détails spécifiques -->
                                <!-- <td>
                                    @if ($notification->type === 'App\Notifications\LowStockAlertNotification')
                                        Produit: {{ $data['product_name'] ?? '—' }}<br>
                                        Stock actuel: {{ $data['current_stock'] ?? '—' }} {{ $data['sale_unit'] ?? '' }}<br>
                                        Seuil d’alerte: {{ $data['alert_threshold'] ?? '—' }}
                                    @elseif ($notification->type === 'App\Notifications\DeliveryRouteAssigned')
                                        Tournée ID: {{ $data['delivery_route_id'] ?? '—' }}<br>
                                        Date livraison: {{ isset($data['delivery_date']) ? \Carbon\Carbon::parse($data['delivery_date'])->format('d/m/Y') : '—' }}<br>
                                        Statut: {{ $data['status'] ?? '—' }}
                                    @elseif ($notification->type === 'App\Notifications\ResetPasswordNotification')
                                        Instruction: {{ $data['message'] ?? 'Réinitialisation de mot de passe disponible' }}
                                    @elseif ($notification->type === 'App\Notifications\DeliveryCompletedNotification')
                                        Message: {{ $data['message'] ?? 'Tournée complétée' }}
                                    @else
                                        — {{-- Pour d'autres types non gérés encore --}}
                                    @endif
                                </td> -->

                                <!-- Date de réception -->
                                <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>

                                <!-- Actions -->
                                <td class="text-end pe-4">
                                    <div class="btn-group" role="group">
                                        <!-- Voir détails -->
                                        <a href="{{ route('notifications.show', $notification) }}" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <!-- Marquer comme lu si non lu -->
                                        @if (!$notification->read_at)
                                            <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Marquer comme lu">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Supprimer -->
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
                                <td colspan="6" class="text-center text-muted py-4">
                                    Vous n'avez aucune notification pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $notifications->links('vendor.pagination.bootstrap-5') }}
    </div>
</div>

@endsection





