<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5; /* Un gris très clair */
            font-family: 'Poppins', sans-serif;
        }
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    
        .btn-primary {
            background-color: #4a5c6e;
            border-color: #4a5c6e;
        }
        .btn-primary:hover {
            background-color: #3b4b5c;
            border-color: #3b4b5c;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <div class="row w-100 justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header text-white text-center bg-primary py-4 rounded-top-3">
                        <h4 class="mb-0 fw-bold">Réinitialiser le mot de passe</h4>
                    </div>
                    <div class="card-body p-4 p-md-5">

                        <p class="text-muted mb-4 text-center">
                            Vous avez oublié votre mot de passe ? Pas de problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation qui vous permettra d'en choisir un nouveau.
                        </p>
        
                        @if (session('status'))
                            <div class="alert alert-success text-center mb-4" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
        
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
        
                            <div class="mb-3">
                                <label for="email" class="form-label fw-bold">Adresse E-mail</label>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus>
        
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
        
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                    Envoyer le lien de réinitialisation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>