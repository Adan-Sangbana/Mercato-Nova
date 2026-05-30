<?php
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_annonce = (int) $_GET['id'];

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (!in_array($id_annonce, $_SESSION['panier'])) {
        $_SESSION['panier'][] = $id_annonce;
    }
}

header("Location: panier.php");
exit;
?>