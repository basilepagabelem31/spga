<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPGA-SARL - L'excellence au service de l'agriculture</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
 <!-- Google Fonts (fusionné) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<!-- Font Awesome (une seule version) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />


<style>
        body {
            font-family: 'Poppins', sans-serif;
            color: #4a4a4a;
        }
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
        }


#map {
    height: 300px;
    width: 100%;
    border-radius: 10px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url("{{ asset('images/agriculture/agr1.jpg') }}") no-repeat center center;
            background-size: cover;
            background-attachment: fixed;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .hero-title {
            font-size: 4.5rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.5);
            letter-spacing: 2px;
        }

        .card-elegant {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border-radius: 1rem;
            border: none;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 1rem 1rem 2rem rgba(0,0,0,.15); 
        }
        .card-elegant:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 1rem 3rem rgba(0,0,0,.15) !important;
        }
        .card-elegant .card-body {
            padding: 3rem;
        }

        .icon-box {
            font-size: 3rem;
            color: #28a745;
            transition: color 0.3s ease-in-out;
        }
        .card-elegant:hover .icon-box {
            color: #007bff;
        }

        .team-member-card {
            transition: transform 0.3s ease-in-out;
            border: none;
            border-radius: 1rem;
        }
        .team-member-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
        }
        .team-member-card img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-top: -75px;
            border: 5px solid #fff;
        }

        .cta-section {
            background-color: #212529;
        }

        .separator-wave {
            height: 150px;
            background: linear-gradient(to top, #fff 50%, transparent 50%);
            mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23fff" fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,90.7C672,85,768,101,864,133.3C960,165,1056,213,1152,213.3C1248,213,1344,160,1392,133.3L1440,107L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            -webkit-mask: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23fff" fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,90.7C672,85,768,101,864,133.3C960,165,1056,213,1152,213.3C1248,213,1344,160,1392,133.3L1440,107L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            mask-repeat: no-repeat;
            -webkit-mask-repeat: no-repeat;
            mask-size: cover;
            -webkit-mask-size: cover;
            position: relative;
            top: -150px;
        }

        .modal-body img {
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
        }
        
        .modal-content {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
        }
        
        .modal-header .btn-close {
            background-color: #fff;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

    </style>
</head>
<body>

<section class="hero-section text-white d-flex align-items-center">
    <div class="container text-center">
        {{-- Logo de l'entreprise --}}
  <div class="mb-4 animate__animated animate__fadeInDown text-start">
    <img src="{{ asset('images/logo/logo2.jpg') }}"
         class="img-fluid rounded-circle shadow"
         style="width: 100px; height: 100px; object-fit: cover; margin-top: -200px;"
         alt="Logo SPGA-SARL">
</div>


        <h1 class="display-3 fw-bold hero-title animate__animated animate__fadeInDown">
            SPGA-SARL
        </h1>
        <p class="lead mb-4 fw-light fs-4 animate__animated animate__fadeInUp">
            S'engager avec la SPGA-SARL, c'est garantir vos approvisionnements des champs à vos tables.
        </p>
        <div class="mt-5 animate__animated animate__fadeInUp animate__delay-1s">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-5 py-3 me-3 shadow-lg">
                Se Connecter
            </a>
            <a href="#about" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3 shadow-lg">
                Découvrir
            </a>
        </div>
    </div>
</section>


<div class="separator-wave bg-light"></div>

<section id="about" class="py-5 bg-success">
    <div class="container my-5">
        <div class="row align-items-center justify-content-center">
            <div class="col-lg-5 mb-4 mb-lg-0 order-lg-2">
                <img src="{{asset('images/agriculture/agr2.jpg')}}" class="img-fluid rounded shadow-lg about-image" alt="Notre engagement" loading="lazy">
            </div>
            <div class="col-lg-6 order-lg-1">
                <h2 class="display-5 fw-bold text-white mb-4">Notre histoire, notre mission.</h2>
                <p class="lead text-white">
                    Depuis le Burkina Faso, la SPGA-SARL est une force motrice dans la production locale et durable de produits agricoles. Notre mission est de contribuer activement à la sécurité alimentaire tout en valorisant nos filières locales.
                </p>
                <p class="lead text-white">
                    Nous croyons en une agriculture moderne et responsable. Notre plateforme web est la concrétisation de cette vision, un outil robuste pour centraliser la gestion et optimiser les processus clés.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="cibles" class="py-5">
    <div class="container my-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark">Nos cibles</h2>
            <p class="lead text-muted mt-3">
                Nous avons conçu notre plateforme pour chaque acteur clé de notre écosystème.
            </p>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm p-4" data-bs-toggle="modal" data-bs-target="#producteursModal">
                    <div class="card-body">
                        <i class="fas fa-seedling fa-4x text-success mb-3"></i>
                        <h5 class="fw-bold card-title">Producteurs</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm p-4" data-bs-toggle="modal" data-bs-target="#fournisseursModal">
                    <div class="card-body">
                        <i class="fas fa-truck-loading fa-4x text-success mb-3"></i>
                        <h5 class="fw-bold card-title">Fournisseurs</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm p-4" data-bs-toggle="modal" data-bs-target="#grossistesPartenairesModal">
                    <div class="card-body">
                        <i class="fas fa-warehouse fa-4x text-success mb-3"></i>
                        <h5 class="fw-bold card-title">Grossistes Partenaires</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm p-4" data-bs-toggle="modal" data-bs-target="#hotelsModal">
                    <div class="card-body">
                        <i class="fas fa-hotel fa-4x text-primary mb-3"></i>
                        <h5 class="fw-bold card-title">Hôtels</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm p-4" data-bs-toggle="modal" data-bs-target="#restaurantsModal">
                    <div class="card-body">
                        <i class="fas fa-utensils fa-4x text-primary mb-3"></i>
                        <h5 class="fw-bold card-title">Restaurants</h5>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm p-4" data-bs-toggle="modal" data-bs-target="#grossistesClientsModal">
                    <div class="card-body">
                        <i class="fas fa-box-open fa-4x text-primary mb-3"></i>
                        <h5 class="fw-bold card-title">Grossistes Clients</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="producteursModal" tabindex="-1" aria-labelledby="producteursModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('images/agriculture/pro.jpg')}}" class="img-fluid rounded-4 mb-4" alt="Image pour les Producteurs">
                <h3 class="fw-bold text-success mb-3">Nos Producteurs Partenaires</h3>
                <p class="lead text-muted">
                    La plateforme leur permet de mieux planifier leurs récoltes, de suivre leurs ventes et d'assurer une collaboration transparente avec la SPGA-SARL.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fournisseursModal" tabindex="-1" aria-labelledby="fournisseursModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('images/agriculture/fourn.jpg')}}" class="img-fluid rounded-4 mb-4" alt="Image pour les Fournisseurs">
                <h3 class="fw-bold text-success mb-3">Nos Fournisseurs</h3>
                <p class="lead text-muted">
                    Nous travaillons avec des fournisseurs locaux et régionaux pour garantir la qualité et la diversité de nos produits. La plateforme simplifie les échanges et la gestion des commandes.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="grossistesPartenairesModal" tabindex="-1" aria-labelledby="grossistesPartenairesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('images/agriculture/gros.jpg')}}" class="img-fluid rounded-4 mb-4" alt="Image pour les Grossistes">
                <h3 class="fw-bold text-success mb-3">Nos Grossistes Partenaires</h3>
                <p class="lead text-muted">
                    Des partenariats solides pour une distribution efficace. La plateforme leur fournit des outils pour une collaboration fluide et des transactions simplifiées.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="hotelsModal" tabindex="-1" aria-labelledby="hotelsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('images/agriculture/hot.jpg')}}" class="img-fluid rounded-4 mb-4" alt="Image pour les Hôtels">
                <h3 class="fw-bold text-primary mb-3">Nos Clients Hôtels</h3>
                <p class="lead text-muted">
                    La qualité de nos produits est essentielle pour la gastronomie. Notre plateforme leur offre un accès facile à notre catalogue et un suivi de leurs commandes pour une gestion simplifiée.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="restaurantsModal" tabindex="-1" aria-labelledby="restaurantsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('images/agriculture/rest.jpg')}}" class="img-fluid rounded-4 mb-4" alt="Image pour les Restaurants">
                <h3 class="fw-bold text-primary mb-3">Nos Clients Restaurants</h3>
                <p class="lead text-muted">
                    La fraîcheur et la qualité de nos produits sont au cœur de l'expérience culinaire. Ils peuvent passer leurs commandes en ligne et bénéficier d'une logistique optimisée.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="grossistesClientsModal" tabindex="-1" aria-labelledby="grossistesClientsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{asset('images/agriculture/clien.jpg')}}" class="img-fluid rounded-4 mb-4" alt="Image pour les Grossistes">
                <h3 class="fw-bold text-primary mb-3">Nos Clients Grossistes</h3>
                <p class="lead text-muted">
                    La chaîne d'approvisionnement est un élément clé. Nous leur fournissons les outils pour gérer leurs commandes en gros, suivre les livraisons et accéder à des rapports détaillés.
                </p>
            </div>
        </div>
    </div>
</div>

<section id="equipe" class="py-5 bg-success text-center">
    <div class="container my-5">
        <h2 class="display-5 fw-bold text-white mb-5">Rencontrez notre équipe de direction</h2>
        <div class="row g-5 justify-content-center">
            <div class="col-md-6 col-lg-3">
                <div class="card team-member-card text-center pb-3 pt-5 shadow-sm">
                    <img src="{{ asset('images/equipes/fem.jpg') }}" class="mx-auto" alt="Directeur Principal">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mt-2">Nom du Directeur Principal</h5>
                        <p class="text-muted mb-0">Directeur Principal (Administrateur)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card team-member-card text-center pb-3 pt-5 shadow-sm">
                    <img src="{{ asset('images/equipes/direct.jpg') }}" class="mx-auto" alt="Superviseur Commercial">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mt-2">Nom du Sous-Directeur Commercial</h5>
                        <p class="text-muted mb-0">Sous-Directeur Commercial</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card team-member-card text-center pb-3 pt-5 shadow-sm">
                    <img src="{{ asset('images/equipes/su.jpg') }}" class="mx-auto" alt="Superviseur Administratif">
                    <div class="card-body">
                        <h5 class="card-title fw-bold mt-2">Nom du Sous-Directeur Administratif</h5>
                        <p class="text-muted mb-0">Sous-Directeur Administratif & Financier</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
               <div class="card team-member-card text-center pb-3 pt-5 shadow-sm">
    <img src="{{ asset('images/equipes/super.jpg') }}" class="mx-auto" alt="Superviseur Technique">
    <div class="card-body">
        <h5 class="card-title fw-bold mt-2">Nom du Sous-Directeur Technique</h5>
        <p class="text-muted mb-0">Sous-Directeur Technique & Production</p>
    </div>
</div>
                </div>
            </div>
        </div>
       
    </div>
</section>

<section id="features" class="py-5">
    <div class="container my-5">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-primary">L'innovation au service de votre productivité</h2>
            <p class="lead text-muted mt-3">
                Une plateforme conçue pour chaque acteur de notre écosystème
            </p>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-sitemap icon-box mb-3"></i>
                        <h5 class="fw-bold card-title">Gestion centralisée</h5>
                        <p class="card-text text-muted">
                            Regroupez les produits, les stocks, les clients et les commandes en un seul lieu.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-rocket icon-box mb-3"></i>
                        <h5 class="fw-bold card-title">Efficacité opérationnelle</h5>
                        <p class="card-text text-muted">
                            Rationalisez la prise de commande et la planification des livraisons.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-search-plus icon-box mb-3"></i>
                        <h5 class="fw-bold card-title">Traçabilité renforcée</h5>
                        <p class="card-text text-muted">
                            Garantissez une meilleure visibilité sur les flux de produits et les transactions.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card card-elegant h-100 text-center shadow-sm">
                    <div class="card-body">
                        <i class="fas fa-users-cog icon-box mb-3"></i>
                        <h5 class="fw-bold card-title">Relation client & partenaire</h5>
                        <p class="card-text text-muted">
                            Offrez un accès personnalisé aux informations et aux services.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<br>

<h2>Notre  Localisation</h2>
    <div id="map"></div>



<script>
    // Initialisation de la carte centrée sur Meknès
    var map = L.map('map').setView([33.895, -5.554], 14); // Coordonnées de Meknès

    // Ajouter un fond de carte OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Ajouter un marqueur sur Meknès avec une popup personnalisée
    L.marker([33.895, -5.554]).addTo(map)
        .bindPopup('<b>Meknès 50000</b><br>Maroc.<br><a href="https://www.google.com/maps?q=33.895,-5.554" target="_blank">Voir sur Google Maps</a>')
        .openPopup();
</script>


  <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap">
    </script>

<br><br>


<section class="py-5 cta-section text-white text-center">
    <div class="container">
        <h2 class="display-5 fw-bold mb-4">Prêt à digitaliser votre gestion agricole ?</h2>
        <p class="lead mb-4 w-75 mx-auto">
            Accédez à la plateforme pour une gestion intuitive et une croissance mesurable.
        </p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg rounded-pill shadow-lg px-5 py-3">
            S'inscrire et commencer
        </a>
    </div>
</section>
<hr>

<footer class="bg-dark text-white-50 py-5">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-4 mb-4">
                <h5 class="text-white">SPGA-SARL</h5>
                <p>
                    La Société de Production et de Gestion des Aliments, basée au Burkina Faso, est spécialisée dans la production, la gestion et la distribution de produits agricoles.
                </p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="text-white">Contact</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-map-marker-alt me-2 text-success"></i> <a href="#" class="text-white-50 text-decoration-none">Ouagadougou, Burkina Faso</a></li>
                    <li><i class="fas fa-envelope me-2 text-success"></i> <a href="mailto:contact@spga-sarl.com" class="text-white-50 text-decoration-none">contact@spga-sarl.com</a></li>
                    <li><i class="fab fa-whatsapp me-2 text-success"></i> <a href="https://wa.me/22600000000" class="text-white-50 text-decoration-none">+226 00 00 00 00</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="text-white">Suivez-nous</h5>
                <div class="d-flex justify-content-center justify-content-md-start">
                    <a href="#" class="text-white-50 me-3" style="font-size: 1.5rem;"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white-50 me-3" style="font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="text-white-50" style="font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                </div>
                <h5 class="text-white mt-4">Liens Utiles</h5>
                <ul class="list-unstyled">
                    <li><a href="#" class="text-white-50 text-decoration-none">À Propos</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none">Fonctionnalités</a></li>
                    <li><a href="{{ route('login') }}" class="text-white-50 text-decoration-none">Se connecter</a></li>
                </ul>
            </div>
        </div>
        <hr class="my-4">
        <div class="text-center">
            <p class="mb-0">&copy; {{ date('Y') }} SPGA-SARL. Tous droits réservés.</p>
            <p class="mb-0 mt-1"><small>Conçu avec soin pour l'écosystème agricole du Burkina Faso.</small></p>
        </div>
    </div>
</footer>

</body>
</html>