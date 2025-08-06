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
                            <div class="text-lg fw-bold">75</div> {{-- Remplacez par le compte dynamique --}}
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
                            <div class="text-white-75 small">Contrats actifs</div>
                            <div class="text-lg fw-bold">2</div> {{-- Remplacez par le compte dynamique --}}
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

        {{-- Commandes récentes --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Commandes récentes</div>
                            <div class="text-lg fw-bold">5</div> {{-- Remplacez par le compte dynamique --}}
                        </div>
                        <i class="fas fa-shopping-basket fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="#">
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
                    Commandes récentes pour mes produits
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
                                    <th>Détails</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Données fictives, à remplacer par vos données dynamiques --}}
                                <tr>
                                    <td>#ORD-0010</td>
                                    <td>Produit A</td>
                                    <td>100 kg</td>
                                    <td><span class="badge bg-warning text-dark">En cours de validation</span></td>
                                    <td>2025-08-01</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                </tr>
                                <tr>
                                    <td>#ORD-0009</td>
                                    <td>Produit B</td>
                                    <td>50 kg</td>
                                    <td><span class="badge bg-success">Livrée</span></td>
                                    <td>2025-07-28</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                </tr>
                                <tr>
                                    <td>#ORD-0008</td>
                                    <td>Produit A</td>
                                    <td>250 kg</td>
                                    <td><span class="badge bg-primary">En production</span></td>
                                    <td>2025-07-25</td>
                                    <td><a href="#" class="btn btn-sm btn-outline-info">Voir</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection