<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>

<body class="flex justify-center items-center h92">
    <main class="card main_auth">
        <form action="?action=inscription" method="POST" autocomplete="on" novalidate>
            <h1 class="margin3">Créer un compte</h1>
            <input type="text" name="pseudo" placeholder="Pseudo"><br><br>
            <?php if (!empty($erreurs['pseudo'])): ?>
                <span class="error margin-left2"><?= $erreurs['pseudo'] ?></span><br>
            <?php endif; ?>
            <input type="text" name="email" placeholder="Adresse email"><br><br>
            <?php if (!empty($erreurs['email'])): ?>
                <span class="error margin-left2"><?= $erreurs['email'] ?></span><br>
            <?php endif; ?>
            <input type="password" name="mdp" placeholder="Mot de passe"><br><br>
            <?php if (!empty($erreurs['mdp'])): ?>
                <span class="error margin-left2"><?= $erreurs['mdp'] ?></span><br>
            <?php endif; ?>
            <input type="submit" name="creer" value="S'inscrire">
        </form>
        <div class="margin2">
            <p>Vous avez déjà un compte ?
                <a href="./?action=connexion">Connectez-vous ici</a>
            </p>
        </div>
    </main>
</body>
</html>