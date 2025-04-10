<?php
/**
 * @var Utilisateur $utilisateur
 * @var int $abonne
 * @var int $abonnement
 * @var AbonneDAO $abonne_dao
 * @var UtilisateurDAO $utilisateur_dao
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?= $utilisateur->getPseudo()?></title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <script src="../../public/scripts/lien_to_pressepapier.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <link rel="stylesheet" href="../../public/styles/style.css">
</head>

<body>
    <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
    <div>
        <img src="../../public/<?= htmlspecialchars($utilisateur->getCheminPhoto())?>" style="width: 100px; height: 100px; border-radius: 30%; cursor: pointer;"
            id="profileImage" alt="Profil"
            <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
                onclick="document.getElementById('imageInput').click();"
            <?php endif; ?>
        >

        <input type="file" id="imageInput" accept="image/*" style="display: none;">
        <div id="cropperContainer">
            <img id="imagePreview" src="" alt="Preview">
            <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
            <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
        </div>



        <h1><?= htmlspecialchars($utilisateur->getPseudo())?></h1>
        <p><?= "\n". htmlspecialchars($utilisateur->getBio())?></p>
        <p id="compteur_abonne"><?= "Abonnés : $abonne\n"?></p>
        <p><?= "Abonnements : $abonnement"?></p>
        <p><?="Compte créé le ". (new DateTime($utilisateur->getDateInscription()))->format('d/m/Y')?></p>
        <button onclick="partagerURL()">Partager le profil</button><br>

        <?php if ($utilisateur->getPseudo() != $_SESSION['Pseudo']): ?>
            <?php if ($abonne_dao->estAbonne($utilisateur_dao->getIdByPseudo($_SESSION['Pseudo']), $utilisateur->getId())): ?>
                <button id="btnAbonnement" data-pseudo="<?= $utilisateur->getPseudo() ?>">Se désabonner</button>
            <?php else: ?>
                <button id="btnAbonnement" data-pseudo="<?= $utilisateur->getPseudo() ?>">S'abonner</button>
            <?php endif; ?>
        <?php endif; ?>

        <!-- Ajoutez ce bouton dans profil.php -->
        <!-- TODO : Paramètres à implémenter -->
        <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
            <button id="btnParametres">⚙️ Paramètres</button>
            <div id="parametres" style="display: none;">
                <!-- Formulaire des paramètres -->
                <h1>Paramètres</h1>
                <p>

                </p>

            </div>
        <?php endif; ?>
    </div>

    <script src="../../public/scripts/gestion_abonnement.js"></script>
    <script src="../../public/scripts/image_cropper.js"></script>
</body>
</html>