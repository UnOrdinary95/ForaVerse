<?php
/**
 * @var Utilisateur $utilisateur
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css">
</head>
<body>
    <div class="flex">
        <img src="../../public/images/Logo_ForaVerse.png" alt="Logo" style="width: 100px; height: auto;">
        <h1 id="title">ForaVerse</h1>
        <a>
            <?php if (!isset($_SESSION['Pseudo'])): ?>
            <a href="./?action=connexion">Connexion</a>
            <a href="./?action=inscription">Inscription</a>
            <?php else: ?>
            <?="Bonjour " . $_SESSION['Pseudo']?>
            <a href="./?action=deconnexion">Déconnexion</a>
            <a href="./?action=profil&utilisateur=<?= $_SESSION['Pseudo']?>">
                <img src="../../public/<?= $utilisateur->getCheminPhoto() ?: 'images/pp_user/sunday2.jpg' ?>" alt="Profil" style="width: 50px; height: 50px; border-radius: 30%">
            </a>
            <?php endif; ?>
    </div>
</body>
</html>