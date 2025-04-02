<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="icon" type="image/png" href="../../public/images/Logo_ForaVerse.png">
</head>
<body>
    <form action="?action=connexion" method="POST" autocomplete="on">
        <h1><a href="./" style="text-decoration: none">⬅️</a> Se connecter</h1>
        <input type="text" name="identifiant" placeholder="Adresse email ou pseudo"><br><br>
        <?php if (!empty($erreurs['identifiant'])): ?>
            <span style="color: red"><?= $erreurs['identifiant'] ?></span><br>
        <?php endif; ?>
        <input type="password" name="mdp" placeholder="Mot de passe"><br><br>
        <?php if (!empty($erreurs['mdp'])): ?>
            <span style="color: red"><?= $erreurs['mdp'] ?></span><br>
        <?php endif; ?>
        <?php if (!empty($erreurs['idmdp'])): ?>
            <span style="color: red"><?= $erreurs['idmdp'] ?></span><br>
        <?php endif; ?>
        <input type="submit" name="connecter" value="Se connecter">
    </form>
    <p>Nouveau dans ForaVerse ?
        <a href="./?action=inscription">Créer un compte</a>
    </p>
</body>
