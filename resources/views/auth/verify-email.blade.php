<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification de l'e-mail - SPGA-SARL</title>

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

        .verification-container {
            min-height: 100vh;
        }

        .verification-image-column {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("{{ asset('images/agriculture/login.jpg') }}") no-repeat center center;
            background-size: cover;
        }
        
        .verification-card {
            border: none;
            border-radius: 1.5rem;
            box-shadow: 0 1rem 3rem rgba(0,0,0,.175) !important;
            max-width: 500px;
        }
        
        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50rem;
        }

        .btn-link {
            font-size: 0.9rem;
            font-weight: 500;
            color: #4a4a4a;
            text-decoration: none;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body class="d-flex align-items-center justify-content-center">

    <div class="container-fluid verification-container">
        <div class="row g-0 h-100">
            <div class="col-lg-6 d-none d-lg-flex verification-image-column">
                <div class="d-flex flex-column align-items-start justify-content-center p-5 text-white">
                    <h1 class="display-4 fw-bold">Un dernier pas...</h1>
                    <p class="lead mt-3">
                        Vérifiez votre e-mail pour activer votre compte.
                    </p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center bg-light">
                <div class="p-5 w-100">
                    <div class="card verification-card mx-auto">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <a href="{{ url('/') }}">
                                    <h2 class="display-6 fw-bold text-success">SPGA-SARL</h2>
                                </a>
                            </div>
                            
                            <h4 class="fw-bold text-dark mb-4">Vérification de l'e-mail</h4>

                            <p class="mb-4 text-muted">
                                Merci de vous être inscrit(e) ! Avant de commencer, veuillez vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer.
                                Si vous n'avez pas reçu l'e-mail, nous vous en enverrons un nouveau avec plaisir.
                            </p>

                            @if (session('status') == 'verification-link-sent')
                                <div class="alert alert-success mb-4">
                                    Un nouveau lien de vérification a été envoyé à l'adresse e-mail fournie lors de l'inscription.
                                </div>
                            @endif

                            <div class="d-flex align-items-center justify-content-between mt-4">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <div>
                                        <button type="submit" class="btn btn-success btn-lg">
                                            Renvoyer l'e-mail
                                        </button>
                                    </div>
                                </form>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link text-muted">
                                        Se déconnecter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>