<?php
session_start();
require 'db_connect.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = htmlspecialchars($_POST['pseudo']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO UTILISATEUR (pseudo, email, mot_de_passe_hash) VALUES (:pseudo, :email, :mot_de_passe_hash)");
        
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':email' => $email,
            ':mot_de_passe_hash' => $mot_de_passe_hash
        ]);

        header("Location: connexion.php?succes=inscription");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $erreur = "Cet email ou ce pseudo est déjà utilisé.";
        } else {
            $erreur = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .form-box { background: #111; padding: 40px; border-radius: 8px; border: 1px solid #222; width: 100%; max-width: 400px; }
        h2 { color: #d4af37; text-align: center; font-family: sans-serif; margin-bottom: 30px; text-transform: uppercase; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; background: #222; color: #fff; border: 1px solid #333; border-radius: 4px; box-sizing: border-box; }
        button { background: #d4af37; color: #000; border: none; padding: 15px; width: 100%; cursor: pointer; font-family: sans-serif; font-weight: bold; text-transform: uppercase; border-radius: 4px; }
        .link { display: block; text-align: center; margin-top: 20px; color: #888; text-decoration: none; font-family: sans-serif; font-size: 0.9em; transition: 0.3s; }
        .link:hover { color: #d4af37; }
        .erreur { background: #8b0000; color: #fff; text-align: center; padding: 10px; margin-bottom: 20px; border-radius: 4px; font-family: sans-serif; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Créer un compte</h2>

        <?php if ($erreur): ?>
            <div class="erreur"><?= $erreur ?></div>
        <?php endif; ?>

        <form action="inscription.php" method="POST">
            <input type="text" name="pseudo" placeholder="Votre Pseudo" required>
            <input type="email" name="email" placeholder="Votre Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">S'inscrire</button>
        </form>
        <a href="connexion.php" class="link">Déjà un compte ? Se connecter</a>
        <a href="index.php" class="link" style="margin-top: 10px; font-size: 0.8em;">Retour à l'accueil</a>
    </div>
</body>
</html>
