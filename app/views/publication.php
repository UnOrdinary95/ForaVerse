
<!DOCTYPE html>
<htmls lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="icon" href="../../public/images/favicon/favicon_foraverse.png"/>
    <link rel="stylesheet" href="../../public/styles/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../../public/scripts/lien_to_pressepapier.js"></script>
</head>
<body>
    <div style="display:flex; min-height: 100vh;">
        <div style="width: 80vw; border: 3px solid black;">
            <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
            <img src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 50px; height: 50px; border-radius: 50%">
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <img src="../../public/<?= htmlspecialchars($publication->getUtilisateur()->getCheminPhoto()) ?>" alt="ProfilAuteurDiscussion" style="width: 50px; height: 50px; border-radius: 50%">
            <div>
                <a href="./?action=profil&utilisateur=<?= htmlspecialchars($publication->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none;">
                    <p><?= htmlspecialchars($publication->getUtilisateur()->getPseudo()) ?></p>
                </a>
                <span style="color: orange; font-weight: bold;">{Auteur}</span>
            </div>
            <h2><?= htmlspecialchars($publication->getTitre()) ?></h2>
            <p>Publiée le <?= (new DateTime($publication->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($publication->getDateCreation()))->format('H:i') ?></p>
            
            <hr>

            <p><?= nl2br(htmlspecialchars($publication->getContenu()))?></p>

            <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container" data-publication-id=<?=$publication->getIdPublication()?>>
                <?php if(isset($_SESSION['Pseudo'])){
                    switch($publication->getVoteUtilisateurCourant()){
                        case 1:
                            print '
                            <button class="vote-up active">⬆️</button>
                            <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                            <button class="vote-down">⬇️</button>';
                            break;
                        case -1:
                            print '
                            <button class="vote-up">⬆️</button>
                            <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                            <button class="vote-down">⬇️</button>;';
                            break;
                        default:
                            print '
                            <button class="vote-up">⬆️</button>
                            <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                            <button class="vote-down">⬇️</button>';
                    }
                    print '
                    <button>🗨️Commentaire</button>
                    <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                        <input type="hidden" name="idPublication" value="'.htmlspecialchars($publication->getIdPublication()).'">';
                    if ($publication->estFavoris()){
                        print '<button type="submit" name="Favoris">❌Favoris</button>';
                    }
                    else{
                        print '<button type="submit" name="Favoris">⭐Favoris</button>';
                    }
                    print '</form>';
                    
                    if (isset($role) && $role->peutModerer()){
                        print '
                        <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                            <input type="hidden" name="idPublication" value="'.htmlspecialchars($publication->getIdPublication()).'">';
                        if ($publication->estEpingle()){
                            print '<button type="submit" name="epinglerPublication">❌Désépingler</button>';
                        }
                        else{
                            print '<button type="submit" name="epinglerPublication">📌Epingler</button>';
                        }
                        print '</form>';
                    }

                    print '<button onclick="partagerURL()">Partager la publication</button>';
                }
                else{
                    print '
                    <a href="./?action=connexion"><button>⬆️</button></a>
                    <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                    <a href="./?action=connexion"><button>⬇️</button></a>
                    <a href="./?action=connexion"><button>🗨️Commentaire</button></a>
                    <button onclick="partagerURL()">Partager la publication</button>';
                }?>
            </span>
        </div>

        <div style="width: 20vw;border: 3px solid black;">
            <img id="communauteImage" src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 75px; height: 75px; border-radius: 50%; cursor: pointer;">

            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <p id="description"><?= nl2br(htmlspecialchars($communaute->getDescription())) ?></p>
            <p>Visibilité : <?= $communaute->getVisibilite() == true ? "Publique" : "Privée" ?></p>
            <p id="compteurMembres"><?= htmlspecialchars($nbr_membres) . " Membres"?></p>

            <h2>Propriétaire</h2>
            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($proprio['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                <img src="../../public/<?= htmlspecialchars($proprio['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                <span style="font-size: 18px;"><?= htmlspecialchars($proprio['pseudo']) ?></span>
            </a>

            <h2>Modérateurs</h2>
            <div>
                <?php if(isset($mods) && count($mods) > 0): ?>
                    <?php foreach($mods as $mod): ?>
                        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($mod['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                            <img src="../../public/<?= htmlspecialchars($mod['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                            <span style="font-size: 18px;"><?= htmlspecialchars($mod['pseudo']) ?></span>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun modérateur pour le moment.</p>
                <?php endif; ?>
            </div>

            <h2>Membres</h2>
            <div>
                <?php if(isset($membres) && count($membres) > 0): ?>
                    <?php foreach($membres as $membre): ?>
                        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($membre['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                            <img src="../../public/<?= htmlspecialchars($membre['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                            <span style="font-size: 18px;"><?= htmlspecialchars($membre['pseudo']) ?></span>
                            <?php if($membre['admin']): ?>
                                <span style="color: red; font-weight: bold;">{Admin}</span>
                            <?php endif; ?>
                            <?php if($membre['banglobal']): ?>
                                <span style="color: green; font-weight: bold;">{Ban global}</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun membre pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../../public/scripts/communaute_discussion.js"></script>
    <script src="../../public/scripts/vote_publication.js"> </script>
</body>
</html>