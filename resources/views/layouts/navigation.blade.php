<div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-white">SPGA-SARL</div>
    <div class="list-group list-group-flush">
        
        {{-- Logique pour les administrateurs et superviseurs --}}
        @if (auth()->user()->hasAnyRole(['admin_principal', 'superviseur_commercial', 'superviseur_production']))
            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('admin.dashboard')) active @endif">
                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
            </a>

            <a href="{{ route('users.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('users.*')) active @endif">
                <i class="fas fa-users me-2"></i> Gestion des Utilisateurs
            </a>

            <div class="list-group-item list-group-item-action bg-dark text-white">
                <a data-bs-toggle="collapse" href="#collapseRoles" role="button" aria-expanded="true" aria-controls="collapseRoles" class="text-white text-decoration-none d-block">
                    <i class="fas fa-user-tag me-2"></i> Rôles & Permissions
                </a>
                <div class="collapse show" id="collapseRoles">
                    <a href="{{ route('roles.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('roles.*')) active @endif">- Rôles</a>
                    <a href="{{ route('permissions.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('permissions.*')) active @endif">- Permissions</a>
                    <a href="{{ route('role_has_permissions.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('role_has_permissions.*')) active @endif">- Attribuer</a>
                </div>
            </div>

            <div class="list-group-item list-group-item-action bg-dark text-white">
                <a data-bs-toggle="collapse" href="#collapsePartners" role="button" aria-expanded="true" aria-controls="collapsePartners" class="text-white text-decoration-none d-block">
                    <i class="fas fa-handshake me-2"></i> Partenaires
                </a>
                <div class="collapse show" id="collapsePartners">
                    <a href="{{ route('partners.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('partners.*')) active @endif">- Partenaires</a>
                    <a href="{{ route('contracts.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('contracts.*')) active @endif">- Contrats</a>
                </div>
            </div>

            <div class="list-group-item list-group-item-action bg-dark text-white">
                <a data-bs-toggle="collapse" href="#collapseProducts" role="button" aria-expanded="true" aria-controls="collapseProducts" class="text-white text-decoration-none d-block">
                    <i class="fas fa-box-open me-2"></i> Produits
                </a>
                <div class="collapse show" id="collapseProducts">
                    <a href="{{ route('categories.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('categories.*')) active @endif">- Catégories</a>
                    <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('products.*')) active @endif">- Produits</a>
                    <a href="{{ route('partner_products.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('partner_products.*')) active @endif">- Association Prod.</a>
                </div>
            </div>

            <div class="list-group-item list-group-item-action bg-dark text-white">
                <a data-bs-toggle="collapse" href="#collapseOrders" role="button" aria-expanded="true" aria-controls="collapseOrders" class="text-white text-decoration-none d-block">
                    <i class="fas fa-shopping-cart me-2"></i> Commandes & Stocks
                </a>
                <div class="collapse show" id="collapseOrders">
                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('orders.*')) active @endif">- Commandes</a>
                    <a href="{{ route('stocks.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('stocks.*')) active @endif">- Stocks</a>
                </div>
            </div>

            <div class="list-group-item list-group-item-action bg-dark text-white">
                <a data-bs-toggle="collapse" href="#collapseProduction" role="button" aria-expanded="true" aria-controls="collapseProduction" class="text-white text-decoration-none d-block">
                    <i class="fas fa-cogs me-2"></i> Production & Qualité
                </a>
                <div class="collapse show" id="collapseProduction">
                    <a href="{{ route('production-follow-ups.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('production-follow-ups.*')) active @endif">- Suivi de production</a>
                    <a href="{{ route('quality-controls.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('quality-controls.*')) active @endif">- Contrôles qualité</a>
                    <a href="{{ route('non-conformities.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('non-conformities.*')) active @endif">- Non-conformités</a>
                </div>
            </div>
            
            <div class="list-group-item list-group-item-action bg-dark text-white">
                <a data-bs-toggle="collapse" href="#collapseSystem" role="button" aria-expanded="true" aria-controls="collapseSystem" class="text-white text-decoration-none d-block">
                    <i class="fas fa-cogs me-2"></i> Système
                </a>
                <div class="collapse show" id="collapseSystem">
                    <a href="{{ route('notifications.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('notifications.*')) active @endif">- Notifications</a>
                    <a href="{{ route('activity-logs.index') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('activity-logs.*')) active @endif">- Logs d'activité</a>
                </div>
            </div>
        
        {{-- Logique pour les partenaires --}}
        @elseif (auth()->user()->isPartner())
            <a href="{{ route('partenaire.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('partenaire.dashboard')) active @endif">
                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord Partenaire
            </a>
            <a href="{{ route('partenaire.products') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('partenaire.products')) active @endif">
                <i class="fas fa-box me-2"></i> Mes Produits
            </a>
            <a href="{{ route('partenaire.contracts') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('partenaire.contracts')) active @endif">
                <i class="fas fa-file-contract me-2"></i> Mes Contrats
            </a>

        {{-- Logique pour les clients --}}
        @elseif (auth()->user()->isClient())
            <a href="{{ route('client.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('client.dashboard')) active @endif">
                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord Client
            </a>
            <a href="{{ route('client.orders') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('client.orders')) active @endif">
                <i class="fas fa-shopping-cart me-2"></i> Mes Commandes
            </a>
            <a href="{{ route('client.products') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('client.products')) active @endif">
                <i class="fas fa-box me-2"></i> Produits
            </a>

        {{-- Logique pour les chauffeurs --}}
        @elseif (auth()->user()->isDriver())
            <a href="{{ route('chauffeur.dashboard') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('chauffeur.dashboard')) active @endif">
                <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord Chauffeur
            </a>
            <a href="{{ route('chauffeur.deliveries') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('chauffeur.deliveries')) active @endif">
                <i class="fas fa-truck-moving me-2"></i> Mes Livraisons
            </a>
            <a href="{{ route('chauffeur.planning') }}" class="list-group-item list-group-item-action bg-dark text-white @if(request()->routeIs('chauffeur.planning')) active @endif">
                <i class="fas fa-calendar-alt me-2"></i> Mon Planning
            </a>
        @endif
        
    </div>
</div>