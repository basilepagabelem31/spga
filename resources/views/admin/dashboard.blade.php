@extends('layouts.app')

@section('title', 'Tableau de bord Administrateur')

@section('content')

<div class="container-fluid">
    <h1 class="mt-4">Tableau de bord Administrateur</h1>
    <p>Bienvenue sur le tableau de bord, {{ auth()->user()->first_name }} ! Vue d'ensemble et statistiques clés.</p>

    {{-- Section des indicateurs clés (KPIs) --}}
    <div class="row">
        {{-- Carte des Utilisateurs --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total Utilisateurs</div>
                            <div class="text-lg fw-bold">{{ $totalUsers }}</div>
                        </div>
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('users.index') }}">
                        Voir les utilisateurs
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Carte des Partenaires --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total Partenaires</div>
                            <div class="text-lg fw-bold">{{ $totalPartners }}</div>
                        </div>
                        <i class="fas fa-handshake fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('partners.index') }}">
                        Voir les partenaires
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        {{-- Carte des Commandes --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Commandes en attente</div>
                            <div class="text-lg fw-bold">{{ $pendingOrders }}</div>
                        </div>
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('orders.index') }}">
                        Voir les commandes
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Carte des Produits en rupture de stock --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Produits en rupture de stock</div>
                            <div class="text-lg fw-bold">{{ $outOfStockProducts }}</div>
                        </div>
                        <i class="fas fa-warehouse fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('stocks.index') }}">
                        Gérer les stocks
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        {{-- Nouvelle carte pour les associations Partenaire-Produit --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Associations Partenaire-Produit</div>
                            <div class="text-lg fw-bold">{{ $totalPartnerProducts }}</div>
                        </div>
                        <i class="fas fa-link fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('partner_products.index') }}">
                        Gérer les associations
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        {{-- Carte des Produits --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total Produits</div>
                            <div class="text-lg fw-bold">{{ $totalProducts }}</div>
                        </div>
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between small">
                    <a class="text-white stretched-link text-decoration-none" href="{{ route('products.index') }}">
                        Voir les produits
                    </a>
                    <div class="text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques ou tableaux pour plus de détails --}}
    <div class="row">
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-area me-1"></i>
                    Suivi des commandes (Exemple)
                </div>
                <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Activités récentes (Exemple)
                </div>
                <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
            </div>
        </div>
    </div>
    
    {{-- Sections supplémentaires pour la production, les partenaires et le système --}}
    <div class="row">
        {{-- Production & Qualité --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-cogs me-1"></i> Production & Qualité
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('production_follow_ups.index') }}" class="list-group-item list-group-item-action">
                            Suivi de production
                        </a>
                        <a href="{{ route('quality_controls.index') }}" class="list-group-item list-group-item-action">
                            Contrôles qualité
                        </a>
                        <a href="{{ route('non_conformities.index') }}" class="list-group-item list-group-item-action">
                            Non-conformités
                        </a>
                        <a href="{{ route('production_follow_ups.index') }}" class="list-group-item list-group-item-action">
                            Dates de récolte estimées
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Partenaires et Produits --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fas fa-box-open me-1"></i> Partenaires et Produits
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('partners.index') }}" class="list-group-item list-group-item-action">
                            Liste des partenaires
                        </a>
                        <a href="{{ route('contracts.index') }}" class="list-group-item list-group-item-action">
                            Contrats partenaires
                        </a>
                        <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action">
                            Catalogue de produits
                        </a>
                        <a href="{{ route('partner_products.index') }}" class="list-group-item list-group-item-action">
                            Association Produits-Partenaires
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section de l'activité système --}}
    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i> Activité Système
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('activity-logs.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Logs d'activité récents
                            <span class="badge bg-secondary rounded-pill">Voir tout</span>
                        </a>
                        <a href="{{ route('notifications.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Notifications
                            <span class="badge bg-danger rounded-pill">3 nouvelles</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>

{{-- Scripts pour les graphiques --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
<script>
    // Pour l'exemple, nous incluons des données de graphique de base
    // Tu peux remplacer les données statiques ci-dessous par des variables Blade
    // provenant du contrôleur si tu as les données réelles (ex: data: {{ json_encode($yearlyOrderData) }}).
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Commandes",
                lineTension: 0.3,
                backgroundColor: "rgba(0,123,255,0.2)",
                borderColor: "rgba(0,123,255,1)",
                pointRadius: 5,
                pointBackgroundColor: "rgba(0,123,255,1)",
                pointBorderColor: "rgba(255,255,255,0.8)",
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(0,123,255,1)",
                pointHitRadius: 50,
                pointBorderWidth: 2,
                data: [10, 30, 20, 50, 45, 60, 55, 70, 65, 80, 75, 90],
            }],
        },
        // Configuration des options...
    });
    
    var ctxB = document.getElementById("myBarChart");
    var myBarChart = new Chart(ctxB, {
        type: 'bar',
        data: {
            // Tu peux aussi mettre à jour ces labels et données avec des variables Blade
            labels: ["Utilisateurs", "Partenaires", "Produits", "Associations"],
            datasets: [{
                label: "Nombre",
                backgroundColor: ["#007bff", "#28a745", "#ffc107", "#dc3545"],
                borderColor: ["#007bff", "#28a745", "#ffc107", "#dc3545"],
                data: [{{ $totalUsers }}, {{ $totalPartners }}, {{ $totalProducts }}, {{ $totalPartnerProducts }}],
            }],
        },
        options: {
            // Configuration des options...
        }
    });
</script>

@endsection
