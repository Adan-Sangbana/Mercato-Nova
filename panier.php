<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit;
}

$annonces_panier = [];
$total = 0;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    $ids = implode(',', $_SESSION['panier']);
    
    $stmt = $pdo->query("SELECT * FROM ANNONCE WHERE id_annonce IN ($ids)");
    $annonces_panier = $stmt->fetchAll();

    foreach ($annonces_panier as $article) {
        $total += $article['prix'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .header a { color: #d4af37; text-decoration: none; font-family: sans-serif; font-size: 0.9em; letter-spacing: 1px; text-transform: uppercase; }
        .container { max-width: 800px; margin: 0 auto; background: #111; padding: 30px; border-radius: 8px; border: 1px solid #222; }
        h1 { font-weight: normal; margin-top: 0; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .item { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #222; padding: 15px 0; }
        .item-info { display: flex; align-items: center; gap: 20px; }
        .item-img { width: 80px; height: 80px; object-fit: cover; border-radius: 4px; background: #222; }
        .item-titre { font-size: 1.2em; }
        .item-prix { color: #d4af37; font-family: sans-serif; font-weight: bold; }
        .total-section { text-align: right; margin-top: 30px; font-size: 1.5em; }
        .total-section span { color: #d4af37; font-weight: bold; font-family: sans-serif; }
        .btn-checkout { background: #d4af37; color: #000; border: none; padding: 15px 30px; cursor: pointer; font-family: sans-serif; font-size: 1em; text-transform: uppercase; font-weight: bold; display: block; width: 100%; margin-top: 30px; text-align: center; text-decoration: none; }
        .btn-checkout:hover { background: #fff; }
        .empty-msg { font-family: sans-serif; color: #888; text-align: center; padding: 40px 0; }
    </style>
</head>
<body>

    <div class="header">
        <h2 style="margin:0; font-weight:normal;">Mercato Nova</h2>
        <div>
            <a href="catalogue.php" style="margin-right: 20px;">Continuer les achats</a>
            <a href="dashboard.php">Dashboard</a>
        </div>
    </div>

    <div class="container">
        <h1>Mon Panier Privé</h1>

        <?php if (count($annonces_panier) > 0): ?>
            <?php foreach ($annonces_panier as $article): ?>
                <div class="item">
                    <div class="item-info">
                        <?php if ($article['image_url']): ?>
                            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Image" class="item-img">
                        <?php else: ?>
                            <div class="item-img" style="display:flex; align-items:center; justify-content:center; font-size:10px; color:#555;">SANS IMAGE</div>
                        <?php endif; ?>
                        <div class="item-titre"><?= htmlspecialchars($article['titre']) ?></div>
                    </div>
                    <div class="item-prix"><?= number_format($article['prix'], 2, ',', ' ') ?> €</div>
                </div>
            <?php endforeach; ?>

            <div class="total-section">
                Total : <span><?= number_format($total, 2, ',', ' ') ?> €</span>
            </div>

            <a href="paiement.php" class="btn-checkout">Procéder au paiement</a>

        <?php else: ?>
            <div class="empty-msg">Votre panier est actuellement vide.</div>
        <?php endif; ?>
    </div>

</body>
</html>