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
    <div style="display:flex; min-height: 100vh;">
        <div style="width: 80vw; border: 3px solid black;">
            <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
            <img src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 50px; height: 50px; border-radius: 50%">
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>  
        </div>
        <div style="border: 3px solid black;">
            <img src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 50px; height: 50px; border-radius: 50%">
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <p id="description"><?= nl2br(htmlspecialchars($communaute->getDescription())) ?></p>
            <p>Visibilité : <?= $communaute->getVisibilite() == true ? "Publique" : "Privée" ?></p>
        </div>
    </div>
</body>
</html>