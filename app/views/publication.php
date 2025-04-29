
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=htmlspecialchars($publication->getTitre())?></title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../public/scripts/lien_to_pressepapier.js"></script>
</head>
<body>
    <?php include_once 'components/header.php'; ?>
    <main class="flex">
        <?php include_once 'components/left_sidebar.php'; ?>

        <div id="pub_container" class="padding-x4">
            <div>
                <div class="flex items-center margin3 gap2">
                    <a href="./?action=communaute&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>" class="flex items-center">
                        <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m313-440 224 224-57 56-320-320 320-320 57 56-224 224h487v80z"/></svg>
                    </a>
                    
                    <img src="../../public/<?= htmlspecialchars($publication->getUtilisateur()->getCheminPhoto()) ?>" alt="ProfilAuteurDiscussion" style="width: 50px; height: 50px; border-radius: 50%">
                    <div class="flex flex-col">
                        <div class="flex items-center gap2"> 
                            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($publication->getUtilisateur()->getPseudo()) ?>">
                                <h3><?= htmlspecialchars($publication->getUtilisateur()->getPseudo()) ?></h3>
                            </a>
                            <span style="color: orange; font-weight: bold;">{Auteur}</span>
                        </div>   
                        <small> le <?= (new DateTime($publication->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($publication->getDateCreation()))->format('H:i') ?></small>
                    </div>
                </div>
            </div>

            
            <h2 class="margin2"><?= htmlspecialchars($publication->getTitre()) ?></h2>
            <p><?= nl2br(htmlspecialchars($publication->getContenu()))?></p>
            <br>
            <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container card bg-border" data-publication-id=<?=$publication->getIdPublication()?>>
                <?php if(isset($_SESSION['Pseudo'])){
                    switch($publication->getVoteUtilisateurCourant()){
                        case 1:
                            print '
                            <svg class="vote-up active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                            <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                            <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                            break;
                        case -1:
                            print '
                            <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                            <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                            <svg class="vote-down active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                            break;
                        default:
                            print '
                            <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                            <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                            <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                    }
                    print '
                    <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                        <input type="hidden" name="idPublication" value="'.htmlspecialchars($publication->getIdPublication()).'">
                        <input type="hidden" name="Favoris" value="1">';
                    if ($publication->estFavoris()){
                        print '<svg onclick="this.closest(\'form\').submit();" class="svg_red pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120zm400-600H280v520h400zm-400 0v520z"/>Favoris</svg>';
                    }
                    else{
                        print '<svg onclick="this.closest(\'form\').submit();" class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m480-120-58-52q-101-91-167-157T150-447.5 95.5-544 80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5 705-329 538-172zm0-108q96-86 158-147.5t98-107 50-81 14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81 98 107T480-228m0-273"/>Favoris</svg>';
                    }
                    print '</form>';
                    
                    if (isset($role) && $role->peutModerer()){
                        print '
                        <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                            <input type="hidden" name="idPublication" value="'.htmlspecialchars($publication->getIdPublication()).'">
                            <input type="hidden" name="epinglerPublication" value="1">';
                        if ($publication->estEpingle()){
                            print '<svg
                            onclick="this.closest(\'form\').submit();" class="svg_red pointer"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            >
                            <path d="M12 17v5" />
                            <path d="M15 9.34V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H7.89" />
                            <path d="m2 2 20 20" />
                            <path d="M9 9v1.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h11" />
                            </svg>
                            ';                        
                        }
                        else{
                            print '<svg
                            onclick="this.closest(\'form\').submit();" class="svg_red pointer"
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            >
                            <path d="M12 17v5" />
                            <path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z" />
                            </svg>
                            ';                        
                        }
                        print '</form>';
                    }
                    print '<svg onclick="partagerURL()" class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M720-80q-50 0-85-35t-35-85q0-7 1-14.5t3-13.5L322-392q-17 15-38 23.5t-44 8.5q-50 0-85-35t-35-85 35-85 85-35q23 0 44 8.5t38 23.5l282-164q-2-6-3-13.5t-1-14.5q0-50 35-85t85-35 85 35 35 85-35 85-85 35q-23 0-44-8.5T638-672L356-508q2 6 3 13.5t1 14.5-1 14.5-3 13.5l282 164q17-15 38-23.5t44-8.5q50 0 85 35t35 85-35 85-85 35m0-640q17 0 28.5-11.5T760-760t-11.5-28.5T720-800t-28.5 11.5T680-760t11.5 28.5T720-720M240-440q17 0 28.5-11.5T280-480t-11.5-28.5T240-520t-28.5 11.5T200-480t11.5 28.5T240-440m480 280q17 0 28.5-11.5T760-200t-11.5-28.5T720-240t-28.5 11.5T680-200t11.5 28.5T720-160m0-40"/></svg>';
                    print '</span>
            <br>
            <form method="POST" class="flex items-end gap2" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                <img src="../../public/'.htmlspecialchars($session_user->getCheminPhoto()).'" alt="ProfilCommentaire" style="width: 50px; height: 50px; border-radius: 50%">
                <textarea name="contenuCommentaire" style="resize: vertical; border-radius: 30px; width: 70%; min-height: 42px; height: 42px; padding: 10px 20px;" placeholder="Commentaire"></textarea>
                <div><button type="submit" name="CreerCommentaire">Commenter</button></div>
            </form>';
                }
                else{
                    print '
                    <a href="./?action=connexion">
                        <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                    </a>
                    <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                    <a href="./?action=connexion">
                        <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>
                    </a>';
                    print '<svg onclick="partagerURL()" class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M720-80q-50 0-85-35t-35-85q0-7 1-14.5t3-13.5L322-392q-17 15-38 23.5t-44 8.5q-50 0-85-35t-35-85 35-85 85-35q23 0 44 8.5t38 23.5l282-164q-2-6-3-13.5t-1-14.5q0-50 35-85t85-35 85 35 35 85-35 85-85 35q-23 0-44-8.5T638-672L356-508q2 6 3 13.5t1 14.5-1 14.5-3 13.5l282 164q17-15 38-23.5t44-8.5q50 0 85 35t35 85-35 85-85 35m0-640q17 0 28.5-11.5T760-760t-11.5-28.5T720-800t-28.5 11.5T680-760t11.5 28.5T720-720M240-440q17 0 28.5-11.5T280-480t-11.5-28.5T240-520t-28.5 11.5T200-480t11.5 28.5T240-440m480 280q17 0 28.5-11.5T760-200t-11.5-28.5T720-240t-28.5 11.5T680-200t11.5 28.5T720-160m0-40"/></svg>
                    </span>
                    <br>';
                }?>
            
            <hr class="margin4">

            <div class="flex items-center gap3">
                <p>Trié par :</p>
                
                <form class="flex gap3" method="POST" action="?action=publication&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>&idPublication=<?= htmlspecialchars($publication->getIdPublication()) ?>">
                    <input type="hidden" name="idPublication" value="<?= htmlspecialchars($publication->getIdPublication()) ?>">
                    <select name="tri" id="tri">
                        <option value="select">Sélectionner un tri</option>
                        <option value="recents">Récents (Par défaut)</option>
                        <option value="anciens">Anciens</option>
                        <option value="upvotes">Upvotes (⬆️)</option>
                        <option value="downvotes">Downvotes (⬇️)</option>
                    </select>
                    <button type="submit" id="triButton">✅</button>
                </form>
                
                <form method="POST" action="?action=publication&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>&idPublication=<?= htmlspecialchars($publication->getIdPublication()) ?>">
                    <input name="commentaire_mot_cle" type="text" placeholder="🔍Rechercher un commentaire" style="width: 30vw; border-radius: 30px; min-height: 40px; height: 40px; padding: 10px 20px;">
                </form>
            </div>
            <br>
            <div id="commentaireContainer">
                <?php if (isset($commentaires) && count($commentaires) > 0): ?>
                    <?php foreach ($commentaires as $commentaire): ?>
                        <div class="commentaire card bg-border margin3" style="border: 1px solid silver;">
                            <div>
                                <div class="flex items-center gap1">
                                    <img src="../../public/<?= htmlspecialchars($commentaire->getUtilisateur()->getCheminPhoto()) ?>" alt="ProfilCommentaire" style="width: 50px; height: 50px; border-radius: 50%">
                                    
                                    <div class="flex flex-col">
                                        <div class="flex items-center gap2">
                                            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($commentaire->getUtilisateur()->getPseudo()) ?>">
                                                <h2><?= htmlspecialchars($commentaire->getUtilisateur()->getPseudo()) ?></h2>
                                            </a>
                                            
                                            <?php if($commentaire->getUtilisateur()->estAdministrateur()): ?>
                                                <span style="color: red; font-weight: bold;">{Admin}</span>
                                            <?php elseif($commentaire->getRoleUtilisateur()->estProprietaire()): ?>
                                                <span style="color: purple; font-weight: bold;">{Propriétaire}</span>
                                            <?php elseif($commentaire->getRoleUtilisateur()->estModerateur()): ?>
                                                <span style="color: cyan; font-weight: bold;">{Modérateur}</span>
                                            <?php elseif ($commentaire->getIdUtilisateur() == $publication->getIdUtilisateur()): ?>
                                                <span style="color: orange; font-weight: bold;">{Auteur}</span>
                                            <?php else: ?>
                                                <span style="color: silver; font-weight: bold;">{Membre}</span>
                                            <?php endif;?>

                                            <?php if($commentaire->estEpingle()): ?>
                                                <span>📌</span>
                                            <?php endif; ?>

                                            <?php if (isset($role) && $role->peutModerer() || isset($session_user) && $commentaire->getIdUtilisateur() == $session_user->getId()): ?>
                                                <form class="flex justify-end" style="margin: 0; padding: 0;" method="POST" action="?action=publication&nomCommu=<?= htmlspecialchars($_GET['nomCommu']) ?>&idPublication=<?= htmlspecialchars($_GET['idPublication']) ?>">
                                                    <input type="hidden" name="deleteCommentaire" value="<?= $commentaire->getIdPublication() ?>">
                                                    <svg onclick="this.closest('form').submit();" class="pointer svg_red" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 -960 960 960"><path d="m256-200-56-56 224-224-224-224 56-56 224 224 224-224 56 56-224 224 224 224-56 56-224-224z"/></svg>    
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                        <small>le <?= (new DateTime($commentaire->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($commentaire->getDateCreation()))->format('H:i') ?></small>
                                    </div>
                                </div>
                            </div>

                            <p class="margin4" style="color: aliceblue"><?= nl2br(htmlspecialchars($commentaire->getContenu()))?></p>

                            <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container" data-publication-id=<?=$commentaire->getIdPublication()?>>
                                <?php if(isset($_SESSION['Pseudo'])){
                                    switch($commentaire->getVoteUtilisateurCourant()){
                                        case 1:
                                            print '
                                            <svg class="vote-up active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                            <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                            <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                            break;
                                        case -1:
                                            print '
                                            <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                            <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                            <svg class="vote-down active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                            break;
                                        default:
                                            print '
                                            <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                            <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                            <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                    }
                                    print '
                                    <a href="./?action=commentaire&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($commentaire->getIdPublication()).'">
                                        <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M240-400h480v-80H240zm0-120h480v-80H240zm0-120h480v-80H240zM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800zM160-320h594l46 45v-525H160zm0 0v-480z"/></svg>
                                    </a>
                                    <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                                        <input type="hidden" name="idPublication" value="'.htmlspecialchars($commentaire->getIdPublication()).'">
                                        <input type="hidden" name="Favoris" value="1">';
                                    if ($commentaire->estFavoris()){
                                        print '<svg onclick="this.closest(\'form\').submit();" class="svg_red pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120zm400-600H280v520h400zm-400 0v520z"/>Favoris</svg>';
                                    }
                                    else{
                                        print '<svg onclick="this.closest(\'form\').submit();" class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m480-120-58-52q-101-91-167-157T150-447.5 95.5-544 80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5 705-329 538-172zm0-108q96-86 158-147.5t98-107 50-81 14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81 98 107T480-228m0-273"/>Favoris</svg>';
                                    }
                                    print '</form>';
                                    
                                    if (isset($role) && $role->peutModerer()){
                                        print '
                                        <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                                            <input type="hidden" name="idPublication" value="'.htmlspecialchars($commentaire->getIdPublication()).'">
                                            <input type="hidden" name="epinglerPublication" value="1">';
                                        if ($commentaire->estEpingle()){
                                            print '<svg
                                            onclick="this.closest(\'form\').submit();" class="svg_red pointer"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            >
                                            <path d="M12 17v5" />
                                            <path d="M15 9.34V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H7.89" />
                                            <path d="m2 2 20 20" />
                                            <path d="M9 9v1.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h11" />
                                            </svg>
                                            ';                        
                                        }
                                        else{
                                            print '<svg
                                            onclick="this.closest(\'form\').submit();" class="svg_red pointer"
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            >
                                            <path d="M12 17v5" />
                                            <path d="M9 10.76a2 2 0 0 1-1.11 1.79l-1.78.9A2 2 0 0 0 5 15.24V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-.76a2 2 0 0 0-1.11-1.79l-1.78-.9A2 2 0 0 1 15 10.76V7a1 1 0 0 1 1-1 2 2 0 0 0 0-4H8a2 2 0 0 0 0 4 1 1 0 0 1 1 1z" />
                                            </svg>
                                            ';                        
                                        }
                                        print '</form>';
                                    }
                                }
                                else{
                                    print '
                                    <a href="./?action=connexion">
                                        <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                    </a>
                                    <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                    <a href="./?action=connexion">
                                        <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>
                                    </a>';
                                    print '
                                    <a href="./?action=commentaire&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($commentaire->getIdPublication()).'">
                                        <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M240-400h480v-80H240zm0-120h480v-80H240zm0-120h480v-80H240zM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800zM160-320h594l46 45v-525H160zm0 0v-480z"/></svg>
                                    </a>
                                    </span>';
                                }?>
                            </span>
                            <br>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun commentaire pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php include_once 'components/right_sidebar.php'; ?>
    </div>
    </main>
    <script>
        if (window.location.hash === "#creerDiscussionContainer"){
            window.history.replaceState("", document.title, window.location.pathname + window.location.search);
        }
    </script>
    <script src="../../public/scripts/creer_commu.js"></script>
    <script src="../../public/scripts/communaute_discussion.js"></script>
    <script src="../../public/scripts/vote_publication.js"> </script>
</body>
</html>