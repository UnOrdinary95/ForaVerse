<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
    <link rel="icon" type="image/png" href="../../public/images/Logo_ForaVerse.png">
    <link rel="stylesheet" href="../../public/styles/style.css">
</head>
<body>
<div class="flex">
    <img src="../../public/images/Logo_ForaVerse.png" alt="Logo" style="width: 100px; height: auto;">
    <h1 id="title">ForaVerse</h1>
    <div>
        <?php if (!isset($_SESSION['UserID'])): ?>
        <a href="./?action=connexion">Connexion</a>
        <a href="./?action=inscription">Inscription</a>
        <?php else: ?>
        <a href="./?action=deconnexion">Déconnexion</a>
        <img src="../../public/images/pp_user/guest.jpeg" alt="Profil" style="width: 50px; border-radius: 50%">
        <?php endif; ?>
    </div>
</div>
</body>
</html>