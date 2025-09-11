@extends('layouts.app')

@section('title', 'Tableau de bord Client')

@section('content')

<div class="container-fluid">
    <h1 class="mt-4">Tableau de bord Client</h1>
    <p class="mb-4">Bonjour, {{ auth()->user()->first_name }} ! Bienvenue sur votre espace personnel.</p>

    {{-- Section des indicateurs clés --}}
    <div class="row">
        {{-- Total des Commandes --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total de vos commandes</div>
                            <div class="text-lg fw-bold">{{ $totalOrders }}</div>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('client.orders') }}">
                        Voir toutes mes commandes
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Commandes en attente --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Commandes en attente</div>
                            <div class="text-lg fw-bold">{{ $pendingOrders }}</div>
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('client.orders') }}">
                        Suivre mes commandes
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Créer une nouvelle commande --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Nouveau !</div>
                            <div class="text-lg fw-bold">Passer une commande</div>
                        </div>
                        <i class="fas fa-plus-circle fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('client.products') }}">
                        Parcourir les produits
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Produits disponibles --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Produits disponibles</div>
                            <div class="text-lg fw-bold">{{ $availableProducts }}</div>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('client.products') }}">
                        Voir le catalogue
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section des commandes récentes --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Mes commandes récentes
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N° de Commande</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Total</th>
                                    <th>Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentOrders as $order)
                                    <tr>
                                        <td>#ORD-{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = '';
                                                switch($order->status) {
                                                    case 'En attente':
                                                    case 'En cours de validation':
                                                        $badgeClass = 'bg-warning text-dark';
                                                        break;
                                                    case 'Livrée':
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
                                        <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune commande récente à afficher.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-muted text-decoration-none" href="{{ route('client.orders') }}">
                        Voir tout l'historique des commandes
                    </a>
                    <div class="text-muted"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection