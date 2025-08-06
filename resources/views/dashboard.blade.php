<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de vérification</title>

    {{-- Bibliothèque CSS de Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    {{-- Bibliothèque CSS de Font Awesome pour les icônes --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2I_XfU+A8/0k0+C7E6sK1K4u6E44f7w+5810y2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

    {{-- Bouton de déconnexion --}}
    <div class="position-absolute top-0 end-0 p-3">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
            </button>
        </form>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg border-success">
                    <div class="card-header bg-success text-white text-center">
                        <h5 class="mb-0">Vérification de l'e-mail réussie</h5>
                    </div>
                    <div class="card-body p-5 text-center">
                        
                        <i class="fas fa-check-circle text-success fa-5x mb-4"></i>

                        <h3 class="display-5 fw-bold mb-3">
                            Félicitations, {{ auth()->user()->first_name }} !
                        </h3>
                        
                        <p class="lead text-muted mb-4">
                            Votre adresse e-mail a été vérifiée avec succès. Vous êtes maintenant prêt à explorer toutes les fonctionnalités de notre plateforme.
                        </p>

                        <p class="text-muted mb-4">
                            Cliquez sur le bouton ci-dessous pour accéder à votre tableau de bord personnel.
                        </p>

                        <a href="{{ route('client.dashboard') }}" class="btn btn-success btn-lg mt-3">
                            <i class="fas fa-home me-2"></i> Accéder à mon tableau de bord client
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bibliothèque JS de Bootstrap et ses dépendances --}}
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
