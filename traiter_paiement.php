<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header("Location: panier.php");
    exit;
}

try {
    $pdo->beginTransaction();

    foreach ($_SESSION['panier'] as $id_annonce) {
        $stmt_prix = $pdo->prepare("SELECT prix FROM ANNONCE WHERE id_annonce = :id");
        $stmt_prix->execute([':id' => $id_annonce]);
        $annonce = $stmt_prix->fetch();

        $stmt = $pdo->prepare("INSERT INTO TRANSACTION (id_acheteur, id_annonce, montant_final, origine, statut) VALUES (:acheteur, :annonce, :montant, 'achat_immediat', 'validee')");
        $stmt->execute([
            ':acheteur' => $_SESSION['id_utilisateur'],
            ':annonce' => $id_annonce,
            ':montant' => $annonce['prix']
        ]);
        
        $stmt_upd = $pdo->prepare("UPDATE ANNONCE SET statut = 'vendu' WHERE id_annonce = :id");
        $stmt_upd->execute([':id' => $id_annonce]);
    }

    $pdo->commit();
    $_SESSION['panier'] = [];
    
    header("Location: dashboard.php?succes=paiement");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors du paiement : " . $e->getMessage());
}
?>