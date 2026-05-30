<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: catalogue.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $id_categorie = (int) $_POST['id_categorie'];
    $description = $_POST['description'];
    $prix = (float) $_POST['prix'];
    $mode_vente = $_POST['mode_vente'];
    $id_vendeur = $_SESSION['id_utilisateur'];

    $date_fin_enchere = null;
    if ($mode_vente === 'enchere' && !empty($_POST['date_fin_enchere'])) {
        $date_fin_enchere = $_POST['date_fin_enchere'];
    }

    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $filename = time() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = $target_path;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO ANNONCE (id_vendeur, id_categorie, titre, description, prix, mode_vente, date_fin_enchere, image_url, statut) 
                           VALUES (:vendeur, :categorie, :titre, :description, :prix, :mode, :date_fin, :image, 'active')");
    $stmt->execute([
        ':vendeur' => $id_vendeur,
        ':categorie' => $id_categorie,
        ':titre' => $titre,
        ':description' => $description,
        ':prix' => $prix,
        ':mode' => $mode_vente,
        ':date_fin' => $date_fin_enchere,
        ':image' => $image_url
    ]);

    header("Location: dashboard_vendeur.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une annonce - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; margin: 0; padding: 40px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 50px; border-bottom: 1px solid #333; padding-bottom: 20px; }
        .header a { color: #d4af37; text-decoration: none; font-family: sans-serif; font-size: 0.9em; letter-spacing: 1px; text-transform: uppercase; margin-left: 20px; }
        .form-container { max-width: 600px; margin: 0 auto; background: #111; padding: 40px; border-radius: 8px; border: 1px solid #222; }
        label { display: block; margin-bottom: 8px; color: #d4af37; font-family: sans-serif; font-size: 0.9em; text-transform: uppercase; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 20px; background: #222; color: #fff; border: 1px solid #333; border-radius: 4px; box-sizing: border-box; }
        .btn-submit { background: #d4af37; color: #000; border: none; padding: 15px; width: 100%; cursor: pointer; font-family: sans-serif; font-weight: bold; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Mercato Nova</h2>
        <div>
            <a href="catalogue.php">Catalogue</a>
            <a href="dashboard_vendeur.php">Espace Vendeur</a>
        </div>
    </div>

    <div class="form-container">
        <h3 style="text-align: center; margin-bottom: 30px; font-family: sans-serif; color: #d4af37;">Nouvelle Acquisition</h3>
        
        <form action="ajouter_annonce.php" method="POST" enctype="multipart/form-data">
            
            <label>Titre de l'article :</label>
            <input type="text" name="titre" required>

            <label>Catégorie :</label>
            <select name="id_categorie" required>
                <option value="1">Timepieces (Montres)</option>
                <option value="2">Parfums</option>
            </select>

            <label>Description :</label>
            <textarea name="description" rows="4" required></textarea>

            <label>Prix (en €) :</label>
            <input type="number" name="prix" step="0.01" required>

            <label>Mode de vente :</label>
            <select name="mode_vente" id="mode_vente" required onchange="afficherDateEnchere()">
                <option value="achat_immediat">Achat Immédiat</option>
                <option value="enchere">Enchère</option>
                <option value="negociation">Négociation</option>
            </select>

            <div id="bloc_date_enchere" style="display: none; background: #1a1a1a; padding: 15px; border-left: 3px solid #d4af37; margin-bottom: 20px;">
                <label>Date et heure de fin d'enchère :</label>
                <input type="datetime-local" name="date_fin_enchere" id="date_fin_enchere" style="margin-bottom: 0;">
            </div>

            <label>Image de l'article :</label>
            <input type="file" name="image" accept="image/*" required>

            <button type="submit" class="btn-submit">Mettre en ligne</button>
        </form>
    </div>

    <script>
        function afficherDateEnchere() {
            var modeVente = document.getElementById("mode_vente").value;
            var blocDate = document.getElementById("bloc_date_enchere");
            var inputDate = document.getElementById("date_fin_enchere");

            if (modeVente === "enchere") {
                blocDate.style.display = "block";
                inputDate.setAttribute("required", "required");
            } else {
                blocDate.style.display = "none";
                inputDate.removeAttribute("required");
                inputDate.value = "";
            }
        }
    </script>
</body>
</html>