<?php
session_start();

if (isset($_SESSION['id_utilisateur'])) {
    header("Location: dashboard_vendeur.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Mercato Nova</title>
    <style>
        body { background-color: #0a0a0a; color: #fff; font-family: 'Georgia', serif; margin: 0; display: flex; align-items: center; justify-content: center; height: 100vh; flex-direction: column; }
        h1 { font-size: 4em; color: #d4af37; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 2px; }
        p { font-family: sans-serif; color: #888; margin-bottom: 50px; letter-spacing: 1px; font-size: 1.2em; }
        .btn-container { display: flex; gap: 20px; }
        .btn { padding: 15px 40px; text-decoration: none; font-family: sans-serif; font-weight: bold; text-transform: uppercase; border-radius: 4px; border: 1px solid #d4af37; transition: 0.3s; font-size: 1em; }
        .btn-login { background: #d4af37; color: #000; }
        .btn-login:hover { background: #fff; border-color: #fff; }
        .btn-register { background: transparent; color: #d4af37; }
        .btn-register:hover { background: #d4af37; color: #000; }
    </style>
</head>
<body>
    <h1>Mercato Nova</h1>
    <p>L'excellence de l'acquisition secrète</p>
    <div class="btn-container">
        <a href="connexion.php" class="btn btn-login">Se Connecter</a>
        <a href="inscription.php" class="btn btn-register">S'inscrire</a>
    </div>
</body>
</html>