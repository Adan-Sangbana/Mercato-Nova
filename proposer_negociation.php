<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_utilisateur'])) {
    $id_annonce = (int) $_POST['id_annonce'];
    $prix_propose = (float) $_POST['prix_propose'];
    $id_acheteur = $_SESSION['id_utilisateur'];
    
    $stmt = $pdo->prepare("INSERT INTO NEGOCIATION (id_annonce, id_acheteur, prix_propose, statut, echanges_restants) 
                           VALUES (:id, :acheteur, :prix, 'en_attente', 3)");
    $stmt->execute([':id' => $id_annonce, ':acheteur' => $id_acheteur, ':prix' => $prix_propose]);

    header("Location: details_annonce.php?id=$id_annonce&negociation=envoyee");
    exit;
}
?>