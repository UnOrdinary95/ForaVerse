<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>

<body class="flex justify-center items-center h92">
    <main class="card main_auth">
        <form action="?action=connexion" method="POST" autocomplete="on" novalidate>
            <h1 class="margin3">Se connecter</h1>
            <input type="text" name="identifiant" placeholder="Adresse email ou pseudo"><br><br>
            <?php if (!empty($erreurs['identifiant'])): ?>
                <small class="margin-left2 error"><?= $erreurs['identifiant'] ?></small><br>
            <?php endif; ?>
            <input type="password" name="mdp" placeholder="Mot de passe"><br><br>
            <?php if (!empty($erreurs['mdp'])): ?>
                <small class="margin-left2 error"><?= $erreurs['mdp'] ?></small><br>
            <?php endif; ?>
            <?php if (!empty($erreurs['idmdp'])): ?>
                <small class="margin-left2 error"><?= $erreurs['idmdp'] ?></small><br>
            <?php endif; ?>
            <input class="bg-gradient pointer" type="submit" name="connecter" value="Se connecter"><br>
        </form>
        <div class="margin2">
            <a href="./?action=demande_resetmdp">Mot de passe oublié ?</a>
        </div>
        <p>Nouveau dans ForaVerse ?
            <a href="./?action=inscription">Créer un compte</a>
        </p>
    </main>
</body>
</html>
