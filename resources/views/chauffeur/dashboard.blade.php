@extends('layouts.app')

@section('title', 'Tableau de bord Chauffeur')

@section('content')

<div class="container-fluid">
    <h1 class="mt-4">Tableau de bord Chauffeur</h1>
    <p class="mb-4">Bonjour, {{ auth()->user()->first_name }} ! Voici les informations clés pour votre journée de travail.</p>

    {{-- Section des indicateurs clés --}}
    <div class="row">
        {{-- Livraisons du jour --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Livraisons prévues aujourd'hui</div>
                            <div class="text-lg fw-bold">3</div> {{-- Remplacez par le compte dynamique --}}
                        </div>
                        <i class="fas fa-route fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.deliveries') }}">
                        Voir les tournées
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Livraisons en cours --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Livraisons en cours</div>
                            <div class="text-lg fw-bold">1</div> {{-- Remplacez par le compte dynamique --}}
                        </div>
                        <i class="fas fa-truck-moving fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.deliveries') }}">
                        Détails de la livraison
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Livraisons terminées --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Livraisons terminées</div>
                            <div class="text-lg fw-bold">0</div> {{-- Remplacez par le compte dynamique --}}
                        </div>
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.deliveries') }}">
                        Historique des livraisons
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Heure de la prochaine livraison --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-dark text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Prochaine livraison à</div>
                            <div class="text-lg fw-bold">11:30</div> {{-- Remplacez par l'heure dynamique --}}
                        </div>
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('chauffeur.planning') }}">
                        Voir le planning
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section principale : Carte et tableau des livraisons --}}
    <div class="row">
        {{-- Carte de la tournée (placeholder) --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-map-marker-alt me-1"></i>
                    Carte de la tournée du jour
                </div>
                <div class="card-body">
                    {{-- Placeholder pour une carte, remplacez par une intégration d'API (ex: Google Maps) --}}
                    <div class="text-center p-5 bg-light rounded" style="min-height: 300px;">
                        <i class="fas fa-map fa-3x text-secondary mb-3"></i>
                        <p class="text-secondary">La carte de votre tournée apparaîtra ici.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableau des prochaines livraisons --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-table me-1"></i>
                    Détails des prochaines livraisons
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>N° Commande</th>
                                    <th>Client</th>
                                    <th>Adresse</th>
                                    <th>Statut</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Données fictives, à remplacer par vos données dynamiques --}}
                                <tr>
                                    <td>#ORD-001</td>
                                    <td>Alpha Logistic</td>
                                    <td>123 Rue de la Victoire</td>
                                    <td><span class="badge bg-warning text-dark">En route</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-success">Marquer comme livré</a></td>
                                </tr>
                                <tr>
                                    <td>#ORD-002</td>
                                    <td>Beta Services</td>
                                    <td>456 Avenue de la Liberté</td>
                                    <td><span class="badge bg-danger">En attente</span></td>
                                    <td><a href="#" class="btn btn-sm btn-outline-success">Marquer comme livré</a></td>
                                </tr>
                                <tr>
                                    <td>#ORD-003</td>
                                    <td>Gamma Inc.</td>
                                    <td>789 Boulevard de l'Espoir</td>
                                    <td><span class="badge bg-success">Livré</span></td>
                                    <td><button class="btn btn-sm btn-outline-secondary" disabled>Livré</button></td>
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