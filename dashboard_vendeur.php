<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: index.php");
    exit;
}

$id_vendeur = $_SESSION['id_utilisateur'];

$stmt_annonces = $pdo->prepare("SELECT * FROM ANNONCE WHERE id_vendeur = :id_vendeur ORDER BY date_publication DESC");
$stmt_annonces->execute([':id_vendeur' => $id_vendeur]);
$mes_annonces = $stmt_annonces->fetchAll();

$stmt_nego = $pdo->prepare("
    SELECT n.*, a.titre, u.pseudo AS acheteur_pseudo 
    FROM NEGOCIATION n
    JOIN ANNONCE a ON n.id_annonce = a.id_annonce
    JOIN UTILISATEUR u ON n.id_acheteur = u.id_utilisateur
    WHERE a.id_vendeur = :id_vendeur AND n.statut = 'en_attente'
");
$stmt_nego->execute([':id_vendeur' => $id_vendeur]);
$propositions = $stmt_nego->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Vendeur - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .header a { color: #d4af37; text-decoration: none; font-family: sans-serif; font-size: 0.9em; letter-spacing: 1px; text-transform: uppercase; margin-left: 20px; }
        h2, h3 { color: #d4af37; font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 50px; font-family: sans-serif; background: #111; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #222; }
        th { background-color: #1a1a1a; color: #d4af37; text-transform: uppercase; font-size: 0.85em; letter-spacing: 1px; }
        .btn-accept { background: #2d5a27; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; margin-right: 10px; font-size: 0.9em; }
        .btn-reject { background: #8b0000; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.9em; }
        .logout-btn { color: #8b0000 !important; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Mercato Nova</h2>
        <div>
            <a href="catalogue.php">Catalogue</a>
            <a href="ajouter_annonce.php">Nouvelle annonce</a>
            <a href="logout.php" class="logout-btn">Se Déconnecter</a>
        </div>
    </div>

    <h3>Propositions de prix à traiter</h3>
    <?php if (count($propositions) > 0): ?>
        <table>
            <tr>
                <th>Article</th>
                <th>Acheteur</th>
                <th>Prix Proposé</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($propositions as $prop): ?>
            <tr>
                <td><?= htmlspecialchars($prop['titre']) ?></td>
                <td><?= htmlspecialchars($prop['acheteur_pseudo']) ?></td>
                <td style="color: #d4af37; font-weight: bold;"><?= number_format((float)$prop['prix_propose'], 2, ',', ' ') ?> €</td>
                <td>
                    <a href="traiter_negociation.php?id=<?= $prop['id_negociation'] ?>&action=accepter" class="btn-accept">Accepter</a>
                    <a href="traiter_negociation.php?id=<?= $prop['id_negociation'] ?>&action=refuser" class="btn-reject">Refuser</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="color: #888; font-family: sans-serif; margin-bottom: 50px;">Aucune proposition en attente pour le moment.</p>
    <?php endif; ?>

    <h3>Mes Annonces Publiées</h3>
    <?php if (count($mes_annonces) > 0): ?>
        <table>
            <tr>
                <th>Titre</th>
                <th>Prix Actuel</th>
                <th>Mode de Vente</th>
                <th>Statut</th>
            </tr>
            <?php foreach ($mes_annonces as $annonce): ?>
            <tr>
                <td><?= htmlspecialchars($annonce['titre']) ?></td>
                <td><?= number_format($annonce['prix'], 2, ',', ' ') ?> €</td>
                <td style="text-transform: uppercase; font-size: 0.9em;"><?= str_replace('_', ' ', htmlspecialchars($annonce['mode_vente'])) ?></td>
                <td>
                    <?php if ($annonce['statut'] === 'active'): ?>
                        <span style="color: #2d5a27; font-weight: bold;">Active</span>
                    <?php else: ?>
                        <span style="color: #d4af37; font-weight: bold;">Vendue</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p style="color: #888; font-family: sans-serif;">Vous n'avez pas encore publié d'annonces.</p>
    <?php endif; ?>

</body>
</html>