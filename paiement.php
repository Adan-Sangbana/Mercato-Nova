<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Sécurisé - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: sans-serif; text-align: center; padding: 50px; }
        .card { background: #151515; padding: 30px; display: inline-block; border-radius: 8px; border: 1px solid #d4af37; width: 350px; }
        input { display: block; width: 100%; margin: 10px 0; padding: 10px; background: #222; border: 1px solid #333; color: #fff; }
        button { background: #d4af37; color: #000; padding: 10px; width: 100%; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
    <div class="card">
        <h3>Paiement Sécurisé</h3>
        <input type="text" placeholder="Nom sur la carte">
        <input type="text" placeholder="Numéro de carte">
        <input type="text" placeholder="MM/AA">
        <input type="text" placeholder="CVC">
        <a href="traiter_paiement.php" style="text-decoration: none;">
            <button>Payer maintenant</button>
        </a>
    </div>
</body>
</html>
