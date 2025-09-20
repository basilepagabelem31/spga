@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <h2 class="mb-2">
            <i class="fas fa-bell me-2 text-warning"></i>
            Détails de la Notification #{{ $notification->id }}
        </h2>
        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>
    </div>

    {{-- Carte principale --}}
    <div class="card shadow-lg rounded-5 border-0">
        <div class="card-header bg-gradient p-3 text-white d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">
                    <span class="badge {{ $notification->isRead() ? 'bg-secondary' : 'bg-primary' }}">
                        @if ($notification->isRead())
                            <i class="fas fa-envelope-open me-1"></i> Lue
                        @else
                            <i class="fas fa-envelope me-1"></i> Non lue
                        @endif
                    </span>
                    <span class="badge bg-info ms-2">{{ class_basename($notification->type) }}</span>
                </h5>
            </div>
            <small>{{ $notification->created_at->format('d/m/Y H:i') }}</small>
        </div>

        <div class="card-body p-4">
            {{-- Message --}}
            <div class="mb-4">
                <h6 class="text-muted">Message :</h6>
                <p class="fs-5">{{ $notification->data['message'] ?? $notification->message }}</p>
            </div>

            {{-- Détails produit/stock --}}
            @if ($notification->type === 'App\Notifications\LowStockAlertNotification' && isset($notification->data['product_name']))
                <div class="mb-4">
                    <h6 class="text-muted">Détails Produit :</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-box-open fa-2x mb-2 text-primary"></i>
                                    <h6 class="card-title">{{ $notification->data['product_name'] }}</h6>
                                    <small class="text-muted">Nom du produit</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-layer-group fa-2x mb-2 text-success"></i>
                                    <h6 class="card-title">{{ $notification->data['current_stock'] }} {{ $notification->data['sale_unit'] }}</h6>
                                    <small class="text-muted">Stock actuel</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-0 shadow-sm">
                                <div class="card-body">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-2 text-danger"></i>
                                    <h6 class="card-title">{{ $notification->data['alert_threshold'] }}</h6>
                                    <small class="text-muted">Seuil d’alerte</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Utilisateur destinataire --}}
            <div class="mb-4">
                <h6 class="text-muted">Utilisateur destinataire :</h6>
                <p>{{ $notification->user->name ?? 'Tous les utilisateurs' }}</p>
            </div>

            {{-- Date lecture --}}
            @if ($notification->isRead())
                <div class="mb-4">
                    <h6 class="text-muted">Date de lecture :</h6>
                    <p>{{ $notification->read_at->format('d/m/Y H:i') }}</p>
                </div>
            @endif

            {{-- Liens contextuels --}}
            <div class="mb-4">
                @if(class_basename($notification->type) === 'LowStockAlertNotification' && isset($notification->data['product_id']))
                    <a href="{{ route('products.show', $notification->data['product_id']) }}" class="btn btn-info me-2 shadow-sm">
                        <i class="fas fa-box me-1"></i> Voir le produit
                    </a>
                @endif
                @if(class_basename($notification->type) === 'DeliveryRouteAssigned' && isset($notification->data['delivery_route_id']))
                    <a href="{{ route('chauffeur.planning') }}" class="btn btn-primary me-2 shadow-sm">
                        <i class="fas fa-truck me-1"></i> Voir la tournée
                    </a>
                @endif
            </div>

            {{-- Historique notifications similaires --}}
            <div class="mb-4">
                <h6 class="text-muted">Notifications similaires :</h6>
                <div class="accordion" id="similarNotifications">
                    @foreach(auth()->user()->notifications()->where('type', $notification->type)->latest()->take(5)->get() as $similar)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $similar->id }}">
                                <button class="accordion-button {{ $similar->id === $notification->id ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $similar->id }}" aria-expanded="{{ $similar->id === $notification->id ? 'true' : 'false' }}" aria-controls="collapse{{ $similar->id }}">
                                    {{ Str::limit($similar->data['message'] ?? $similar->message, 80) }}
                                    <small class="ms-2 text-muted">{{ $similar->created_at->format('d/m/Y H:i') }}</small>
                                </button>
                            </h2>
                            <div id="collapse{{ $similar->id }}" class="accordion-collapse collapse {{ $similar->id === $notification->id ? 'show' : '' }}" aria-labelledby="heading{{ $similar->id }}" data-bs-parent="#similarNotifications">
                                <div class="accordion-body">
                                    {{ $similar->data['message'] ?? $similar->message }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-4 d-flex flex-wrap gap-2">
                @if (!$notification->isRead())
                    <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success shadow-sm">
                            <i class="fas fa-check-circle me-1"></i> Marquer comme lu
                        </button>
                    </form>
                @endif

                <button type="button" class="btn btn-danger shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteNotificationModal{{ $notification->id }}">
                    <i class="fas fa-trash-alt me-1"></i> Supprimer
                </button>

                <button class="btn btn-secondary shadow-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
            </div>
        </div>
    </div>
</div>

@include('notifications.partials.delete_modal', ['notification' => $notification])
@endsection
