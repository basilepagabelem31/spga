<!DOCTYPE html>
<html>
<head>
    <title>Alerte de Stock Faible</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #f8f8f8; padding: 10px; text-align: center; border-bottom: 1px solid #eee; }
        .alert { color: #D8000C; background-color: #FFD2D2; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Alerte de Stock Faible 🔔</h2>
        </div>
        <p>Bonjour,</p>
        <p>Nous souhaitons vous informer que le stock du produit suivant a atteint ou dépassé son seuil d'alerte :</p>
        <div class="alert">
            <strong>Produit :</strong> {{ $productName }}<br>
            <strong>Stock Actuel :</strong> {{ number_format($currentStock, 2) }} {{ $saleUnit }}<br>
            <strong>Seuil d'Alerte :</strong> {{ number_format($alertThreshold, 2) }} {{ $saleUnit }}
        </div>
        @if ($currentStock <= 0)
            <p style="color: red; font-weight: bold;">⚠️ Ce produit est actuellement en rupture de stock !</p>
        @endif
        <p>Veuillez prendre les mesures nécessaires pour réapprovisionner ce produit.</p>
        <p>Cordialement,</p>
        <p>L'équipe {{ config('app.name') }}</p>
        <div class="footer">
            Ceci est un e-mail automatique, veuillez ne pas y répondre.
        </div>
    </div>
</body>
</html>