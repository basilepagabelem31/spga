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
                            <div class="text-lg fw-bold">10</div> {{-- Remplacez par le compte dynamique --}}
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
                            <div class="text-lg fw-bold">3</div> {{-- Remplacez par le compte dynamique --}}
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
                            <div class="text-lg fw-bold">120</div> {{-- Remplacez par le compte dynamique --}}
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
                                {{-- Données fictives, à remplacer par vos données dynamiques --}}
                                <tr>
                                    <td>#ORD-0010</td>
                                    <td>2025-08-01</td>
                                    <td><span class="badge bg-warning text-dark">En cours de validation</span></td>
                                    <td>500,00 MAD</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                </tr>
                                <tr>
                                    <td>#ORD-0009</td>
                                    <td>2025-07-28</td>
                                    <td><span class="badge bg-success">Livrée</span></td>
                                    <td>250,00 MAD</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                </tr>
                                <tr>
                                    <td>#ORD-0008</td>
                                    <td>2025-07-25</td>
                                    <td><span class="badge bg-success">Livrée</span></td>
                                    <td>750,00 MAD</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-muted text-decoration-none" href="{{ route('client.orders') }}">
                        Voir tout l'historique des commandes
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection