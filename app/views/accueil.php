<?php
/**
 * @var Utilisateur $utilisateur
 * @var array $communautes
 * @var array $erreurs
 */
?>
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
    <div class="flex">
        <img src="../../public/images/Logo_ForaVerse.png" alt="Logo" style="width: 100px; height: auto;">
        <h1 id="title">ForaVerse</h1>
        <?php if (!isset($_SESSION['Pseudo'])): ?>
            <a href="./?action=connexion">Connexion</a>
            <a href="./?action=inscription">Inscription</a>
        <?php else: ?>
            <button id="btnCreerCommu">➕Créer</button>
            <div id="creerCommuContainer" class="modal">
                <div class="modal-content">
                    <h1>Créer une communauté</h1><h1 id="closeCommuContainer" style="cursor: pointer;">❌</h1>
                    <form method="POST" action="?action=accueil#creerCommu" novalidate>
                        <input type="text" name="nomCommu" placeholder="Nom de la communauté"><br><br>
                        <?php if (!empty($erreurs['nomCommu'])): ?>
                            <span style="color: red"><?= $erreurs['nomCommu'] ?></span><br>
                        <?php endif; ?>
                        <textarea name="descriptionCommu" style="resize: none;" rows="10" cols="100" placeholder="Description de la communauté"></textarea><br><br>
                        <?php if (!empty($erreurs['descriptionCommu'])): ?>
                            <span style="color: red"><?= $erreurs['descriptionCommu'] ?></span><br>
                        <?php endif; ?>
                        <label for="visibilite">Visibilité</label>
                        <select name="visibilite" id="visibilite">
                            <option value="publique">Communauté Publique</option>
                            <option value="privee">Communauté Privée</option>
                        </select><br><br>
                        <button type="submit">Créer</button>
                    </form>
                </div>
            </div>
            <a href="./?action=deconnexion">Déconnexion</a>
            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($_SESSION['Pseudo'])?>">
                <img src="../../public/<?= htmlspecialchars($utilisateur->getCheminPhoto()) ?>" alt="Profil" style="width: 50px; height: 50px; border-radius: 30%">
            </a>
        <?php endif; ?>
    </div>
    <div>
        <h1>Liste des communautés :</h1>
        <?php 
        foreach($communautes as $communaute){
            echo "<a style='text-decoration: none;'  href=\"./?action=communaute&nomCommu={$communaute->getNom()}\">
                    <h3>{$communaute->getNom()}</h3>
                </a>";
        } 
        ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('creerCommuContainer');
            const button = document.getElementById('btnCreerCommu');
            const close_button = document.getElementById('closeCommuContainer');

            button.onclick = function(){
                window.location.hash = "creerCommu";
            }

            close_button.onclick = function(){
                history.pushState("", document.title, window.location.pathname + window.location.search);
                showModalBasedOnHash();
            }

            function showModalBasedOnHash(){
                if (window.location.hash === "#creerCommu") {
                    modal.style.display = "block";
                }
                else{
                    modal.style.display = "none";
                }
            }

            window.addEventListener('hashchange', showModalBasedOnHash);
            showModalBasedOnHash();
        });
    </script>
</body>
</html>