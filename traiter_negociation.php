<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['id_utilisateur']) || !isset($_GET['id']) || !isset($_GET['action'])) {
    header("Location: dashboard_vendeur.php");
    exit;
}

$id_negociation = (int) $_GET['id'];
$action = $_GET['action'];
$id_vendeur = $_SESSION['id_utilisateur']; 

// On récupère les infos pour vérifier que c'est bien le vendeur de l'annonce qui agit
$stmt = $pdo->prepare("
    SELECT n.id_annonce, n.prix_propose, a.id_vendeur 
    FROM NEGOCIATION n 
    JOIN ANNONCE a ON n.id_annonce = a.id_annonce 
    WHERE n.id_negociation = :id_nego
");
$stmt->execute([':id_nego' => $id_negociation]);
$nego = $stmt->fetch();

// Sécurité : On vérifie que la négociation existe et que l'utilisateur est bien le vendeur
if ($nego && $nego['id_vendeur'] == $id_vendeur) {
    if ($action === 'accepter') {
        // 1. On passe la négociation en 'acceptee'
        $pdo->prepare("UPDATE NEGOCIATION SET statut = 'acceptee' WHERE id_negociation = :id")
            ->execute([':id' => $id_negociation]);
        
        // 2. On passe l'annonce en 'vendue' et on met à jour le prix avec l'offre acceptée
        $pdo->prepare("UPDATE ANNONCE SET statut = 'vendue', prix = :prix WHERE id_annonce = :id_annonce")
            ->execute([':prix' => $nego['prix_propose'], ':id_annonce' => $nego['id_annonce']]);
            
        // 3. Optionnel mais pro : On refuse automatiquement les autres offres en attente pour cette même annonce
        $pdo->prepare("UPDATE NEGOCIATION SET statut = 'refusee' WHERE id_annonce = :id_annonce AND statut = 'en_attente'")
            ->execute([':id_annonce' => $nego['id_annonce']]);

    } elseif ($action === 'refuser') {
        // On passe juste la négociation en 'refusee'
        $pdo->prepare("UPDATE NEGOCIATION SET statut = 'refusee' WHERE id_negociation = :id")
            ->execute([':id' => $id_negociation]);
    }
}

// Retour direct au dashboard une fois l'action effectuée
header("Location: dashboard_vendeur.php");
exit;
?>