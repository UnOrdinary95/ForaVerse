<?php
/**
 * @var Utilisateur $session_user
 */
?>

<header class="items-center">
    <div id="hcontainer1">
        <a class="flex items-center gap1" href="./?action=accueil">
            <img src="../../../public/images/favicon/favicon_foraverse.png" alt="Logo" class="logo">
            <h1 class="font_titre text-gradient">ForaVerse</h1>       
        </a>
    </div>

    <?php if($_GET['action'] == 'accueil' || $_GET['action'] == 'profil'): ?>
        <div class="header_container <?php if($_GET['action'] == 'profil'){print 'invisible';}?>" id="hcontainer2">
            <form  method="POST" action="?action=">
                <input name="recherche_mot_cle" type="text" placeholder="🔍Rechercher dans ForaVerse">
            </form>
        </div>

    <?php elseif($_GET['action'] == 'communaute' || $_GET['action'] == 'publication'): ?>
        <div class="header_container" id="hcontainer2">
            <form  method="POST" action="?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>">
                <input name="discussion_mot_cle" type="text" placeholder="🔍Rechercher une discussion">
            </form>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-evenly" id="hcontainer3">
    <?php if (!isset($_SESSION['Pseudo'])): ?>
        <a href="./?action=connexion">
            <button>Connexion</button>    
        </a>
        <a href="./?action=inscription">
            <button>Inscription</button>
        </a>
    <?php else: ?>
        <div>
            <button id="btnCreerCommu" class="flex items-center gap2 <?php if($_GET['action'] == 'profil' || $_GET['action'] == 'communaute' || $_GET['action'] == 'publication'){print 'invisible';}?>">
                <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                >
                <circle cx="12" cy="12" r="10" />
                <path d="M8 12h8" />
                <path d="M12 8v8" />
                </svg>

                Créer
            </button>
        </div>

        <a href="./?action=deconnexion">
            <button class="flex items-center gap2">
                <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                >
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                <polyline points="16 17 21 12 16 7" />
                <line x1="21" x2="9" y1="12" y2="12" />
                </svg>

                Déconnexion
            </button>
        </a>
    
        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($_SESSION['Pseudo'])?>">
            <img id="pp" src="../../public/<?= htmlspecialchars($session_user->getCheminPhoto()) ?>" alt="Profil" style="width: 50px; height: 50px; border-radius: 30%">
        </a>
        <?php endif; ?>
    </div>
</header>
<div id="creerCommuContainer" class="modal">
    <div class="modal-content">
        <div class="flex items-center justify-between">
            <h2>Créer une communauté</h2>
            <svg id="closeCommuContainer" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
        </div>
        <form method="POST" action="?action=accueil#creerCommu" novalidate>
            <input type="text" name="nomCommu" placeholder="Nom de la communauté"><br><br>
            <?php if (!empty($erreurs['nomCommu'])): ?>
                <span class="error"><?= $erreurs['nomCommu'] ?></span><br>
            <?php endif; ?>
            <textarea name="descriptionCommu" style="resize: none;" rows="10" cols="100" placeholder="Description de la communauté"></textarea><br><br>
            <?php if (!empty($erreurs['descriptionCommu'])): ?>
                <span class="error"><?= $erreurs['descriptionCommu'] ?></span><br>
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
