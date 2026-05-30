<?php
session_start();
require 'db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: catalogue.php");
    exit;
}

$id_annonce = (int) $_GET['id'];

$stmt = $pdo->prepare("SELECT ANNONCE.*, CATEGORIE.nom AS nom_categorie, UTILISATEUR.pseudo AS vendeur_pseudo 
                       FROM ANNONCE 
                       LEFT JOIN CATEGORIE ON ANNONCE.id_categorie = CATEGORIE.id_categorie 
                       JOIN UTILISATEUR ON ANNONCE.id_vendeur = UTILISATEUR.id_utilisateur
                       WHERE ANNONCE.id_annonce = :id_annonce AND ANNONCE.statut = 'active'");
$stmt->execute([':id_annonce' => $id_annonce]);
$annonce = $stmt->fetch();

if (!$annonce) {
    header("Location: catalogue.php");
    exit;
}

$enchere_terminee = false;
if ($annonce['mode_vente'] === 'enchere' && !empty($annonce['date_fin_enchere'])) {
    $date_limite = strtotime($annonce['date_fin_enchere']);
    $maintenant = time();
    
    if ($maintenant >= $date_limite) {
        $enchere_terminee = true;
        
        $pdo->prepare("UPDATE ANNONCE SET statut = 'vendue' WHERE id_annonce = :id")
            ->execute([':id' => $id_annonce]);
            
        $annonce['statut'] = 'vendue'; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($annonce['titre']) ?> - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .header a { color: #d4af37; text-decoration: none; font-family: sans-serif; font-size: 0.9em; letter-spacing: 1px; text-transform: uppercase; }
        .container { display: flex; gap: 50px; max-width: 1200px; margin: 0 auto; }
        .image-section { flex: 1; background: #111; border-radius: 8px; overflow: hidden; display: flex; align-items: center; justify-content: center; min-height: 400px; border: 1px solid #222; }
        .image-section img { width: 100%; height: auto; object-fit: cover; }
        .details-section { flex: 1; }
        .prix { font-size: 2em; color: #d4af37; font-family: sans-serif; margin: 20px 0; }
        .btn-action { background: #d4af37; color: #000; border: none; padding: 15px 30px; cursor: pointer; font-family: sans-serif; font-size: 1em; text-transform: uppercase; font-weight: bold; display: block; text-align: center; text-decoration: none; }
        .btn-disabled { background: #555; color: #aaa; cursor: not-allowed; }
        .success-msg { background: #2d5a27; color: #fff; padding: 10px; margin-bottom: 20px; text-align: center; border-radius: 4px; font-family: sans-serif; font-size: 0.9em; }
        .error-msg { background: #8b0000; color: #fff; padding: 10px; margin-bottom: 20px; text-align: center; border-radius: 4px; font-family: sans-serif; font-size: 0.9em; }
        .timer { font-family: sans-serif; font-size: 0.9em; color: #aaa; margin-bottom: 15px; border-left: 3px solid #d4af37; padding-left: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Mercato Nova</h2>
        <a href="catalogue.php">Retour au catalogue</a>
    </div>

    <div class="container">
        <div class="image-section">
            <?php if ($annonce['image_url']): ?>
                <img src="<?= htmlspecialchars($annonce['image_url']) ?>" alt="<?= htmlspecialchars($annonce['titre']) ?>">
            <?php else: ?>
                <span>Acquisition secrète</span>
            <?php endif; ?>
        </div>

        <div class="details-section">
            <h1><?= htmlspecialchars($annonce['titre']) ?></h1>

            <?php if (isset($_GET['negociation']) && $_GET['negociation'] === 'envoyee'): ?>
                <div class="success-msg">Négociation initiée avec succès !</div>
            <?php endif; ?>
            
            <?php if (isset($_GET['enchere']) && $_GET['enchere'] === 'succes'): ?>
                <div class="success-msg">Votre enchère a bien été enregistrée et le prix a été mis à jour !</div>
            <?php endif; ?>

            <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'enchere_basse'): ?>
                <div class="error-msg">Erreur : Votre offre doit être strictement supérieure au prix actuel.</div>
            <?php endif; ?>
            
            <div class="prix"><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</div>
            
            <?php if ($annonce['mode_vente'] === 'enchere'): ?>
                <?php if (!empty($annonce['date_fin_enchere'])): ?>
                    <div class="timer">
                        <?php if ($enchere_terminee): ?>
                            <strong style="color: #8b0000;">Enchère terminée</strong>
                        <?php else: ?>
                            Fin de l'enchère : <strong><?= date('d/m/Y à H:i', strtotime($annonce['date_fin_enchere'])) ?></strong>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($enchere_terminee || $annonce['statut'] === 'vendue'): ?>
                    <button class="btn-action btn-disabled" style="width:100%;" disabled>Adjugué !</button>
                <?php else: ?>
                    <form action="placer_enchere.php" method="POST">
                        <input type="hidden" name="id_annonce" value="<?= $annonce['id_annonce'] ?>">
                        <input type="number" name="montant" placeholder="Votre enchère (€)" step="0.01" required style="width:100%; padding:10px; margin-bottom:10px; background:#222; color:#fff; border:1px solid #333; box-sizing:border-box;">
                        <button type="submit" class="btn-action" style="width:100%;">Placer mon enchère</button>
                    </form>
                <?php endif; ?>

            <?php elseif ($annonce['mode_vente'] === 'achat_immediat'): ?>
                <a href="ajouter_panier.php?id=<?= $annonce['id_annonce'] ?>" class="btn-action">Ajouter au panier</a>
            <?php else: ?>
                <form action="proposer_negociation.php" method="POST">
                    <input type="hidden" name="id_annonce" value="<?= $annonce['id_annonce'] ?>">
                    <input type="number" name="prix_propose" placeholder="Votre prix proposé (€)" required style="width:100%; padding:10px; margin-bottom:10px; background:#222; color:#fff; border:1px solid #333; box-sizing:border-box;">
                    <button type="submit" class="btn-action" style="width:100%;">Proposer un prix</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>