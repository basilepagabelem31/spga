<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription - SPGA-SARL</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkuyhXzP/K42iFj4bA3+0H3V9E5K6S5P5N5h6+y1KzJ/k1P0sA4g3bJb3L2F7fD4v5bS5V5G4fG7L5c8e2F+Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .register-container {
            min-height: 100vh;
        }

        .register-image-column {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("{{ asset('images/agriculture/login.jpg') }}") no-repeat center center;
            background-size: cover;
        }
        
        .register-form-card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
            max-width: 500px;
        }
        
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1.25rem;
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
            border-color: #28a745;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50rem;
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container-fluid register-container">
        <div class="row g-0 h-100">
            <div class="col-lg-6 d-none d-lg-flex register-image-column">
                <div class="d-flex flex-column align-items-start justify-content-center p-5 text-white">
                    <h1 class="display-4 fw-bold">Rejoignez-nous</h1>
                    <p class="lead mt-3">
                        Créez votre compte pour accéder à l'ensemble de nos services.
                    </p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-light">
                <div class="p-5 w-100">
                    <div class="card register-form-card mx-auto">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <a href="{{ url('/') }}">
                                    <h2 class="display-6 fw-bold text-success">SPGA-SARL</h2>
                                </a>
                                <p class="text-muted mt-2">Créer un nouveau compte</p>
                            </div>

                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="row mb-3 g-2">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label text-muted">Nom</label>
                                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-control @error('name') is-invalid @enderror">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label text-muted">Prénom</label>
                                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" class="form-control @error('first_name') is-invalid @enderror">
                                        @error('first_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label text-muted">Adresse E-mail</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label text-muted">Mot de passe</label>
                                    <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label text-muted">Confirmer le mot de passe</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        S'inscrire
                                    </button>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="text-muted">Déjà un compte ?
                                        <a href="{{ route('login') }}" class="text-success text-decoration-none fw-bold">Se connecter</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>