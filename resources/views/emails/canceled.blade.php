<!DOCTYPE html>
<html>
<head>
    <title>Commande annulée</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background-color: #ffe6e6; padding: 10px; text-align: center; border-bottom: 1px solid #ffcccc; }
        .details { background-color: #fff0f0; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 5px solid #cc0000; }
        .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #777; }
        strong { color: #cc0000; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>❌ Commande annulée !</h2>
        </div>
        <p>Bonjour,</p>
        <p>Nous vous informons que la commande suivante a été annulée. Le stock a été remis à jour en conséquence.</p>
        
        <div class="details">
            <p><strong>Détails de la commande :</strong></p>
            <ul>
                <li><strong>Code Commande :</strong> {{ $order->order_code }}</li>
                <li><strong>Produit concerné :</strong> {{ $product->name }}</li>
                <li><strong>Quantité annulée :</strong> {{ number_format($orderedQuantity, 2) }} {{ $product->sale_unit }}</li>
            </ul>
        </div>

        <p>Cordialement,</p>
        <p>L'équipe {{ config('app.name') }}</p>
        <div class="footer">
            Ceci est un e-mail automatique, veuillez ne pas y répondre.
        </div>
    </div>
</body>
</html>