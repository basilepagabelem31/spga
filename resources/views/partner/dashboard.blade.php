@extends('layouts.app')

@section('title', 'Tableau de bord Partenaire')

@section('content')

<div class="container-fluid">
    <h1 class="mt-4">Tableau de bord Partenaire</h1>
    <p class="mb-4">Bonjour, {{ auth()->user()->first_name }} ! Voici un aperçu de vos activités.</p>

    {{-- Section des indicateurs clés --}}
    <div class="row">
        {{-- Total de mes produits --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total de mes produits</div>
                            {{-- Affichage de la valeur dynamique --}}
                            <div class="text-lg fw-bold">{{ $myTotalProducts }}</div>
                        </div>
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('partenaire.products') }}">
                        Gérer mes produits
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Contrats actifs --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Mes Contrats </div>
                            {{-- Affichage de la valeur dynamique --}}
                            <div class="text-lg fw-bold">{{ $activeContractsCount }}</div>
                        </div>
                        <i class="fas fa-file-contract fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('partenaire.contracts') }}">
                        Voir mes contrats
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Commandes pour mes produits --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total des commandes</div>
                            {{-- Affichage de la valeur dynamique --}}
                            <div class="text-lg fw-bold">{{ $recentOrdersCount }}</div>
                        </div>
                        <i class="fas fa-shopping-basket fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('partenaire.orders') }}">
                        Voir les commandes
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section des commandes récentes pour mes produits --}}
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Dernières commandes pour mes produits
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N° de Commande</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Statut</th>
                                    <th>Date de commande</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Boucle sur les commandes récentes --}}
                                @forelse ($recentOrders as $order)
                                    {{-- Pour chaque commande, bouclez sur les articles de commande pertinents --}}
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $order->order_code }}</td>
                                            <td>{{ $item->product->name }}</td>
                                            <td>{{ $item->quantity }} {{ $item->sale_unit_at_order }}</td>
                                            <td>
                                                @php
                                                    $badgeClass = '';
                                                    switch($order->status) {
                                                        case 'En attente de validation':
                                                            $badgeClass = 'bg-warning text-dark';
                                                            break;
                                                        case 'Validée':
                                                            $badgeClass = 'bg-info';
                                                            break;
                                                        case 'En préparation':
                                                            $badgeClass = 'bg-primary';
                                                            break;
                                                        case 'En livraison':
                                                            $badgeClass = 'bg-secondary';
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
                                                <span class="badge {{ $badgeClass }}">{{ $order->status }}</span>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Aucune commande récente trouvée.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
