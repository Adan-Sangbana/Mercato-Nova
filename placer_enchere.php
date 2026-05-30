<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['id_utilisateur']) || !isset($_POST['id_annonce']) || !isset($_POST['montant'])) {
    header("Location: catalogue.php");
    exit;
}

$id_annonce = (int) $_POST['id_annonce'];
$montant_propose = (float) $_POST['montant'];

$stmt = $pdo->prepare("SELECT prix FROM ANNONCE WHERE id_annonce = :id_annonce");
$stmt->execute([':id_annonce' => $id_annonce]);
$annonce = $stmt->fetch();

if ($annonce) {
    $prix_actuel = (float) $annonce['prix'];

    if ($montant_propose > $prix_actuel) {
        $update = $pdo->prepare("UPDATE ANNONCE SET prix = :nouveau_prix WHERE id_annonce = :id_annonce");
        $update->execute([
            ':nouveau_prix' => $montant_propose,
            ':id_annonce' => $id_annonce
        ]);

        header("Location: details_annonce.php?id=$id_annonce&enchere=succes");
        exit;
    } else {
        header("Location: details_annonce.php?id=$id_annonce&erreur=enchere_basse");
        exit;
    }
}

header("Location: catalogue.php");
exit;
?>