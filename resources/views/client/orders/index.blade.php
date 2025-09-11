@extends('layouts.app')

@section('title', 'Mes Commandes')

@section('content')

<div class="container-fluid">
    <h1 class="mt-4">Mes Commandes</h1>
    <p class="mb-4">Voici l'historique de toutes vos commandes passées.</p>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-table me-1"></i>
                Liste des commandes
            </span>
            <a href="{{ route('client.orders.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Nouvelle Commande
            </a>
        </div>
        <div class="card-body">
            {{-- Formulaire de recherche et de filtre --}}
            <form action="{{ route('client.orders') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Rechercher par N° Commande..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control">
                            <option value="">Tous les statuts</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                        <small class="form-text text-muted">Date de début</small>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                        <small class="form-text text-muted">Date de fin</small>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-filter me-1"></i> Filtrer
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>N° Commande</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                {{-- J'ai corrigé cette ligne pour afficher le bon code de commande --}}
                                <td>{{ $order->order_code }}</td>
                                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @php
                                        $badgeClass = '';
                                        switch($order->status) {
                                            case 'En attente de validation':
                                            case 'En préparation':
                                                $badgeClass = 'bg-warning text-dark';
                                                break;
                                            case 'Validée':
                                            case 'En livraison':
                                                $badgeClass = 'bg-info';
                                                break;
                                            case 'Terminée':
                                                $badgeClass = 'bg-success';
                                                break;
                                            case 'Annulée':
                                                $badgeClass = 'bg-danger';
                                                break;
                                            default:
                                                $badgeClass = 'bg-secondary';
                                                break;
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                                </td>
                                <td>{{ number_format($order->total_amount, 2, ',', ' ') }} FCFA</td>
                                <td>
                                    <a href="{{ route('client.orders.show', $order) }}" class="btn btn-sm btn-info text-white">Détails</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Aucune commande trouvée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
</div>

@endsection