<?php
session_start();
require 'db_connect.php';

$sql = "SELECT ANNONCE.*, CATEGORIE.nom AS nom_categorie 
        FROM ANNONCE 
        LEFT JOIN CATEGORIE ON ANNONCE.id_categorie = CATEGORIE.id_categorie 
        WHERE ANNONCE.statut = 'active'";

$params = [];

if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $id_cat = (int) $_GET['categorie'];
    $sql .= " AND ANNONCE.id_categorie = :id_cat";
    $params[':id_cat'] = $id_cat;
}

$sql .= " ORDER BY ANNONCE.date_publication DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$annonces = $stmt->fetchAll();

$cat_active = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .header a { color: #d4af37; text-decoration: none; font-family: sans-serif; font-size: 0.9em; letter-spacing: 1px; text-transform: uppercase; margin-left: 20px; }
        h1 { font-size: 2.5em; margin-bottom: 20px; }
        
        /* Style du menu de filtres */
        .filters { display: flex; gap: 20px; margin-bottom: 40px; border-bottom: 1px solid #222; padding-bottom: 15px; }
        .filters a { color: #888; text-decoration: none; font-family: sans-serif; text-transform: uppercase; letter-spacing: 1px; font-size: 0.9em; padding-bottom: 5px; transition: 0.3s; }
        .filters a:hover { color: #fff; }
        .filters a.active { color: #d4af37; border-bottom: 2px solid #d4af37; font-weight: bold; }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
        .card { background: #111; border: 1px solid #222; padding: 20px; display: flex; flex-direction: column; }
        .card img { width: 100%; height: 280px; object-fit: cover; margin-bottom: 15px; background: #000; }
        .card-placeholder { width: 100%; height: 280px; display: flex; align-items: center; justify-content: center; background: #050505; margin-bottom: 15px; color: #333; font-family: sans-serif; font-size: 0.8em; letter-spacing: 1px; }
        .card .categorie { font-family: sans-serif; font-size: 0.8em; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .card h3 { margin: 0 0 10px 0; font-size: 1.8em; }
        .card .desc { color: #aaa; font-size: 0.9em; flex-grow: 1; margin-bottom: 20px; }
        .card .price-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #222; padding-bottom: 15px; }
        .card .prix { color: #d4af37; font-size: 1.3em; font-family: sans-serif; }
        .card .mode { font-family: sans-serif; font-size: 0.7em; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .btn-view { border: 1px solid #d4af37; color: #d4af37; padding: 12px; text-align: center; text-decoration: none; text-transform: uppercase; font-family: sans-serif; font-size: 0.9em; transition: 0.3s; display: block; }
        .btn-view:hover { background: #d4af37; color: #000; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Mercato Nova</h2>
        <div>
            <?php if (isset($_SESSION['id_utilisateur'])): ?>
                <a href="dashboard_vendeur.php">Espace Vendeur</a>
                <a href="logout.php" style="color: #8b0000;">Se Déconnecter</a>
            <?php else: ?>
                <a href="connexion.php">Se Connecter</a>
            <?php endif; ?>
            <a href="panier.php">Mon Panier</a>
        </div>
    </div>

    <h1>Trending Acquisitions</h1>

    <div class="filters">
        <a href="catalogue.php" class="<?= $cat_active === 0 ? 'active' : '' ?>">Toute la collection</a>
        <a href="catalogue.php?categorie=1" class="<?= $cat_active === 1 ? 'active' : '' ?>">Timepieces</a>
        <a href="catalogue.php?categorie=2" class="<?= $cat_active === 2 ? 'active' : '' ?>">Parfums</a>
    </div>

    <?php if (count($annonces) > 0): ?>
        <div class="grid">
            <?php foreach ($annonces as $annonce): ?>
                <div class="card">
                    <?php if ($annonce['image_url']): ?>
                        <img src="<?= htmlspecialchars($annonce['image_url']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
                    <?php else: ?>
                        <div class="card-placeholder">ACQUISITION SECRÈTE</div>
                    <?php endif; ?>
                    
                    <div class="categorie">
                        <?= $annonce['nom_categorie'] ? htmlspecialchars($annonce['nom_categorie']) : 'Non catégorisé' ?>
                    </div>
                    
                    <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                    <div class="desc"><?= htmlspecialchars(substr($annonce['description'], 0, 60)) ?>...</div>
                    
                    <div class="price-row">
                        <div class="prix"><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</div>
                        <div class="mode"><?= str_replace('_', ' ', htmlspecialchars($annonce['mode_vente'])) ?></div>
                    </div>
                    
                    <a href="details_annonce.php?id=<?= $annonce['id_annonce'] ?>" class="btn-view">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="color: #888; font-family: sans-serif; font-size: 1.1em; padding: 40px 0;">Aucune acquisition disponible dans cette catégorie pour le moment.</p>
    <?php endif; ?>
</body>
</html>