
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
    <style>
        #cropperContainer {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            z-index: 1000;
            width: 40vw;
            max-width: 100vw;
            /*overflow: hidden;*/
        }

        #imagePreview {
            width: 100%;
            max-height: 40vh;
            display: block;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
    <div>
        <img src="../../public/<?= $utilisateur->getCheminPhoto() ?: 'images/pp_user/sunday2.jpg' ?>" style="width: 100px; height: 100px; border-radius: 30%; cursor: pointer;"
             id="profileImage" alt="Profil"
            <?php if ($utilisateur->getPseudo() == $_SESSION['Pseudo']): ?>
                onclick="document.getElementById('imageInput').click();"
            <?php endif; ?>
        >

        <input type="file" id="imageInput" accept="image/*" style="display: none;">
        <div id="cropperContainer" style="display: none;">
            <img id="imagePreview" src="" alt="Preview">
            <button type="button" id="cropButton" style="display: block; margin-top: 10px;">Enregistrer</button>
            <button type="button" id="cancelButton" style="display: block; margin-top: 10px;">Annuler</button>
        </div>



        <h1><?= $utilisateur->getPseudo()?></h1>
        <p><?= "\n".$utilisateur->getBio()?></p>
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

    <script>
        $(document).ready(function() {
            $('#btnAbonnement').click(function() {
                const pseudo = $(this).data('pseudo');
                const action = $(this).text() === 'S\'abonner' ? 'ajouterabonnement' : 'supprimerabonnement';

                $.post('../../app/utils/traitement.php',
                    {
                        action: action,
                        utilisateur: pseudo
                    },
                    function() {
                    if (action === 'ajouterabonnement') {
                        $('#btnAbonnement').text('Se désabonner');
                    } else {
                        $('#btnAbonnement').text('S\'abonner');
                    }

                })
                .done(function(data) {
                    const response = JSON.parse(data);
                    $('#compteur_abonne').text('Abonnés : ' + response.nbAbonnes);
                });
            });
        });
    </script>
<script src="../../public/scripts/image_cropper.js"></script>
</body>
</html>