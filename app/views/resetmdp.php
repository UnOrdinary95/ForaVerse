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
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>

<body class="flex justify-center items-center" style="height:92vh;">
    <main class="card" style="max-width:400px; width:90%; padding:2rem;">
        <form action="?action=resetmdp&token=<?= urlencode($token)?>" method="POST" novalidate>
            <div class="margin3">
                <h1>Réinitialiser votre mot de passe</h1>
                <p><?= htmlspecialchars($pseudo)?>, saisissez votre nouveau mot de passe.</p>
            </div>
            
            <input type="password" name="mdp" placeholder="Mot de passe" required><br><br>

            <?php if (isset($_SESSION['erreurs']['mdp'])): ?>
                <span class="error margin-left3"><?= $_SESSION['erreurs']['mdp'] ?></span><br>
                <?php unset($_SESSION['erreurs']['mdp']); ?>
            <?php endif; ?>

            <input type="submit" name="envoyer" value="Modifier votre mot de passe">
        </form>
    </main>
</body>
</html>
