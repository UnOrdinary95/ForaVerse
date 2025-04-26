
<!DOCTYPE html>
<html lang="fr">
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
            <div>
                <div style="display: flex; align-items: center;">
                    <h1><a href="./" style="text-decoration: none; width: 100px">⬅️</a></h1>
                    <img src="../../public/<?= htmlspecialchars($publication->getUtilisateur()->getCheminPhoto()) ?>" alt="ProfilAuteurDiscussion" style="width: 50px; height: 50px; border-radius: 50%">
                    <div style="display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: center; gap: 5px;"> 
                            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($publication->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none; padding: 0; margin: 0; display: block;">
                                <h2 style="margin: 0;"><?= htmlspecialchars($publication->getUtilisateur()->getPseudo()) ?></h2>
                            </a>
                            <span style="color: orange; font-weight: bold;">{Auteur}</span>
                        </div>   
                        <p style="margin: 0;"> le <?= (new DateTime($publication->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($publication->getDateCreation()))->format('H:i') ?></p>
                    </div>
                </div>
            </div>
            <h2 style="margin: 0;"><?= htmlspecialchars($publication->getTitre()) ?></h2>
            <p><?= nl2br(htmlspecialchars($publication->getContenu()))?></p>
            <br>
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
                            <button class="vote-down">⬇️</button>';
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
                    print '</span>
            <br>
            <form method="POST" style="display: flex; align-items: flex-end;" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                <img src="../../public/'.htmlspecialchars($session_user->getCheminPhoto()).'" alt="ProfilCommentaire" style="width: 50px; height: 50px; border-radius: 50%">
                <textarea name="contenuCommentaire" style="resize: vertical; border-radius: 30px; width: 80%; min-height: 40px; height: 40px; padding: 10px 20px;" placeholder="Commentaire" ></textarea>
                <div><button type="submit" name="CreerCommentaire">Commenter</button></div>
            </form>';
                }
                else{
                    print '
                    <a href="./?action=connexion"><button>⬆️</button></a>
                    <span class="score-value">'.htmlspecialchars($publication->getScore()).'</span>
                    <a href="./?action=connexion"><button>⬇️</button></a>
                    <a href="./?action=connexion"><button>🗨️Commentaire</button></a>
                    <button onclick="partagerURL()">Partager la publication</button>
                    </span>
                    <br>';
                }?>
            <hr>

            <div style="display: flex; align-items: center; gap: 10px;">
                <p>Trié par :</p>
                <div>
                    <form method="POST" action="?action=publication&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>&idPublication=<?= htmlspecialchars($publication->getIdPublication()) ?>">
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
                </div>
                <form method="POST" action="?action=publication&nomCommu=<?= htmlspecialchars($communaute->getNom()) ?>&idPublication=<?= htmlspecialchars($publication->getIdPublication()) ?>">
                    <input name="commentaire_mot_cle" type="text" placeholder="🔍Rechercher un commentaire" style="width: 40vw; border-radius: 30px; min-height: 15px; height: 15px; padding: 10px 20px;">
                </form>
            </div>

            <div id="commentaireContainer">
                <?php if (isset($commentaires) && count($commentaires) > 0): ?>
                    <?php foreach ($commentaires as $commentaire): ?>
                        <div class="commentaire">
                            <div>
                                <div style="display: flex; align-items: center;">
                                    <img src="../../public/<?= htmlspecialchars($commentaire->getUtilisateur()->getCheminPhoto()) ?>" alt="ProfilCommentaire" style="width: 50px; height: 50px; border-radius: 50%">
                                    <div style="display: flex; flex-direction: column;">
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($commentaire->getUtilisateur()->getPseudo()) ?>" style="text-decoration: none; padding: 0; margin: 0; display: block;">
                                                <h2 style="margin: 0;"><?= htmlspecialchars($commentaire->getUtilisateur()->getPseudo()) ?></h2>
                                            </a>
                                            
                                            <?php if($commentaire->getUtilisateur()->estAdministrateur()): ?>
                                                <span style="color: red; font-weight: bold;">{Admin}</span>
                                            <?php elseif($commentaire->getRoleUtilisateur()->estProprietaire()): ?>
                                                <span style="color: purple; font-weight: bold;">{Propriétaire}</span>
                                            <?php elseif($commentaire->getRoleUtilisateur()->estModerateur()): ?>
                                                <span style="color: blue; font-weight: bold;">{Modérateur}</span>
                                            <?php elseif ($commentaire->getIdUtilisateur() == $publication->getIdUtilisateur()): ?>
                                                <span style="color: orange; font-weight: bold;">{Auteur}</span>
                                            <?php else: ?>
                                                <span style="color: gray; font-weight: bold;">{Membre}</span>
                                            <?php endif;?>

                                            <?php if($commentaire->estEpingle()): ?>
                                                <span>📌</span>
                                            <?php endif; ?>
                                        </div>
                                        <p style="margin: 0;">le <?= (new DateTime($commentaire->getDateCreation()))->format('d/m/Y')?> à <?= (new DateTime($commentaire->getDateCreation()))->format('H:i') ?></p>
                                    </div>
                                </div>
                            </div>
                                
                            <p><?= nl2br(htmlspecialchars($commentaire->getContenu()))?></p>

                            <span style="display:flex; flex-direction: flex-row; gap: 10px;" class="vote-container" data-publication-id=<?=$commentaire->getIdPublication()?>>
                                <?php if(isset($_SESSION['Pseudo'])){
                                    switch($commentaire->getVoteUtilisateurCourant()){
                                        case 1:
                                            print '
                                            <button class="vote-up active">⬆️</button>
                                            <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                            <button class="vote-down">⬇️</button>';
                                            break;
                                        case -1:
                                            print '
                                            <button class="vote-up">⬆️</button>
                                            <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                            <button class="vote-down active">⬇️</button>';
                                            break;
                                        default:
                                            print '
                                            <button class="vote-up">⬆️</button>
                                            <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                            <button class="vote-down">⬇️</button>';
                                    }
                                    print '
                                    <button>🗨️Commentaire</button>
                                    <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                                        <input type="hidden" name="idPublication" value="'.htmlspecialchars($commentaire->getIdPublication()).'">';
                                    if ($commentaire->estFavoris()){
                                        print '<button type="submit" name="Favoris">❌Favoris</button>';
                                    }
                                    else{
                                        print '<button type="submit" name="Favoris">⭐Favoris</button>';
                                    }
                                    print '</form>';
                                    
                                    if (isset($role) && $role->peutModerer()){
                                        print '
                                        <form method="POST" action="?action=publication&nomCommu='.htmlspecialchars($communaute->getNom()).'&idPublication='.htmlspecialchars($publication->getIdPublication()).'">
                                        <input type="hidden" name="idPublication" value="'.htmlspecialchars($commentaire->getIdPublication()).'">';
                                        if ($commentaire->estEpingle()){
                                            print '<button type="submit" name="epinglerPublication">❌Désépingler</button>';
                                        }
                                        else{
                                            print '<button type="submit" name="epinglerPublication">📌Epingler</button>';
                                        }
                                        print '</form>';
                                    }
                                }
                                else{
                                    print '
                                    <a href="./?action=connexion"><button>⬆️</button></a>
                                    <span class="score-value">'.htmlspecialchars($commentaire->getScore()).'</span>
                                    <a href="./?action=connexion"><button>⬇️</button></a>
                                    <a href="./?action=connexion"><button>🗨️Commentaire</button></a>';
                                }?>
                            </span>
                            <!-- <hr> -->
                            <br>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun commentaire pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <aside style="width: 20vw;border: 3px solid black; height: 100vh; overflow-y: auto; position: sticky; top: 0;">
            <img id="communauteImage" src="../../public/<?= htmlspecialchars($communaute->getCheminPhoto()) ?>" alt="ProfilCommunaute" style="width: 75px; height: 75px; border-radius: 50%; cursor: pointer;">
            
            <h1><?= htmlspecialchars($communaute->getNom()) ?></h1>
            <p id="description"><?= nl2br(htmlspecialchars($communaute->getDescription())) ?></p>
            <p>Visibilité : <?= $communaute->getVisibilite() == true ? "Publique" : "Privée" ?></p>
            <p id="compteurMembres"><?= htmlspecialchars($nbr_membres) . " Membres"?></p>
            <?php for($i = 0; $i < 100; $i++): ?>
            <h2>Propriétaire</h2>
            <a href="./?action=profil&utilisateur=<?= htmlspecialchars($proprio['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                <img src="../../public/<?= htmlspecialchars($proprio['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                <span style="font-size: 18px;<?php if(isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] == $proprio['pseudo']){print 'font-weight: bold;';} ?>"><?= htmlspecialchars($proprio['pseudo']) ?></span>
            </a>
            <?php endfor; ?>

            <h2>Modérateurs</h2>
            <div>
                <?php if(isset($mods) && count($mods) > 0): ?>
                    <?php foreach($mods as $mod): ?>
                        <a href="./?action=profil&utilisateur=<?= htmlspecialchars($mod['pseudo']) ?>" style="text-decoration: none; display:flex; align-items: center; gap: 3px;">
                            <img src="../../public/<?= htmlspecialchars($mod['pp'])?>" style="width: 40px; height: 40px; border-radius: 30%;" alt="Profil">
                            <span style="font-size: 18px;<?php if(isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] == $mod['pseudo']){print 'font-weight: bold;';} ?>"><?= htmlspecialchars($mod['pseudo']) ?></span>
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
                            <span style="font-size: 18px;<?php if(isset($_SESSION['Pseudo']) && $_SESSION['Pseudo'] == $membre['pseudo']){print 'font-weight: bold;';} ?>"><?= htmlspecialchars($membre['pseudo']) ?></span>
                            <?php if($membre['admin']): ?>
                                <span style="color: red; font-weight: bold;">{Admin}</span>
                            <?php endif; ?>
                            <?php if($membre['banglobal']): ?>
                                <span style="color: green; font-weight: bold;">{Utilisateur banni}</span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun membre pour le moment.</p>
                <?php endif; ?>
            </div>
        </aside>
    </div>
    <script src="../../public/scripts/communaute_discussion.js"></script>
    <script src="../../public/scripts/vote_publication.js"> </script>
</body>
</html>