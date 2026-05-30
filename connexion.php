<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM UTILISATEUR WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mot_de_passe, $user['mot_de_passe_hash'])) {
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['pseudo'] = $user['pseudo'];
        
        header("Location: dashboard_vendeur.php");
        exit;
    } else {
        $erreur = "Identifiants incorrects.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .form-box { background: #111; padding: 40px; border-radius: 8px; border: 1px solid #222; width: 100%; max-width: 400px; }
        h2 { color: #d4af37; text-align: center; font-family: sans-serif; margin-bottom: 30px; text-transform: uppercase; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; background: #222; color: #fff; border: 1px solid #333; border-radius: 4px; box-sizing: border-box; }
        button { background: #d4af37; color: #000; border: none; padding: 15px; width: 100%; cursor: pointer; font-family: sans-serif; font-weight: bold; text-transform: uppercase; border-radius: 4px; }
        .link { display: block; text-align: center; margin-top: 20px; color: #888; text-decoration: none; font-family: sans-serif; font-size: 0.9em; transition: 0.3s; }
        .link:hover { color: #d4af37; }
        .msg { text-align: center; padding: 10px; margin-bottom: 20px; border-radius: 4px; font-family: sans-serif; font-size: 0.9em; }
        .erreur { background: #8b0000; color: #fff; }
        .succes { background: #2d5a27; color: #fff; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Se Connecter</h2>
        
        <?php if (isset($_GET['succes']) && $_GET['succes'] === 'inscription'): ?>
            <div class="msg succes">Inscription réussie ! Veuillez vous connecter.</div>
        <?php endif; ?>

        <?php if (isset($erreur)): ?>
            <div class="msg erreur"><?= $erreur ?></div>
        <?php endif; ?>

        <form action="connexion.php" method="POST">
            <input type="email" name="email" placeholder="Votre Email" required>
            <input type="password" name="mot_de_passe" placeholder="Mot de passe" required>
            <button type="submit">Accéder</button>
        </form>
        <a href="inscription.php" class="link">Pas encore de compte ? S'inscrire</a>
        <a href="index.php" class="link" style="margin-top: 10px; font-size: 0.8em;">Retour à l'accueil</a>
    </div>
</body>
</html>