<?php
/**
 * @var string $pseudo
 * @var string $token
 */
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
</head>

<body>
    <form action="?action=resetmdp&token=<?= urlencode($token)?>" method="POST">
        <h1><a href="./" style="text-decoration: none">⬅️</a>Réinitialiser votre mot de passe</h1>
        <p><?= htmlspecialchars($pseudo)?>, saisissez votre nouveau mot de passe.</p>

        <input type="password" name="mdp" placeholder="Mot de passe" required><br><br>

        <?php if (isset($_SESSION['erreurs']['mdp'])): ?>
            <span style="color: red"><?= $_SESSION['erreurs']['mdp'] ?></span><br>
            <?php unset($_SESSION['erreurs']['mdp']); ?>
        <?php endif; ?>

        <input type="submit" name="envoyer" value="Modifier votre mot de passe">
    </form>
</body>
</html>
