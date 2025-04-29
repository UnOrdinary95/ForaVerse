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
    <link rel="stylesheet" href="../../public/styles/style.css?<?php echo time(); ?>">
</head>
<body>
    <?php include_once 'components/header.php'; ?>   

    <main class="flex auto_w">
        <?php include_once 'components/left_sidebar.php'; ?>
        
        <div class="flex justify-center" style="width: 100%;">
            <div id="discussionContainer" style="width: 50%;">
                <?php if (isset($discussions) && count($discussions) > 0): ?>
                    <?php foreach ($discussions as $discussion): ?>
                        <div class="flex justify-center items-center auto_w margin4">
                            <div class="discussion card" style="border: 1px solid silver; width: 100%;">
                                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($discussion->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none; display: flex; align-items: center;">
                                    <p><?= htmlspecialchars($discussion->getUtilisateur()->getPseudo()) ?>, le <?= (new DateTime($discussion->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($discussion->getDateCreation()))->format('H:i') ?></p>
                                    <?php if ($discussion->estEpingle()): ?>
                                        <p>📌</p>
                                    <?php endif; ?>
                                </a>
                                <a href="./?action=publication&nomCommu=<?= htmlspecialchars($discussion->getCommunaute()->getNom()) ?>&idPublication=<?= htmlspecialchars($discussion->getIdPublication()) ?>" style="text-decoration: none;">
                                    <h2 class="text-gradient"><?= htmlspecialchars($discussion->getTitre()) ?></h2>
                                </a>
                                <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container card bg-border" data-publication-id=<?=$discussion->getIdPublication()?>>
                                    <?php if(isset($_SESSION['Pseudo'])){
                                        switch($discussion->getVoteUtilisateurCourant()){
                                            case 1:
                                                print '
                                                <svg class="vote-up active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                                <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                                break;
                                            case -1:
                                                print '
                                                <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                                <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                <svg class="vote-down active pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                                break;
                                            default:
                                                print '
                                                <svg class="vote-up pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                                <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                                <svg class="vote-down pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>';
                                        }
                                        print '
                                        <a href="./?action=publication&nomCommu='.htmlspecialchars($discussion->getCommunaute()->getNom()).'&idPublication='.htmlspecialchars($discussion->getIdPublication()).'">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M240-400h480v-80H240zm0-120h480v-80H240zm0-120h480v-80H240zM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800zM160-320h594l46 45v-525H160zm0 0v-480z"/></svg>
                                        </a>
                                        <form method="POST" action="?action=communaute&nomCommu='.htmlspecialchars($discussion->getCommunaute()->getNom()).'">
                                            <input type="hidden" name="idPublication" value="'.htmlspecialchars($discussion->getIdPublication()).'">
                                            <input type="hidden" name="Favoris" value="1">';
                                        if ($discussion->estFavoris()){
                                            print '<svg onclick="this.closest(\'form\').submit();" class="svg_red pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m376-300 104-104 104 104 56-56-104-104 104-104-56-56-104 104-104-104-56 56 104 104-104 104zm-96 180q-33 0-56.5-23.5T200-200v-520h-40v-80h200v-40h240v40h200v80h-40v520q0 33-23.5 56.5T680-120zm400-600H280v520h400zm-400 0v520z"/>Favoris</svg>';
                                        }
                                        else{
                                            print '<svg onclick="this.closest(\'form\').submit();" class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="m480-120-58-52q-101-91-167-157T150-447.5 95.5-544 80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5 705-329 538-172zm0-108q96-86 158-147.5t98-107 50-81 14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81 98 107T480-228m0-273"/>Favoris</svg>';
                                        }
                                        print '</form>';
                                        
                                        if (isset($role) && $role->peutModerer()){
                                            print '
                                            <form method="POST" action="?action=communaute&nomCommu='.htmlspecialchars($discussion->getCommunaute()->getNom()).'">
                                                <input type="hidden" name="idPublication" value="'.htmlspecialchars($discussion->getIdPublication()).'">
                                                <input type="hidden" name="epinglerPublication" value="1">';
                                            if ($discussion->estEpingle()){
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
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-528 296-344l-56-56 240-240 240 240-56 56z"/></svg>
                                        </a>
                                        <span class="score-value">'.htmlspecialchars($discussion->getScore()).'</span>
                                        <a href="./?action=connexion">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M480-344 240-584l56-56 184 184 184-184 56 56z"/></svg>
                                        </a>
                                        <a href="./?action=connexion">
                                            <svg class="svg_white pointer" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 -960 960 960"><path d="M240-400h480v-80H240zm0-120h480v-80H240zm0-120h480v-80H240zM880-80 720-240H160q-33 0-56.5-23.5T80-320v-480q0-33 23.5-56.5T160-880h640q33 0 56.5 23.5T880-800zM160-320h594l46 45v-525H160zm0 0v-480z"/></svg>
                                        </a>
                                        </span>
                                        <br>';
                                    }?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune discussion pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

    </main>
    <script src="../../public/scripts/creer_commu.js"></script>
    <script src="../../public/scripts/vote_publication.js"> </script>
</body>
</html>