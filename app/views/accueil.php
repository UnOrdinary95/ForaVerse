<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue</title>
</head>
<body>
<div>
    <h1>Bienvenue sur notre plateforme [DEBUG]</h1>
    <div>
        <?php if (!isset($_SESSION['UserID'])): ?>
        <a href="./?action=connexion">Connexion</a>
        <a href="./?action=inscription">Inscription</a>
        <?php else: ?>
        <a href="./?action=deconnexion">Déconnexion</a>
        <?php endif; ?>
    </div>
</div>
</body>
</html>