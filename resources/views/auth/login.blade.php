<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - SPGA-SARL</title>

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

        .login-container {
            min-height: 100vh;
        }

        .login-image-column {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("{{ asset('images/agriculture/login.jpg') }}") no-repeat center center;
            background-size: cover;
        }
        
        .login-form-card {
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

    <div class="container-fluid login-container">
        <div class="row g-0 h-100">
            <div class="col-lg-6 d-none d-lg-flex login-image-column">
                <div class="d-flex flex-column align-items-start justify-content-center p-5 text-white">
                    <h1 class="display-4 fw-bold">Bienvenue chez SPGA-SARL</h1>
                    <p class="lead mt-3">
                        Connectez-vous pour accéder à votre plateforme de gestion.
                    </p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-light">
                <div class="p-5 w-100">
                    <div class="card login-form-card mx-auto">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <a href="{{ url('/') }}">
                                    <h2 class="display-6 fw-bold text-success">SPGA-SARL</h2>
                                </a>
                                <p class="text-muted mt-2">Connectez-vous à votre compte</p>
                            </div>

                            @if (session('status'))
                                <div class="alert alert-success mb-4">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label text-muted">Adresse E-mail</label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="form-control @error('email') is-invalid @enderror">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label text-muted">Mot de passe</label>
                                    <input id="password" type="password" name="password" required autocomplete="current-password" class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                        <label class="form-check-label text-muted" for="remember_me">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a class="text-success text-decoration-none fw-bold" href="{{ route('password.request') }}">
                                            Mot de passe oublié ?
                                        </a>
                                    @endif
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        Se Connecter
                                    </button>
                                </div>

                                <div class="text-center mt-4">
                                    <p class="text-muted">Pas encore de compte ?
                                        <a href="{{ route('register') }}" class="text-success text-decoration-none fw-bold">S'inscrire</a>
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