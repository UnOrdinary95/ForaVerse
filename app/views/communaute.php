<?php
/* * Affichage de la communauté
 * 
 * @var Communaute $communaute
 * @var Role $role
 * @var int $nbr_membres
*/
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communauté - <?= htmlspecialchars($communaute->getNom()) ?></title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
    <div style="display:flex; min-height: 100vh;">
        <div style="width: 80vw; border: 3px solid black;">
            <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
            <img src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 50px; height: 50px; border-radius: 50%">
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <?php if (isset($_SESSION['Pseudo'])): ?>
                <?php if (!$role): ?>
                    <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Rejoindre</button>
                <?php elseif ($role->estMembreOuModerateur()): ?>
                    <button id="btnAdhesion" data-communaute_id="<?= $communaute->getId() ?>">Quitter</button>
                <?php elseif ($role->estProprietaire()): ?>
                    <button id="btnGestion">Gérer</button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div style="width: 20vw;border: 3px solid black;">
            <img id="communauteImage" src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 75px; height: 75px; border-radius: 50%; cursor: pointer;" 
                <?php if (isset($_SESSION['Pseudo'])): ?>
                    onclick="document.getElementById('imageInput').click();"
                <?php endif; ?>
            >
            
            <input type="file" id="imageInput" accept="image/*" style="display: none;">
            <div id="cropperContainer" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 0; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.3); z-index: 1000; width: 40vw; max-width: 100vw; display: none;">
                <img id="imagePreview" src="" alt="Preview">
                <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
                <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
            </div>
            
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <p id="description"><?= nl2br(htmlspecialchars($communaute->getDescription())) ?></p>
            <p>Visibilité : <?= $communaute->getVisibilite() == true ? "Publique" : "Privée" ?></p>
            <p id="compteurMembres"><?= htmlspecialchars($nbr_membres) . " Membres"?></p>
        </div>
    </div>
    
    <script src="../../public/scripts/gestion_adhesion.js"></script>
    <script src="../../public/scripts/image_cropper_commu.js"></script>
</body>
</html>