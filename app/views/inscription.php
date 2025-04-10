<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
</head>

<body>
    <form action="?action=inscription" method="POST" autocomplete="on" novalidate>
        <h1><a href="./" style="text-decoration: none">⬅️</a> Créer un compte</h1>
        <input type="text" name="pseudo" placeholder="Pseudo"><br><br>
        <?php if (!empty($erreurs['pseudo'])): ?>
            <span style="color: red"><?= $erreurs['pseudo'] ?></span><br>
        <?php endif; ?>
        <input type="text" name="email" placeholder="Adresse email"><br><br>
        <?php if (!empty($erreurs['email'])): ?>
            <span style="color: red"><?= $erreurs['email'] ?></span><br>
        <?php endif; ?>
        <input type="password" name="mdp" placeholder="Mot de passe"><br><br>
        <?php if (!empty($erreurs['mdp'])): ?>
            <span style="color: red"><?= $erreurs['mdp'] ?></span><br>
        <?php endif; ?>
        <input type="submit" name="creer" value="S'inscrire">
    </form>
    <p>Vous avez déjà un compte ?
        <a href="./?action=connexion">Connectez-vous ici</a>
    </p>
</body>
</html>