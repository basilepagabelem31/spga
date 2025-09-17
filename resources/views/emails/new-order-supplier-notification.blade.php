    <!DOCTYPE html>
    <html>
    <head>
        <title>Nouvelle Commande de Votre Produit</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { width: 80%; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
            .header { background-color: #e6f7ff; padding: 10px; text-align: center; border-bottom: 1px solid #cceeff; }
            .details { background-color: #f0f8ff; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 5px solid #007bff; }
            .footer { text-align: center; margin-top: 20px; font-size: 0.8em; color: #777; }
            strong { color: #0056b3; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>🎉 Nouvelle Commande de Votre Produit !</h2>
            </div>
            <p>Bonjour,</p>
            <p>Nous avons le plaisir de vous informer qu'une nouvelle commande incluant l'un de vos produits a été passée sur notre plateforme.</p>
            
            <div class="details">
                <p><strong>Détails de la commande :</strong></p>
                <ul>
                    <li><strong>Code Commande :</strong> {{ $orderCode }}</li>
                    <li><strong>Client :</strong> {{ $clientName }}</li>
                    <li><strong>Produit Commandé :</strong> {{ $productName }}</li>
                    <li><strong>Quantité Commandée :</strong> {{ number_format($orderedQuantity, 2) }} {{ $saleUnit }}</li>
                    <li><strong>Stock Restant :</strong> {{ number_format($currentStock, 2) }} {{ $saleUnit }}</li>
                    <li><strong>Statut de la Commande :</strong> {{ $orderStatus }}</li>

                </ul>
            </div>

            <p>Veuillez préparer cette commande en conséquence. Le stock de ce produit a été mis à jour.</p>
            
            <p>Cordialement,</p>
            <p>L'équipe {{ config('app.name') }}</p>
            <div class="footer">
                Ceci est un e-mail automatique, veuillez ne pas y répondre.
            </div>
        </div>
    </body>
    </html>
    